<?php

declare(strict_types=1);

namespace App\Models;

use App\Utils\Database;

class Agency
{
    public int $id;
    public string $name;
    public string $type;
    public ?string $fh_code;
    public ?array $address;
    public bool $is_preferred;
    public string $created_at;
    public string $updated_at;

    /**
     * Find agency by ID
     */
    public static function findById(int $id): ?self
    {
        $data = Database::queryOne(
            'SELECT * FROM agencies WHERE id = ?',
            [$id]
        );

        return $data ? self::fromArray($data) : null;
    }

    /**
     * Find agency by name
     */
    public static function findByName(string $name): ?self
    {
        $data = Database::queryOne(
            'SELECT * FROM agencies WHERE name = ?',
            [$name]
        );

        return $data ? self::fromArray($data) : null;
    }

    /**
     * Create agency from array data
     */
    public static function fromArray(array $data): self
    {
        $agency = new self();
        $agency->id = (int) $data['id'];
        $agency->name = $data['name'];
        $agency->type = $data['type'];
        $agency->fh_code = $data['fh_code'];
        $agency->address = $data['address'] ? json_decode($data['address'], true) : null;
        $agency->is_preferred = (bool) $data['is_preferred'];
        $agency->created_at = $data['created_at'];
        $agency->updated_at = $data['updated_at'];

        return $agency;
    }

    /**
     * Get all agencies
     */
    public static function all(): array
    {
        $data = Database::query('SELECT * FROM agencies ORDER BY name');
        
        return array_map([self::class, 'fromArray'], $data);
    }

    /**
     * Get preferred agencies
     */
    public static function getPreferred(): array
    {
        $data = Database::query('SELECT * FROM agencies WHERE is_preferred = 1 ORDER BY name');
        
        return array_map([self::class, 'fromArray'], $data);
    }

    /**
     * Update agency
     */
    public function update(array $data): void
    {
        $fields = [];
        $values = [];

        foreach ($data as $key => $value) {
            if (in_array($key, ['name', 'type', 'fh_code', 'address', 'is_preferred'])) {
                $fields[] = "{$key} = ?";
                $values[] = $key === 'address' ? json_encode($value) : $value;
            }
        }

        if (!empty($fields)) {
            $values[] = $this->id;
            Database::execute(
                'UPDATE agencies SET ' . implode(', ', $fields) . ' WHERE id = ?',
                $values
            );
        }
    }

    /**
     * Get agency statistics
     */
    public function getStats(): array
    {
        $stats = Database::queryOne(
            'SELECT 
                COUNT(*) as total_opportunities,
                COUNT(CASE WHEN status = "Pursue" THEN 1 END) as pursuing,
                COUNT(CASE WHEN status = "Awarded" THEN 1 END) as awarded,
                AVG(score) as avg_score
             FROM opportunities 
             WHERE agency_id = ?',
            [$this->id]
        );

        return $stats ?: [
            'total_opportunities' => 0,
            'pursuing' => 0,
            'awarded' => 0,
            'avg_score' => 0
        ];
    }
}