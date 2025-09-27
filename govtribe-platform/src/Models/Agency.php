<?php

declare(strict_types=1);

namespace GovTribe\Models;

/**
 * Agency model
 */
class Agency extends BaseModel
{
    protected string $table = 'agencies';
    protected string $primaryKey = 'id';
    
    protected array $fillable = [
        'name',
        'type',
        'fh_code',
        'address'
    ];
    
    protected array $casts = [
        'id' => 'int',
        'address' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get opportunities for this agency
     */
    public function getOpportunities(): array
    {
        $sql = "SELECT * FROM opportunities WHERE agency_id = :agency_id AND active = 1 ORDER BY created_at DESC";
        return Database::fetchAll($sql, ['agency_id' => $this->id]);
    }

    /**
     * Get contacts for this agency
     */
    public function getContacts(): array
    {
        $sql = "SELECT * FROM contacts WHERE agency_id = :agency_id ORDER BY name ASC";
        return Database::fetchAll($sql, ['agency_id' => $this->id]);
    }

    /**
     * Get agency statistics
     */
    public function getStats(): array
    {
        $stats = Database::fetchOne(
            "SELECT 
                COUNT(*) as total_opportunities,
                COUNT(CASE WHEN status NOT IN ('No-Bid', 'Awarded', 'Lost') THEN 1 END) as active_opportunities,
                AVG(score) as avg_score,
                COUNT(CASE WHEN score >= 70 THEN 1 END) as high_score_count
             FROM opportunities 
             WHERE agency_id = :agency_id AND active = 1",
            ['agency_id' => $this->id]
        );

        return $stats ?: [
            'total_opportunities' => 0,
            'active_opportunities' => 0,
            'avg_score' => 0,
            'high_score_count' => 0
        ];
    }

    /**
     * Find agency by name
     */
    public static function findByName(string $name): ?self
    {
        $sql = "SELECT * FROM agencies WHERE name = :name LIMIT 1";
        $data = Database::fetchOne($sql, ['name' => $name]);
        
        if ($data) {
            $agency = new static();
            $agency->attributes = $data;
            return $agency;
        }
        
        return null;
    }

    /**
     * Get all agencies with opportunity counts
     */
    public static function getAllWithCounts(): array
    {
        $sql = "SELECT a.*, 
                       COUNT(o.id) as opportunity_count,
                       AVG(o.score) as avg_score
                FROM agencies a
                LEFT JOIN opportunities o ON a.id = o.agency_id AND o.active = 1
                GROUP BY a.id
                ORDER BY opportunity_count DESC, a.name ASC";
        
        return Database::fetchAll($sql);
    }

    /**
     * Get top agencies by opportunity count
     */
    public static function getTopByOpportunities(int $limit = 10): array
    {
        $sql = "SELECT a.*, COUNT(o.id) as opportunity_count
                FROM agencies a
                LEFT JOIN opportunities o ON a.id = o.agency_id AND o.active = 1
                GROUP BY a.id
                ORDER BY opportunity_count DESC
                LIMIT :limit";
        
        return Database::fetchAll($sql, ['limit' => $limit]);
    }
}