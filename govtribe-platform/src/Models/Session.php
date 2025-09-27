<?php

declare(strict_types=1);

namespace GovTribe\Models;

use GovTribe\Utils\Database;
use GovTribe\Utils\Security;

/**
 * Session model
 */
class Session extends BaseModel
{
    protected string $table = 'sessions';
    protected string $primaryKey = 'id';
    
    protected array $fillable = [
        'user_id',
        'ip_address',
        'user_agent'
    ];
    
    protected array $casts = [
        'user_id' => 'int',
        'last_seen' => 'datetime',
        'created_at' => 'datetime'
    ];

    /**
     * Create new session for user
     */
    public static function createForUser(int $userId, string $ipAddress, string $userAgent): self
    {
        $sessionId = Security::generateSessionId();
        
        $session = new static();
        $session->fill([
            'id' => $sessionId,
            'user_id' => $userId,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'last_seen' => new \DateTime()
        ]);
        
        $session->save();
        return $session;
    }

    /**
     * Find session by ID
     */
    public static function findById(string $sessionId): ?self
    {
        $sql = "SELECT * FROM sessions WHERE id = :id LIMIT 1";
        $data = Database::fetchOne($sql, ['id' => $sessionId]);
        
        if ($data) {
            $session = new static();
            $session->attributes = $data;
            return $session;
        }
        
        return null;
    }

    /**
     * Update session last seen timestamp
     */
    public function updateLastSeen(): bool
    {
        $sql = "UPDATE sessions SET last_seen = NOW() WHERE id = :id";
        return Database::execute($sql, ['id' => $this->id]) > 0;
    }

    /**
     * Check if session is valid and not expired
     */
    public function isValid(int $maxLifetime = 7200): bool
    {
        if (!$this->exists()) {
            return false;
        }

        $lastSeen = new \DateTime($this->last_seen);
        $now = new \DateTime();
        $diff = $now->getTimestamp() - $lastSeen->getTimestamp();

        return $diff < $maxLifetime;
    }

    /**
     * Get user for this session
     */
    public function getUser(): ?User
    {
        if (!$this->user_id) {
            return null;
        }

        return User::find($this->user_id);
    }

    /**
     * Delete session
     */
    public function delete(): bool
    {
        $sql = "DELETE FROM sessions WHERE id = :id";
        return Database::execute($sql, ['id' => $this->id]) > 0;
    }

    /**
     * Clean up expired sessions
     */
    public static function cleanupExpired(int $maxLifetime = 7200): int
    {
        $sql = "DELETE FROM sessions WHERE last_seen < DATE_SUB(NOW(), INTERVAL :lifetime SECOND)";
        return Database::execute($sql, ['lifetime' => $maxLifetime]);
    }

    /**
     * Get active sessions for user
     */
    public static function getActiveForUser(int $userId): array
    {
        $sql = "SELECT * FROM sessions WHERE user_id = :user_id AND last_seen > DATE_SUB(NOW(), INTERVAL 7200 SECOND) ORDER BY last_seen DESC";
        $sessions = Database::fetchAll($sql, ['user_id' => $userId]);
        
        $models = [];
        foreach ($sessions as $sessionData) {
            $session = new static();
            $session->attributes = $sessionData;
            $models[] = $session;
        }
        
        return $models;
    }

    /**
     * Revoke session for user
     */
    public static function revokeForUser(int $userId, string $sessionId): bool
    {
        $sql = "DELETE FROM sessions WHERE user_id = :user_id AND id = :id";
        return Database::execute($sql, ['user_id' => $userId, 'id' => $sessionId]) > 0;
    }

    /**
     * Get session statistics
     */
    public static function getStats(): array
    {
        $sql = "SELECT 
                    COUNT(*) as total_sessions,
                    COUNT(DISTINCT user_id) as active_users,
                    COUNT(CASE WHEN last_seen > DATE_SUB(NOW(), INTERVAL 1 HOUR) THEN 1 END) as sessions_last_hour
                FROM sessions";
        
        return Database::fetchOne($sql);
    }
}