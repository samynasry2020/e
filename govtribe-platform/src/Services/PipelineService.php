<?php

declare(strict_types=1);

namespace GovTribe\Services;

use GovTribe\Models\Opportunity;
use GovTribe\Utils\Database;
use GovTribe\Utils\Logger;
use GovTribe\Utils\Security;

/**
 * Pipeline Service - Manages opportunity pipeline and Kanban board
 */
class PipelineService
{
    private Logger $logger;
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->logger = Logger::getInstance();
        $this->authService = $authService;
    }

    /**
     * Get pipeline statistics
     */
    public function getPipelineStats(): array
    {
        $sql = "SELECT 
                    status,
                    COUNT(*) as count,
                    AVG(score) as avg_score,
                    SUM(CASE WHEN score >= 70 THEN 1 ELSE 0 END) as high_score_count
                FROM opportunities 
                WHERE active = 1 
                GROUP BY status
                ORDER BY FIELD(status, 'New', 'Review', 'Pursue', 'No-Bid', 'Awarded', 'Lost')";

        $statusStats = Database::fetchAll($sql);

        // Get total counts
        $totalStats = Database::fetchOne(
            "SELECT 
                COUNT(*) as total_opportunities,
                AVG(score) as overall_avg_score,
                SUM(CASE WHEN score >= 70 THEN 1 ELSE 0 END) as total_high_score,
                SUM(CASE WHEN due_at BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as due_soon
             FROM opportunities 
             WHERE active = 1"
        );

        return [
            'status_breakdown' => $statusStats,
            'totals' => $totalStats
        ];
    }

    /**
     * Get opportunities by status
     */
    public function getOpportunitiesByStatus(string $status, int $limit = 50): array
    {
        $sql = "SELECT o.*, a.name as agency_name,
                       DATEDIFF(o.due_at, NOW()) as days_until_due
                FROM opportunities o
                LEFT JOIN agencies a ON o.agency_id = a.id
                WHERE o.active = 1 AND o.status = :status
                ORDER BY 
                    CASE WHEN o.due_at IS NULL THEN 1 ELSE 0 END,
                    o.due_at ASC,
                    o.score DESC,
                    o.created_at DESC
                LIMIT :limit";

        return Database::fetchAll($sql, ['status' => $status, 'limit' => $limit]);
    }

    /**
     * Update opportunity status (with audit trail)
     */
    public function updateOpportunityStatus(int $opportunityId, string $newStatus, string $notes = ''): bool
    {
        $user = $this->authService->getCurrentUser();
        if (!$user) {
            throw new RuntimeException('User not authenticated');
        }

        // Validate status
        $validStatuses = ['New', 'Review', 'Pursue', 'No-Bid', 'Awarded', 'Lost'];
        if (!in_array($newStatus, $validStatuses, true)) {
            throw new RuntimeException("Invalid status: {$newStatus}");
        }

        // Get current opportunity
        $sql = "SELECT * FROM opportunities WHERE id = :id LIMIT 1";
        $opportunity = Database::fetchOne($sql, ['id' => $opportunityId]);

        if (!$opportunity) {
            throw new RuntimeException("Opportunity not found: {$opportunityId}");
        }

        $oldStatus = $opportunity['status'];

        // Update status
        Database::beginTransaction();

        try {
            $updateSql = "UPDATE opportunities SET 
                status = :status,
                updated_at = NOW()
                WHERE id = :id";

            $updated = Database::execute($updateSql, [
                'id' => $opportunityId,
                'status' => $newStatus
            ]);

            if ($updated === 0) {
                throw new RuntimeException('Failed to update opportunity status');
            }

            // Log status change in audit trail
            $this->logStatusChange($opportunityId, $oldStatus, $newStatus, $notes, $user->id);

            Database::commit();

            $this->logger->info('Opportunity status updated', [
                'opportunity_id' => $opportunityId,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'user_id' => $user->id,
                'notes' => $notes
            ]);

            return true;

        } catch (\Exception $e) {
            Database::rollback();
            throw $e;
        }
    }

    /**
     * Bulk update opportunity statuses
     */
    public function bulkUpdateStatus(array $opportunityIds, string $newStatus, string $notes = ''): array
    {
        $results = [
            'updated' => 0,
            'errors' => []
        ];

        foreach ($opportunityIds as $opportunityId) {
            try {
                $this->updateOpportunityStatus((int) $opportunityId, $newStatus, $notes);
                $results['updated']++;
            } catch (\Exception $e) {
                $results['errors'][] = "Failed to update opportunity {$opportunityId}: " . $e->getMessage();
            }
        }

        return $results;
    }

    /**
     * Get pipeline analytics
     */
    public function getPipelineAnalytics(): array
    {
        $analytics = [];

        // Win/Loss ratio
        $winLossStats = Database::fetchOne(
            "SELECT 
                SUM(CASE WHEN status = 'Awarded' THEN 1 ELSE 0 END) as won,
                SUM(CASE WHEN status = 'Lost' THEN 1 ELSE 0 END) as lost,
                SUM(CASE WHEN status IN ('Awarded', 'Lost') THEN 1 ELSE 0 END) as total_decided
             FROM opportunities 
             WHERE active = 1"
        );

        $analytics['win_loss'] = $winLossStats;
        if ($winLossStats['total_decided'] > 0) {
            $analytics['win_loss']['win_rate'] = round(($winLossStats['won'] / $winLossStats['total_decided']) * 100, 2);
        } else {
            $analytics['win_loss']['win_rate'] = 0;
        }

        // Average cycle time by status
        $cycleTimeStats = Database::fetchAll(
            "SELECT 
                status,
                AVG(DATEDIFF(updated_at, created_at)) as avg_cycle_days,
                COUNT(*) as count
             FROM opportunities 
             WHERE active = 1 
             AND status IN ('Review', 'Pursue', 'No-Bid', 'Awarded', 'Lost')
             GROUP BY status"
        );

        $analytics['cycle_time'] = $cycleTimeStats;

        // Monthly pipeline trends
        $monthlyTrends = Database::fetchAll(
            "SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                status,
                COUNT(*) as count
             FROM opportunities 
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
             GROUP BY month, status
             ORDER BY month ASC, status"
        );

        $analytics['monthly_trends'] = $monthlyTrends;

        // Score distribution by status
        $scoreDistribution = Database::fetchAll(
            "SELECT 
                status,
                AVG(score) as avg_score,
                MIN(score) as min_score,
                MAX(score) as max_score,
                COUNT(*) as count
             FROM opportunities 
             WHERE active = 1 AND score > 0
             GROUP BY status
             ORDER BY avg_score DESC"
        );

        $analytics['score_distribution'] = $scoreDistribution;

        return $analytics;
    }

    /**
     * Get overdue opportunities
     */
    public function getOverdueOpportunities(int $limit = 50): array
    {
        $sql = "SELECT o.*, a.name as agency_name,
                       DATEDIFF(NOW(), o.due_at) as days_overdue
                FROM opportunities o
                LEFT JOIN agencies a ON o.agency_id = a.id
                WHERE o.active = 1 
                AND o.due_at < NOW()
                AND o.status NOT IN ('No-Bid', 'Awarded', 'Lost')
                ORDER BY o.due_at ASC
                LIMIT :limit";

        return Database::fetchAll($sql, ['limit' => $limit]);
    }

    /**
     * Get opportunities due soon
     */
    public function getOpportunitiesDueSoon(int $days = 7, int $limit = 50): array
    {
        $sql = "SELECT o.*, a.name as agency_name,
                       DATEDIFF(o.due_at, NOW()) as days_until_due
                FROM opportunities o
                LEFT JOIN agencies a ON o.agency_id = a.id
                WHERE o.active = 1 
                AND o.due_at BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL :days DAY)
                AND o.status NOT IN ('No-Bid', 'Awarded', 'Lost')
                ORDER BY o.due_at ASC, o.score DESC
                LIMIT :limit";

        return Database::fetchAll($sql, ['days' => $days, 'limit' => $limit]);
    }

    /**
     * Get opportunities by score threshold
     */
    public function getOpportunitiesByScoreThreshold(int $minScore, int $limit = 50): array
    {
        $sql = "SELECT o.*, a.name as agency_name
                FROM opportunities o
                LEFT JOIN agencies a ON o.agency_id = a.id
                WHERE o.active = 1 
                AND o.score >= :min_score
                AND o.status NOT IN ('No-Bid', 'Awarded', 'Lost')
                ORDER BY o.score DESC, o.due_at ASC
                LIMIT :limit";

        return Database::fetchAll($sql, ['min_score' => $minScore, 'limit' => $limit]);
    }

    /**
     * Get pipeline summary for dashboard
     */
    public function getPipelineSummary(): array
    {
        $summary = [];

        // Current pipeline status
        $pipelineStats = $this->getPipelineStats();
        $summary['current_pipeline'] = $pipelineStats;

        // Urgent items (due within 3 days)
        $urgent = $this->getOpportunitiesDueSoon(3, 10);
        $summary['urgent_items'] = $urgent;

        // High-score opportunities
        $highScore = $this->getOpportunitiesByScoreThreshold(70, 10);
        $summary['high_score_opportunities'] = $highScore;

        // Recently updated
        $recentlyUpdated = Database::fetchAll(
            "SELECT o.*, a.name as agency_name
             FROM opportunities o
             LEFT JOIN agencies a ON o.agency_id = a.id
             WHERE o.active = 1
             ORDER BY o.updated_at DESC
             LIMIT 10"
        );
        $summary['recently_updated'] = $recentlyUpdated;

        return $summary;
    }

    /**
     * Log status change in audit trail
     */
    private function logStatusChange(int $opportunityId, string $oldStatus, string $newStatus, string $notes, int $userId): void
    {
        $sql = "INSERT INTO audit_log (user_id, action, entity, entity_id, meta, ip_address, created_at) 
                VALUES (:user_id, :action, :entity, :entity_id, :meta, :ip_address, NOW())";

        Database::execute($sql, [
            'user_id' => $userId,
            'action' => 'status_change',
            'entity' => 'opportunity',
            'entity_id' => $opportunityId,
            'meta' => json_encode([
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'notes' => $notes,
                'changed_at' => date('Y-m-d H:i:s')
            ]),
            'ip_address' => Security::getClientIp()
        ]);
    }

    /**
     * Get status change history for opportunity
     */
    public function getStatusChangeHistory(int $opportunityId): array
    {
        $sql = "SELECT al.*, u.email as user_email
                FROM audit_log al
                LEFT JOIN users u ON al.user_id = u.id
                WHERE al.entity = 'opportunity' 
                AND al.entity_id = :opportunity_id
                AND al.action = 'status_change'
                ORDER BY al.created_at DESC";

        return Database::fetchAll($sql, ['opportunity_id' => $opportunityId]);
    }

    /**
     * Get opportunities needing review
     */
    public function getOpportunitiesNeedingReview(int $limit = 50): array
    {
        $sql = "SELECT o.*, a.name as agency_name,
                       DATEDIFF(NOW(), o.created_at) as days_since_created
                FROM opportunities o
                LEFT JOIN agencies a ON o.agency_id = a.id
                WHERE o.active = 1 
                AND o.status = 'New'
                AND o.score >= 50
                ORDER BY o.score DESC, o.created_at ASC
                LIMIT :limit";

        return Database::fetchAll($sql, ['limit' => $limit]);
    }

    /**
     * Archive completed opportunities
     */
    public function archiveCompletedOpportunities(int $daysOld = 90): int
    {
        $sql = "UPDATE opportunities 
                SET active = 0, updated_at = NOW()
                WHERE status IN ('Awarded', 'Lost') 
                AND updated_at < DATE_SUB(NOW(), INTERVAL :days DAY)
                AND active = 1";

        $archived = Database::execute($sql, ['days' => $daysOld]);

        $this->logger->info('Archived completed opportunities', [
            'archived_count' => $archived,
            'days_old' => $daysOld
        ]);

        return $archived;
    }
}