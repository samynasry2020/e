<?php

declare(strict_types=1);

namespace GovTribe\Services;

use GovTribe\Models\Opportunity;
use GovTribe\Utils\Database;
use GovTribe\Utils\Logger;
use DateTime;

/**
 * Scoring Service - Implements opportunity scoring algorithm per PRD
 */
class ScoringService
{
    private Logger $logger;
    private array $settings;

    public function __construct(array $settings = [])
    {
        $this->logger = Logger::getInstance();
        $this->settings = $settings;
    }

    /**
     * Score opportunity based on multiple criteria
     */
    public function scoreOpportunity(array $opportunity): array
    {
        $score = 0;
        $reasons = [];

        // +30 NAICS match (any in allowlist)
        $naicsScore = $this->calculateNaicsScore($opportunity['id']);
        $score += $naicsScore['points'];
        if ($naicsScore['points'] > 0) {
            $reasons[] = $naicsScore['reason'];
        }

        // +25 set-aside match (WOSB/EDWOSB when enabled)
        $setAsideScore = $this->calculateSetAsideScore($opportunity);
        $score += $setAsideScore['points'];
        if ($setAsideScore['points'] > 0) {
            $reasons[] = $setAsideScore['reason'];
        }

        // +20 preferred agency (Admin list)
        $agencyScore = $this->calculateAgencyScore($opportunity);
        $score += $agencyScore['points'];
        if ($agencyScore['points'] > 0) {
            $reasons[] = $agencyScore['reason'];
        }

        // +10 due date ≥ 7 days away; −20 if ≤ 3 days
        $dueDateScore = $this->calculateDueDateScore($opportunity);
        $score += $dueDateScore['points'];
        if ($dueDateScore['points'] !== 0) {
            $reasons[] = $dueDateScore['reason'];
        }

        // Clamp to 0-100 range
        $score = max(0, min(100, $score));

        return [
            'score' => $score,
            'reasons' => $reasons,
            'breakdown' => [
                'naics' => $naicsScore,
                'set_aside' => $setAsideScore,
                'agency' => $agencyScore,
                'due_date' => $dueDateScore
            ]
        ];
    }

    /**
     * Calculate NAICS code score
     */
    private function calculateNaicsScore(int $opportunityId): array
    {
        $allowedNaics = $this->settings['allowed_naics_codes'] ?? ['334111', '541512'];
        
        $sql = "SELECT naics_code FROM opportunity_naics WHERE opportunity_id = :opportunity_id";
        $naicsCodes = Database::fetchAll($sql, ['opportunity_id' => $opportunityId]);
        
        foreach ($naicsCodes as $row) {
            if (in_array($row['naics_code'], $allowedNaics, true)) {
                return [
                    'points' => 30,
                    'reason' => "NAICS match: {$row['naics_code']} (+30)"
                ];
            }
        }

        return [
            'points' => 0,
            'reason' => 'No matching NAICS codes'
        ];
    }

    /**
     * Calculate set-aside score
     */
    private function calculateSetAsideScore(array $opportunity): array
    {
        $setAside = $opportunity['set_aside'] ?? '';
        
        if (empty($setAside)) {
            return [
                'points' => 0,
                'reason' => 'No set-aside designation'
            ];
        }

        $preferredSetAsides = $this->settings['preferred_set_aside_codes'] ?? ['WOSB', 'EDWOSB'];
        
        if (in_array($setAside, $preferredSetAsides, true)) {
            return [
                'points' => 25,
                'reason' => "Set-aside match: {$setAside} (+25)"
            ];
        }

        return [
            'points' => 0,
            'reason' => "Set-aside not preferred: {$setAside}"
        ];
    }

    /**
     * Calculate agency score
     */
    private function calculateAgencyScore(array $opportunity): array
    {
        $agencyId = $opportunity['agency_id'] ?? null;
        
        if (!$agencyId) {
            return [
                'points' => 0,
                'reason' => 'No agency information'
            ];
        }

        $preferredAgencies = $this->settings['preferred_agencies'] ?? [];
        
        if (in_array($agencyId, $preferredAgencies, true)) {
            $agencyName = $this->getAgencyName($agencyId);
            return [
                'points' => 20,
                'reason' => "Preferred agency: {$agencyName} (+20)"
            ];
        }

        return [
            'points' => 0,
            'reason' => 'Agency not in preferred list'
        ];
    }

    /**
     * Calculate due date score
     */
    private function calculateDueDateScore(array $opportunity): array
    {
        $dueAt = $opportunity['due_at'] ?? null;
        
        if (!$dueAt) {
            return [
                'points' => 0,
                'reason' => 'No due date specified'
            ];
        }

        try {
            $dueDate = new DateTime($dueAt);
            $now = new DateTime();
            $daysUntilDue = $now->diff($dueDate)->days;
            
            // Adjust days based on direction
            if ($dueDate < $now) {
                $daysUntilDue = -$daysUntilDue;
            }

            if ($daysUntilDue >= 7) {
                return [
                    'points' => 10,
                    'reason' => "Due date ≥ 7 days away ({$daysUntilDue} days) (+10)"
                ];
            } elseif ($daysUntilDue <= 3) {
                return [
                    'points' => -20,
                    'reason' => "Due date ≤ 3 days away ({$daysUntilDue} days) (-20)"
                ];
            } else {
                return [
                    'points' => 0,
                    'reason' => "Due date in 4-6 days ({$daysUntilDue} days)"
                ];
            }

        } catch (\Exception $e) {
            $this->logger->warning('Invalid due date format', [
                'opportunity_id' => $opportunity['id'],
                'due_at' => $dueAt,
                'error' => $e->getMessage()
            ]);

            return [
                'points' => 0,
                'reason' => 'Invalid due date format'
            ];
        }
    }

    /**
     * Get agency name by ID
     */
    private function getAgencyName(int $agencyId): string
    {
        $sql = "SELECT name FROM agencies WHERE id = :id LIMIT 1";
        $result = Database::fetchOne($sql, ['id' => $agencyId]);
        
        return $result['name'] ?? 'Unknown Agency';
    }

    /**
     * Score all unscored opportunities
     */
    public function scoreAllUnscored(): array
    {
        $sql = "SELECT * FROM opportunities WHERE score = 0 AND active = 1";
        $opportunities = Database::fetchAll($sql);
        
        $results = [
            'scored' => 0,
            'errors' => []
        ];

        foreach ($opportunities as $opportunity) {
            try {
                $this->updateOpportunityScore($opportunity['id']);
                $results['scored']++;
            } catch (\Exception $e) {
                $results['errors'][] = "Failed to score opportunity {$opportunity['id']}: " . $e->getMessage();
                $this->logger->error('Failed to score opportunity', [
                    'opportunity_id' => $opportunity['id'],
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->logger->info('Bulk scoring completed', $results);
        return $results;
    }

    /**
     * Update opportunity score in database
     */
    public function updateOpportunityScore(int $opportunityId): bool
    {
        $sql = "SELECT * FROM opportunities WHERE id = :id LIMIT 1";
        $opportunity = Database::fetchOne($sql, ['id' => $opportunityId]);
        
        if (!$opportunity) {
            throw new RuntimeException("Opportunity not found: {$opportunityId}");
        }

        $scoringResult = $this->scoreOpportunity($opportunity);
        
        $updateSql = "UPDATE opportunities SET 
            score = :score,
            score_reasons = :score_reasons,
            updated_at = NOW()
            WHERE id = :id";

        $success = Database::execute($updateSql, [
            'id' => $opportunityId,
            'score' => $scoringResult['score'],
            'score_reasons' => implode('; ', $scoringResult['reasons'])
        ]);

        if ($success > 0) {
            $this->logger->info('Opportunity scored', [
                'opportunity_id' => $opportunityId,
                'score' => $scoringResult['score'],
                'reasons' => $scoringResult['reasons']
            ]);
        }

        return $success > 0;
    }

    /**
     * Get scoring statistics
     */
    public function getScoringStats(): array
    {
        $stats = [];

        // Score distribution
        $scoreDistribution = Database::fetchAll(
            "SELECT 
                CASE 
                    WHEN score >= 80 THEN 'High (80-100)'
                    WHEN score >= 60 THEN 'Medium (60-79)'
                    WHEN score >= 40 THEN 'Low (40-59)'
                    WHEN score > 0 THEN 'Very Low (1-39)'
                    ELSE 'Unscored (0)'
                END as score_range,
                COUNT(*) as count
             FROM opportunities 
             WHERE active = 1
             GROUP BY score_range
             ORDER BY count DESC"
        );

        $stats['score_distribution'] = $scoreDistribution;

        // Average score by status
        $avgByStatus = Database::fetchAll(
            "SELECT status, AVG(score) as avg_score, COUNT(*) as count
             FROM opportunities 
             WHERE active = 1 AND score > 0
             GROUP BY status
             ORDER BY avg_score DESC"
        );

        $stats['average_by_status'] = $avgByStatus;

        // Top scoring opportunities
        $topOpportunities = Database::fetchAll(
            "SELECT o.id, o.title, o.score, a.name as agency_name, o.due_at
             FROM opportunities o
             LEFT JOIN agencies a ON o.agency_id = a.id
             WHERE o.active = 1 AND o.score > 0
             ORDER BY o.score DESC, o.due_at ASC
             LIMIT 10"
        );

        $stats['top_opportunities'] = $topOpportunities;

        // Opportunities due soon with high scores
        $dueSoonHighScore = Database::fetchAll(
            "SELECT o.id, o.title, o.score, a.name as agency_name, o.due_at
             FROM opportunities o
             LEFT JOIN agencies a ON o.agency_id = a.id
             WHERE o.active = 1 
             AND o.score >= 70
             AND o.due_at BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)
             ORDER BY o.score DESC, o.due_at ASC
             LIMIT 10"
        );

        $stats['due_soon_high_score'] = $dueSoonHighScore;

        return $stats;
    }

    /**
     * Recalculate scores for all opportunities (force refresh)
     */
    public function recalculateAllScores(): array
    {
        $sql = "SELECT id FROM opportunities WHERE active = 1";
        $opportunities = Database::fetchAll($sql);
        
        $results = [
            'recalculated' => 0,
            'errors' => []
        ];

        foreach ($opportunities as $opportunity) {
            try {
                $this->updateOpportunityScore($opportunity['id']);
                $results['recalculated']++;
            } catch (\Exception $e) {
                $results['errors'][] = "Failed to recalculate opportunity {$opportunity['id']}: " . $e->getMessage();
                $this->logger->error('Failed to recalculate opportunity score', [
                    'opportunity_id' => $opportunity['id'],
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->logger->info('Bulk score recalculation completed', $results);
        return $results;
    }

    /**
     * Get opportunities by score range
     */
    public function getOpportunitiesByScoreRange(int $minScore, int $maxScore, int $limit = 50): array
    {
        $sql = "SELECT o.*, a.name as agency_name 
                FROM opportunities o
                LEFT JOIN agencies a ON o.agency_id = a.id
                WHERE o.active = 1 
                AND o.score >= :min_score 
                AND o.score <= :max_score
                ORDER BY o.score DESC, o.due_at ASC
                LIMIT :limit";

        return Database::fetchAll($sql, [
            'min_score' => $minScore,
            'max_score' => $maxScore,
            'limit' => $limit
        ]);
    }

    /**
     * Get high-priority opportunities (score >= 70)
     */
    public function getHighPriorityOpportunities(int $limit = 20): array
    {
        return $this->getOpportunitiesByScoreRange(70, 100, $limit);
    }

    /**
     * Get opportunities needing attention (due soon with decent scores)
     */
    public function getOpportunitiesNeedingAttention(int $limit = 20): array
    {
        $sql = "SELECT o.*, a.name as agency_name 
                FROM opportunities o
                LEFT JOIN agencies a ON o.agency_id = a.id
                WHERE o.active = 1 
                AND o.score >= 50
                AND o.due_at BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 3 DAY)
                ORDER BY o.due_at ASC, o.score DESC
                LIMIT :limit";

        return Database::fetchAll($sql, ['limit' => $limit]);
    }
}