<?php

declare(strict_types=1);

namespace GovTribe\Models;

use GovTribe\Utils\Database;
use DateTime;

/**
 * Opportunity model
 */
class Opportunity extends BaseModel
{
    protected string $table = 'opportunities';
    protected string $primaryKey = 'id';
    
    protected array $fillable = [
        'source',
        'external_id',
        'title',
        'description',
        'description_status',
        'agency_id',
        'posted_at',
        'due_at',
        'notice_type',
        'set_aside',
        'ui_link',
        'score',
        'score_reasons',
        'status',
        'active'
    ];
    
    protected array $casts = [
        'id' => 'int',
        'agency_id' => 'int',
        'score' => 'int',
        'active' => 'bool',
        'posted_at' => 'datetime',
        'due_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get agency for this opportunity
     */
    public function getAgency(): ?Agency
    {
        if (!$this->agency_id) {
            return null;
        }

        return Agency::find($this->agency_id);
    }

    /**
     * Get NAICS codes for this opportunity
     */
    public function getNaicsCodes(): array
    {
        $sql = "SELECT naics_code FROM opportunity_naics WHERE opportunity_id = :id";
        $results = Database::fetchAll($sql, ['id' => $this->id]);
        
        return array_column($results, 'naics_code');
    }

    /**
     * Get documents/attachments for this opportunity
     */
    public function getDocuments(): array
    {
        $sql = "SELECT d.*, f.original_name, f.mime_type, f.size, f.path
                FROM documents d
                JOIN files f ON d.file_id = f.id
                WHERE d.opportunity_id = :id
                ORDER BY d.created_at ASC";
        
        return Database::fetchAll($sql, ['id' => $this->id]);
    }

    /**
     * Get contacts for this opportunity's agency
     */
    public function getContacts(): array
    {
        if (!$this->agency_id) {
            return [];
        }

        $sql = "SELECT * FROM contacts WHERE agency_id = :agency_id ORDER BY name ASC";
        return Database::fetchAll($sql, ['agency_id' => $this->agency_id]);
    }

    /**
     * Get change history for this opportunity
     */
    public function getChangeHistory(): array
    {
        $sql = "SELECT * FROM opportunity_changes WHERE opportunity_id = :id ORDER BY changed_at DESC";
        return Database::fetchAll($sql, ['id' => $this->id]);
    }

    /**
     * Get days until due
     */
    public function getDaysUntilDue(): ?int
    {
        if (!$this->due_at) {
            return null;
        }

        try {
            $dueDate = new DateTime($this->due_at);
            $now = new DateTime();
            $diff = $now->diff($dueDate);
            
            return $dueDate > $now ? $diff->days : -$diff->days;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Check if opportunity is overdue
     */
    public function isOverdue(): bool
    {
        if (!$this->due_at) {
            return false;
        }

        try {
            $dueDate = new DateTime($this->due_at);
            $now = new DateTime();
            
            return $dueDate < $now && !in_array($this->status, ['No-Bid', 'Awarded', 'Lost']);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if opportunity is due soon
     */
    public function isDueSoon(int $days = 7): bool
    {
        if (!$this->due_at) {
            return false;
        }

        $daysUntilDue = $this->getDaysUntilDue();
        return $daysUntilDue !== null && $daysUntilDue >= 0 && $daysUntilDue <= $days;
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'New' => 'badge bg-primary',
            'Review' => 'badge bg-warning',
            'Pursue' => 'badge bg-success',
            'No-Bid' => 'badge bg-secondary',
            'Awarded' => 'badge bg-success',
            'Lost' => 'badge bg-danger',
            default => 'badge bg-secondary'
        };
    }

    /**
     * Get score badge class for UI
     */
    public function getScoreBadgeClass(): string
    {
        if ($this->score >= 80) {
            return 'badge bg-success';
        } elseif ($this->score >= 60) {
            return 'badge bg-warning';
        } elseif ($this->score >= 40) {
            return 'badge bg-info';
        } elseif ($this->score > 0) {
            return 'badge bg-secondary';
        } else {
            return 'badge bg-light text-dark';
        }
    }

    /**
     * Find opportunity by external ID
     */
    public static function findByExternalId(string $externalId, string $source = 'SAM'): ?self
    {
        $sql = "SELECT * FROM opportunities WHERE source = :source AND external_id = :external_id LIMIT 1";
        $data = Database::fetchOne($sql, ['source' => $source, 'external_id' => $externalId]);
        
        if ($data) {
            $opportunity = new static();
            $opportunity->attributes = $data;
            return $opportunity;
        }
        
        return null;
    }

    /**
     * Get opportunities with filters
     */
    public static function getFiltered(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        
        $whereConditions = ['o.active = 1'];
        $params = [];
        
        // Status filter
        if (!empty($filters['status'])) {
            $whereConditions[] = 'o.status = :status';
            $params['status'] = $filters['status'];
        }
        
        // NAICS filter
        if (!empty($filters['naics_code'])) {
            $whereConditions[] = 'EXISTS (SELECT 1 FROM opportunity_naics on2 WHERE on2.opportunity_id = o.id AND on2.naics_code = :naics_code)';
            $params['naics_code'] = $filters['naics_code'];
        }
        
        // Set-aside filter
        if (!empty($filters['set_aside'])) {
            $whereConditions[] = 'o.set_aside = :set_aside';
            $params['set_aside'] = $filters['set_aside'];
        }
        
        // Agency filter
        if (!empty($filters['agency_id'])) {
            $whereConditions[] = 'o.agency_id = :agency_id';
            $params['agency_id'] = $filters['agency_id'];
        }
        
        // Score range filter
        if (!empty($filters['min_score'])) {
            $whereConditions[] = 'o.score >= :min_score';
            $params['min_score'] = $filters['min_score'];
        }
        
        if (!empty($filters['max_score'])) {
            $whereConditions[] = 'o.score <= :max_score';
            $params['max_score'] = $filters['max_score'];
        }
        
        // Due date filter
        if (!empty($filters['due_before'])) {
            $whereConditions[] = 'o.due_at <= :due_before';
            $params['due_before'] = $filters['due_before'];
        }
        
        if (!empty($filters['due_after'])) {
            $whereConditions[] = 'o.due_at >= :due_after';
            $params['due_after'] = $filters['due_after'];
        }
        
        // Search filter
        if (!empty($filters['search'])) {
            $whereConditions[] = '(o.title LIKE :search OR o.description LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        $whereClause = implode(' AND ', $whereConditions);
        
        $sql = "SELECT o.*, a.name as agency_name,
                       DATEDIFF(o.due_at, NOW()) as days_until_due
                FROM opportunities o
                LEFT JOIN agencies a ON o.agency_id = a.id
                WHERE {$whereClause}
                ORDER BY 
                    CASE WHEN o.due_at IS NULL THEN 1 ELSE 0 END,
                    o.due_at ASC,
                    o.score DESC,
                    o.created_at DESC
                LIMIT :limit OFFSET :offset";
        
        $params['limit'] = $perPage;
        $params['offset'] = $offset;
        
        return Database::fetchAll($sql, $params);
    }

    /**
     * Get total count with filters
     */
    public static function getFilteredCount(array $filters = []): int
    {
        $whereConditions = ['o.active = 1'];
        $params = [];
        
        // Apply same filters as getFiltered method
        if (!empty($filters['status'])) {
            $whereConditions[] = 'o.status = :status';
            $params['status'] = $filters['status'];
        }
        
        if (!empty($filters['naics_code'])) {
            $whereConditions[] = 'EXISTS (SELECT 1 FROM opportunity_naics on2 WHERE on2.opportunity_id = o.id AND on2.naics_code = :naics_code)';
            $params['naics_code'] = $filters['naics_code'];
        }
        
        if (!empty($filters['set_aside'])) {
            $whereConditions[] = 'o.set_aside = :set_aside';
            $params['set_aside'] = $filters['set_aside'];
        }
        
        if (!empty($filters['agency_id'])) {
            $whereConditions[] = 'o.agency_id = :agency_id';
            $params['agency_id'] = $filters['agency_id'];
        }
        
        if (!empty($filters['min_score'])) {
            $whereConditions[] = 'o.score >= :min_score';
            $params['min_score'] = $filters['min_score'];
        }
        
        if (!empty($filters['max_score'])) {
            $whereConditions[] = 'o.score <= :max_score';
            $params['max_score'] = $filters['max_score'];
        }
        
        if (!empty($filters['due_before'])) {
            $whereConditions[] = 'o.due_at <= :due_before';
            $params['due_before'] = $filters['due_before'];
        }
        
        if (!empty($filters['due_after'])) {
            $whereConditions[] = 'o.due_at >= :due_after';
            $params['due_after'] = $filters['due_after'];
        }
        
        if (!empty($filters['search'])) {
            $whereConditions[] = '(o.title LIKE :search OR o.description LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        $whereClause = implode(' AND ', $whereConditions);
        
        $sql = "SELECT COUNT(*) as count FROM opportunities o WHERE {$whereClause}";
        $result = Database::fetchOne($sql, $params);
        
        return (int) $result['count'];
    }

    /**
     * Get recent opportunities
     */
    public static function getRecent(int $limit = 10): array
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
    public static function getDueSoon(int $days = 7, int $limit = 20): array
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
     * Get high-score opportunities
     */
    public static function getHighScore(int $minScore = 70, int $limit = 20): array
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
     * Get statistics
     */
    public static function getStats(): array
    {
        $stats = [];
        
        // Total counts
        $totalStats = Database::fetchOne(
            "SELECT 
                COUNT(*) as total,
                COUNT(CASE WHEN active = 1 THEN 1 END) as active,
                COUNT(CASE WHEN score >= 70 THEN 1 END) as high_score,
                COUNT(CASE WHEN due_at BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY) THEN 1 END) as due_soon
             FROM opportunities"
        );
        
        $stats['totals'] = $totalStats;
        
        // Status breakdown
        $statusStats = Database::fetchAll(
            "SELECT status, COUNT(*) as count FROM opportunities WHERE active = 1 GROUP BY status"
        );
        
        $stats['by_status'] = array_column($statusStats, 'count', 'status');
        
        // Score distribution
        $scoreStats = Database::fetchAll(
            "SELECT 
                CASE 
                    WHEN score >= 80 THEN 'High (80-100)'
                    WHEN score >= 60 THEN 'Medium (60-79)'
                    WHEN score >= 40 THEN 'Low (40-59)'
                    WHEN score > 0 THEN 'Very Low (1-39)'
                    ELSE 'Unscored (0)'
                END as range_name,
                COUNT(*) as count
             FROM opportunities 
             WHERE active = 1
             GROUP BY range_name"
        );
        
        $stats['by_score'] = array_column($scoreStats, 'count', 'range_name');
        
        return $stats;
    }
}