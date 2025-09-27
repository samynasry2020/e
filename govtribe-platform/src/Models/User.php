<?php

declare(strict_types=1);

namespace GovTribe\Models;

use GovTribe\Utils\Security;

/**
 * User model
 */
class User extends BaseModel
{
    protected string $table = 'users';
    protected string $primaryKey = 'id';
    
    protected array $fillable = [
        'email',
        'password_hash',
        'role',
        'status',
        'mfa_secret',
        'last_login_at'
    ];
    
    protected array $casts = [
        'id' => 'int',
        'last_login_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get user role
     */
    public function getRole(): string
    {
        return $this->role ?? '';
    }

    /**
     * Check if user has specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->getRole() === $role;
    }

    /**
     * Check if user has any of the specified roles
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->getRole(), $roles, true);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('Admin');
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status === 'Active';
    }

    /**
     * Check if user can perform action based on role
     */
    public function can(string $action): bool
    {
        $permissions = $this->getPermissions();
        return in_array($action, $permissions, true);
    }

    /**
     * Get user permissions based on role
     */
    public function getPermissions(): array
    {
        $rolePermissions = [
            'Admin' => [
                'view_dashboard',
                'view_opportunities',
                'create_opportunities',
                'edit_opportunities',
                'delete_opportunities',
                'manage_users',
                'manage_settings',
                'manage_api_keys',
                'view_audit_log',
                'manage_backups',
                'view_admin_panel',
                'manage_ingestion',
                'view_reports',
                'manage_suppliers',
                'manage_rfqs',
                'manage_boms',
                'manage_proposals',
                'manage_submissions',
                'manage_awards',
                'manage_invoices'
            ],
            'Capture Manager' => [
                'view_dashboard',
                'view_opportunities',
                'create_opportunities',
                'edit_opportunities',
                'score_opportunities',
                'manage_pipeline',
                'manage_suppliers',
                'manage_rfqs',
                'view_reports'
            ],
            'Proposal Manager' => [
                'view_dashboard',
                'view_opportunities',
                'edit_opportunities',
                'manage_compliance',
                'manage_boms',
                'manage_proposals',
                'manage_submissions'
            ],
            'Sales Engineer' => [
                'view_dashboard',
                'view_opportunities',
                'manage_boms',
                'manage_pricing',
                'view_reports'
            ],
            'Vendor Manager' => [
                'view_dashboard',
                'view_opportunities',
                'manage_suppliers',
                'manage_rfqs',
                'manage_quotes'
            ],
            'Accountant' => [
                'view_dashboard',
                'view_opportunities',
                'manage_awards',
                'manage_invoices',
                'view_reports'
            ],
            'Viewer' => [
                'view_dashboard',
                'view_opportunities',
                'view_reports'
            ]
        ];

        return $rolePermissions[$this->getRole()] ?? [];
    }

    /**
     * Set password (hashed)
     */
    public function setPassword(string $password): void
    {
        $this->password_hash = Security::hashPassword($password);
    }

    /**
     * Verify password
     */
    public function verifyPassword(string $password): bool
    {
        return Security::verifyPassword($password, $this->password_hash ?? '');
    }

    /**
     * Find user by email
     */
    public static function findByEmail(string $email): ?self
    {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $data = Database::fetchOne($sql, ['email' => $email]);
        
        if ($data) {
            $user = new static();
            $user->attributes = $data;
            return $user;
        }
        
        return null;
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin(): bool
    {
        $this->last_login_at = new \DateTime();
        return $this->save();
    }

    /**
     * Get user sessions
     */
    public function getSessions(): array
    {
        $sql = "SELECT * FROM sessions WHERE user_id = :user_id ORDER BY last_seen DESC";
        return Database::fetchAll($sql, ['user_id' => $this->id]);
    }

    /**
     * Revoke all user sessions
     */
    public function revokeAllSessions(): bool
    {
        $sql = "DELETE FROM sessions WHERE user_id = :user_id";
        return Database::execute($sql, ['user_id' => $this->id]) >= 0;
    }

    /**
     * Get user audit log entries
     */
    public function getAuditLog(int $limit = 50): array
    {
        $sql = "SELECT * FROM audit_log WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit";
        return Database::fetchAll($sql, ['user_id' => $this->id, 'limit' => $limit]);
    }

    /**
     * Check if user can access admin functions
     */
    public function canAccessAdmin(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Get user display name
     */
    public function getDisplayName(): string
    {
        return $this->email;
    }

    /**
     * Convert to array for API responses
     */
    public function toArray(): array
    {
        $data = parent::toArray();
        
        // Remove sensitive data
        unset($data['password_hash'], $data['mfa_secret']);
        
        return $data;
    }

    /**
     * Get all users with pagination
     */
    public static function getPaginated(int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM users ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $users = Database::fetchAll($sql, ['limit' => $perPage, 'offset' => $offset]);
        
        $models = [];
        foreach ($users as $userData) {
            $user = new static();
            $user->attributes = $userData;
            $models[] = $user;
        }
        
        return $models;
    }

    /**
     * Get total user count
     */
    public static function getTotalCount(): int
    {
        $sql = "SELECT COUNT(*) as count FROM users";
        $result = Database::fetchOne($sql);
        return (int) $result['count'];
    }

    /**
     * Get users by role
     */
    public static function getByRole(string $role): array
    {
        $sql = "SELECT * FROM users WHERE role = :role AND status = 'Active' ORDER BY created_at DESC";
        $users = Database::fetchAll($sql, ['role' => $role]);
        
        $models = [];
        foreach ($users as $userData) {
            $user = new static();
            $user->attributes = $userData;
            $models[] = $user;
        }
        
        return $models;
    }
}