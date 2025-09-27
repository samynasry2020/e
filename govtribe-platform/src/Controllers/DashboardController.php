<?php

declare(strict_types=1);

namespace GovTribe\Controllers;

use GovTribe\Models\Opportunity;
use GovTribe\Models\User;
use GovTribe\Models\Session;
use GovTribe\Utils\Database;

/**
 * Dashboard controller
 */
class DashboardController extends BaseController
{
    /**
     * Show dashboard
     */
    public function index(): void
    {
        $user = $this->requireAuth();
        
        // Get dashboard statistics
        $stats = $this->getDashboardStats();
        
        // Get recent opportunities
        $recentOpportunities = $this->getRecentOpportunities(10);
        
        // Get opportunities due soon
        $dueSoonOpportunities = $this->getDueSoonOpportunities(5);
        
        // Get pipeline data
        $pipelineData = $this->getPipelineData();
        
        // Get recent activity
        $recentActivity = $this->getRecentActivity(10);

        $this->view('dashboard/index', [
            'user' => $user,
            'stats' => $stats,
            'recent_opportunities' => $recentOpportunities,
            'due_soon_opportunities' => $dueSoonOpportunities,
            'pipeline_data' => $pipelineData,
            'recent_activity' => $recentActivity,
            'csrf_token' => $this->getCsrfToken()
        ]);
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats(): array
    {
        // New opportunities in last 24 hours
        $new24h = Database::fetchValue(
            "SELECT COUNT(*) FROM opportunities WHERE created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)"
        );

        // Opportunities due in next 7 days
        $due7d = Database::fetchValue(
            "SELECT COUNT(*) FROM opportunities WHERE due_at BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY) AND status NOT IN ('No-Bid', 'Awarded', 'Lost')"
        );

        // Total active opportunities
        $totalActive = Database::fetchValue(
            "SELECT COUNT(*) FROM opportunities WHERE active = 1 AND status NOT IN ('No-Bid', 'Awarded', 'Lost')"
        );

        // High-score opportunities (score >= 70)
        $highScore = Database::fetchValue(
            "SELECT COUNT(*) FROM opportunities WHERE score >= 70 AND active = 1 AND status NOT IN ('No-Bid', 'Awarded', 'Lost')"
        );

        // Pipeline breakdown
        $pipelineStats = Database::fetchAll(
            "SELECT status, COUNT(*) as count FROM opportunities WHERE active = 1 GROUP BY status"
        );

        $pipeline = [];
        foreach ($pipelineStats as $stat) {
            $pipeline[$stat['status']] = (int) $stat['count'];
        }

        // Recent errors (from audit log)
        $recentErrors = Database::fetchValue(
            "SELECT COUNT(*) FROM audit_log WHERE action = 'error' AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)"
        );

        return [
            'new_24h' => (int) $new24h,
            'due_7d' => (int) $due7d,
            'total_active' => (int) $totalActive,
            'high_score' => (int) $highScore,
            'pipeline' => $pipeline,
            'recent_errors' => (int) $recentErrors
        ];
    }

    /**
     * Get recent opportunities
     */
    private function getRecentOpportunities(int $limit): array
    {
        $sql = "SELECT o.*, a.name as agency_name 
                FROM opportunities o 
                LEFT JOIN agencies a ON o.agency_id = a.id 
                WHERE o.active = 1 
                ORDER BY o.created_at DESC 
                LIMIT :limit";
        
        return Database::fetchAll($sql, ['limit' => $limit]);
    }

    /**
     * Get opportunities due soon
     */
    private function getDueSoonOpportunities(int $limit): array
    {
        $sql = "SELECT o.*, a.name as agency_name 
                FROM opportunities o 
                LEFT JOIN agencies a ON o.agency_id = a.id 
                WHERE o.due_at BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY) 
                AND o.status NOT IN ('No-Bid', 'Awarded', 'Lost')
                AND o.active = 1 
                ORDER BY o.due_at ASC 
                LIMIT :limit";
        
        return Database::fetchAll($sql, ['limit' => $limit]);
    }

    /**
     * Get pipeline data for charts
     */
    private function getPipelineData(): array
    {
        // Status distribution
        $statusData = Database::fetchAll(
            "SELECT status, COUNT(*) as count FROM opportunities WHERE active = 1 GROUP BY status ORDER BY count DESC"
        );

        // Score distribution
        $scoreData = Database::fetchAll(
            "SELECT 
                CASE 
                    WHEN score >= 80 THEN 'High (80-100)'
                    WHEN score >= 60 THEN 'Medium (60-79)'
                    WHEN score >= 40 THEN 'Low (40-59)'
                    ELSE 'Very Low (0-39)'
                END as score_range,
                COUNT(*) as count
             FROM opportunities 
             WHERE active = 1 AND status NOT IN ('No-Bid', 'Awarded', 'Lost')
             GROUP BY score_range
             ORDER BY count DESC"
        );

        // Monthly trend (last 6 months)
        $trendData = Database::fetchAll(
            "SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as count
             FROM opportunities 
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
             GROUP BY month
             ORDER BY month ASC"
        );

        return [
            'status' => $statusData,
            'score_ranges' => $scoreData,
            'monthly_trend' => $trendData
        ];
    }

    /**
     * Get recent activity
     */
    private function getRecentActivity(int $limit): array
    {
        $sql = "SELECT 
                    al.*,
                    u.email as user_email,
                    o.title as opportunity_title
                FROM audit_log al
                LEFT JOIN users u ON al.user_id = u.id
                LEFT JOIN opportunities o ON al.entity = 'opportunity' AND al.entity_id = o.id
                ORDER BY al.created_at DESC
                LIMIT :limit";
        
        return Database::fetchAll($sql, ['limit' => $limit]);
    }

    /**
     * API endpoint for dashboard stats
     */
    public function apiStats(): void
    {
        $user = $this->requireAuth();
        
        $stats = $this->getDashboardStats();
        $pipelineData = $this->getPipelineData();
        
        $this->json([
            'stats' => $stats,
            'pipeline' => $pipelineData,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Quick actions for dashboard
     */
    public function quickAction(): void
    {
        $user = $this->requireAuth();
        $data = $this->getRequestData();
        
        $action = $data['action'] ?? '';
        
        switch ($action) {
            case 'sync_now':
                if (!$user->can('manage_ingestion')) {
                    $this->json(['error' => 'Permission denied'], 403);
                    return;
                }
                
                // Trigger manual sync (implement in SAM service)
                $this->json(['message' => 'Sync initiated']);
                break;
                
            case 'mark_reviewed':
                $opportunityId = (int)($data['opportunity_id'] ?? 0);
                if ($opportunityId > 0) {
                    $sql = "UPDATE opportunities SET status = 'Review' WHERE id = :id";
                    $updated = Database::execute($sql, ['id' => $opportunityId]);
                    
                    if ($updated > 0) {
                        // Log activity
                        $this->logActivity('opportunity_status_change', 'opportunity', $opportunityId, [
                            'new_status' => 'Review',
                            'old_status' => 'New'
                        ]);
                        
                        $this->json(['message' => 'Opportunity marked for review']);
                    } else {
                        $this->json(['error' => 'Opportunity not found'], 404);
                    }
                } else {
                    $this->json(['error' => 'Invalid opportunity ID'], 400);
                }
                break;
                
            default:
                $this->json(['error' => 'Invalid action'], 400);
        }
    }

    /**
     * Log activity for audit trail
     */
    private function logActivity(string $action, string $entity, ?int $entityId, array $meta = []): void
    {
        $sql = "INSERT INTO audit_log (user_id, action, entity, entity_id, meta, ip_address, created_at) 
                VALUES (:user_id, :action, :entity, :entity_id, :meta, :ip_address, NOW())";
        
        Database::execute($sql, [
            'user_id' => $this->getCurrentUser()->id,
            'action' => $action,
            'entity' => $entity,
            'entity_id' => $entityId,
            'meta' => json_encode($meta),
            'ip_address' => Security::getClientIp()
        ]);
    }
}