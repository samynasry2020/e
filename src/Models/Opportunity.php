<?php

declare(strict_types=1);

namespace App\Models;

use App\Utils\Database;

class Opportunity
{
    public int $id;
    public string $source;
    public string $external_id;
    public string $title;
    public ?string $description;
    public string $description_status;
    public int $agency_id;
    public string $posted_at;
    public ?string $due_at;
    public string $notice_type;
    public ?string $set_aside;
    public ?string $ui_link;
    public int $score;
    public ?string $score_reasons;
    public string $status;
    public bool $active;
    public string $created_at;
    public string $updated_at;

    // Related objects
    public ?Agency $agency = null;
    public array $naics_codes = [];
    public array $contacts = [];
    public array $documents = [];

    /**
     * Find opportunity by ID
     */
    public static function findById(int $id): ?self
    {
        $data = Database::queryOne(
            'SELECT * FROM opportunities WHERE id = ?',
            [$id]
        );

        return $data ? self::fromArray($data) : null;
    }

    /**
     * Find opportunity by external ID
     */
    public static function findByExternalId(string $source, string $externalId): ?self
    {
        $data = Database::queryOne(
            'SELECT * FROM opportunities WHERE source = ? AND external_id = ?',
            [$source, $externalId]
        );

        return $data ? self::fromArray($data) : null;
    }

    /**
     * Create opportunity from array data
     */
    public static function fromArray(array $data): self
    {
        $opportunity = new self();
        $opportunity->id = (int) $data['id'];
        $opportunity->source = $data['source'];
        $opportunity->external_id = $data['external_id'];
        $opportunity->title = $data['title'];
        $opportunity->description = $data['description'];
        $opportunity->description_status = $data['description_status'];
        $opportunity->agency_id = (int) $data['agency_id'];
        $opportunity->posted_at = $data['posted_at'];
        $opportunity->due_at = $data['due_at'];
        $opportunity->notice_type = $data['notice_type'];
        $opportunity->set_aside = $data['set_aside'];
        $opportunity->ui_link = $data['ui_link'];
        $opportunity->score = (int) $data['score'];
        $opportunity->score_reasons = $data['score_reasons'];
        $opportunity->status = $data['status'];
        $opportunity->active = (bool) $data['active'];
        $opportunity->created_at = $data['created_at'];
        $opportunity->updated_at = $data['updated_at'];

        return $opportunity;
    }

    /**
     * Load opportunity with related data
     */
    public function loadRelations(): void
    {
        // Load agency
        $this->agency = Agency::findById($this->agency_id);

        // Load NAICS codes
        $this->naics_codes = Database::query(
            'SELECT naics_code FROM opportunity_naics WHERE opportunity_id = ?',
            [$this->id]
        );

        // Load contacts
        $this->contacts = Database::query(
            'SELECT * FROM contacts WHERE agency_id = ?',
            [$this->agency_id]
        );

        // Load documents
        $this->documents = Database::query(
            'SELECT d.*, f.original_name, f.mime_type, f.size, f.path 
             FROM documents d 
             JOIN files f ON d.file_id = f.id 
             WHERE d.opportunity_id = ?',
            [$this->id]
        );
    }

    /**
     * Update opportunity status
     */
    public function updateStatus(string $status, ?string $note = null): void
    {
        $allowedStatuses = ['New', 'Review', 'Pursue', 'No-Bid', 'Awarded', 'Lost'];
        
        if (!in_array($status, $allowedStatuses)) {
            throw new \InvalidArgumentException("Invalid status: {$status}");
        }

        Database::execute(
            'UPDATE opportunities SET status = ?, updated_at = NOW() WHERE id = ?',
            [$status, $this->id]
        );

        // Log the change
        $change = [
            'status' => ['old' => $this->status, 'new' => $status],
            'note' => $note,
            'changed_by' => $_SESSION['user_id'] ?? null,
            'changed_at' => date('Y-m-d H:i:s')
        ];

        Database::execute(
            'INSERT INTO opportunity_changes (opportunity_id, payload) VALUES (?, ?)',
            [$this->id, json_encode($change)]
        );

        $this->status = $status;
    }

    /**
     * Update opportunity score
     */
    public function updateScore(int $score, array $reasons = []): void
    {
        $score = max(0, min(100, $score));

        Database::execute(
            'UPDATE opportunities SET score = ?, score_reasons = ?, updated_at = NOW() WHERE id = ?',
            [$score, json_encode($reasons), $this->id]
        );

        $this->score = $score;
        $this->score_reasons = json_encode($reasons);
    }

    /**
     * Get opportunities with filters
     */
    public static function getFiltered(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        $where = ['1=1'];
        $params = [];

        // Status filter
        if (!empty($filters['status'])) {
            $where[] = 'status = ?';
            $params[] = $filters['status'];
        }

        // Agency filter
        if (!empty($filters['agency_id'])) {
            $where[] = 'agency_id = ?';
            $params[] = $filters['agency_id'];
        }

        // NAICS filter
        if (!empty($filters['naics_code'])) {
            $where[] = 'id IN (SELECT opportunity_id FROM opportunity_naics WHERE naics_code = ?)';
            $params[] = $filters['naics_code'];
        }

        // Set-aside filter
        if (!empty($filters['set_aside'])) {
            $where[] = 'set_aside = ?';
            $params[] = $filters['set_aside'];
        }

        // Notice type filter
        if (!empty($filters['notice_type'])) {
            $where[] = 'notice_type = ?';
            $params[] = $filters['notice_type'];
        }

        // Score range filter
        if (!empty($filters['score_min'])) {
            $where[] = 'score >= ?';
            $params[] = $filters['score_min'];
        }

        if (!empty($filters['score_max'])) {
            $where[] = 'score <= ?';
            $params[] = $filters['score_max'];
        }

        // Due date filter
        if (!empty($filters['due_before'])) {
            $where[] = 'due_at <= ?';
            $params[] = $filters['due_before'];
        }

        if (!empty($filters['due_after'])) {
            $where[] = 'due_at >= ?';
            $params[] = $filters['due_after'];
        }

        // Active filter
        if (isset($filters['active'])) {
            $where[] = 'active = ?';
            $params[] = $filters['active'] ? 1 : 0;
        }

        // Search filter
        if (!empty($filters['search'])) {
            $where[] = '(title LIKE ? OR description LIKE ?)';
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $sql = 'SELECT * FROM opportunities WHERE ' . implode(' AND ', $where) . ' ORDER BY posted_at DESC';
        
        if ($limit > 0) {
            $sql .= ' LIMIT ? OFFSET ?';
            $params[] = $limit;
            $params[] = $offset;
        }

        $data = Database::query($sql, $params);
        
        return array_map([self::class, 'fromArray'], $data);
    }

    /**
     * Get opportunity count with filters
     */
    public static function getCount(array $filters = []): int
    {
        $where = ['1=1'];
        $params = [];

        // Apply same filters as getFiltered method
        if (!empty($filters['status'])) {
            $where[] = 'status = ?';
            $params[] = $filters['status'];
        }

        if (!empty($filters['agency_id'])) {
            $where[] = 'agency_id = ?';
            $params[] = $filters['agency_id'];
        }

        if (!empty($filters['naics_code'])) {
            $where[] = 'id IN (SELECT opportunity_id FROM opportunity_naics WHERE naics_code = ?)';
            $params[] = $filters['naics_code'];
        }

        if (!empty($filters['set_aside'])) {
            $where[] = 'set_aside = ?';
            $params[] = $filters['set_aside'];
        }

        if (!empty($filters['notice_type'])) {
            $where[] = 'notice_type = ?';
            $params[] = $filters['notice_type'];
        }

        if (isset($filters['active'])) {
            $where[] = 'active = ?';
            $params[] = $filters['active'] ? 1 : 0;
        }

        if (!empty($filters['search'])) {
            $where[] = '(title LIKE ? OR description LIKE ?)';
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $sql = 'SELECT COUNT(*) FROM opportunities WHERE ' . implode(' AND ', $where);
        $result = Database::queryOne($sql, $params);

        return (int) ($result['COUNT(*)'] ?? 0);
    }

    /**
     * Get dashboard statistics
     */
    public static function getDashboardStats(): array
    {
        $stats = Database::queryOne(
            'SELECT 
                COUNT(*) as total,
                COUNT(CASE WHEN status = "New" THEN 1 END) as new_count,
                COUNT(CASE WHEN status = "Review" THEN 1 END) as review_count,
                COUNT(CASE WHEN status = "Pursue" THEN 1 END) as pursue_count,
                COUNT(CASE WHEN due_at <= DATE_ADD(NOW(), INTERVAL 7 DAY) AND due_at >= NOW() THEN 1 END) as due_soon,
                COUNT(CASE WHEN posted_at >= DATE_SUB(NOW(), INTERVAL 1 DAY) THEN 1 END) as new_today
             FROM opportunities 
             WHERE active = 1'
        );

        return $stats ?: [
            'total' => 0,
            'new_count' => 0,
            'review_count' => 0,
            'pursue_count' => 0,
            'due_soon' => 0,
            'new_today' => 0
        ];
    }

    /**
     * Get opportunity changes
     */
    public function getChanges(): array
    {
        return Database::query(
            'SELECT * FROM opportunity_changes WHERE opportunity_id = ? ORDER BY changed_at DESC',
            [$this->id]
        );
    }

    /**
     * Check if opportunity is due soon
     */
    public function isDueSoon(int $days = 7): bool
    {
        if (!$this->due_at) {
            return false;
        }

        $dueTime = strtotime($this->due_at);
        $now = time();
        $daysUntilDue = ($dueTime - $now) / (24 * 60 * 60);

        return $daysUntilDue <= $days && $daysUntilDue >= 0;
    }

    /**
     * Check if opportunity is overdue
     */
    public function isOverdue(): bool
    {
        if (!$this->due_at) {
            return false;
        }

        return strtotime($this->due_at) < time();
    }

    /**
     * Get days until due
     */
    public function getDaysUntilDue(): ?int
    {
        if (!$this->due_at) {
            return null;
        }

        $dueTime = strtotime($this->due_at);
        $now = time();
        $daysUntilDue = ($dueTime - $now) / (24 * 60 * 60);

        return (int) ceil($daysUntilDue);
    }

    /**
     * Get parsed score reasons
     */
    public function getScoreReasons(): array
    {
        if (empty($this->score_reasons)) {
            return [];
        }

        $reasons = json_decode($this->score_reasons, true);
        return is_array($reasons) ? $reasons : [];
    }
}