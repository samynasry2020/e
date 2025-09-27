<?php

declare(strict_types=1);

namespace App\Models;

use App\Utils\Database;
use App\Utils\Security;

class User
{
    public int $id;
    public string $email;
    public string $password_hash;
    public string $first_name;
    public string $last_name;
    public string $role;
    public string $status;
    public ?string $mfa_secret;
    public ?string $last_login_at;
    public int $login_attempts;
    public ?string $locked_until;
    public string $created_at;
    public string $updated_at;

    /**
     * Find user by email
     */
    public static function findByEmail(string $email): ?self
    {
        $data = Database::queryOne(
            'SELECT * FROM users WHERE email = ?',
            [$email]
        );

        return $data ? self::fromArray($data) : null;
    }

    /**
     * Find user by ID
     */
    public static function findById(int $id): ?self
    {
        $data = Database::queryOne(
            'SELECT * FROM users WHERE id = ?',
            [$id]
        );

        return $data ? self::fromArray($data) : null;
    }

    /**
     * Create user from array data
     */
    public static function fromArray(array $data): self
    {
        $user = new self();
        $user->id = (int) $data['id'];
        $user->email = $data['email'];
        $user->password_hash = $data['password_hash'];
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->role = $data['role'];
        $user->status = $data['status'];
        $user->mfa_secret = $data['mfa_secret'];
        $user->last_login_at = $data['last_login_at'];
        $user->login_attempts = (int) $data['login_attempts'];
        $user->locked_until = $data['locked_until'];
        $user->created_at = $data['created_at'];
        $user->updated_at = $data['updated_at'];

        return $user;
    }

    /**
     * Create new user
     */
    public static function create(array $data): self
    {
        $passwordHash = Security::hashPassword($data['password']);

        Database::execute(
            'INSERT INTO users (email, password_hash, first_name, last_name, role, status) VALUES (?, ?, ?, ?, ?, ?)',
            [
                $data['email'],
                $passwordHash,
                $data['first_name'],
                $data['last_name'],
                $data['role'] ?? 'viewer',
                $data['status'] ?? 'active'
            ]
        );

        $id = (int) Database::lastInsertId();
        return self::findById($id);
    }

    /**
     * Update user
     */
    public function update(array $data): void
    {
        $fields = [];
        $values = [];

        foreach ($data as $key => $value) {
            if (in_array($key, ['first_name', 'last_name', 'role', 'status', 'mfa_secret'])) {
                $fields[] = "{$key} = ?";
                $values[] = $value;
            }
        }

        if (!empty($fields)) {
            $values[] = $this->id;
            Database::execute(
                'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = ?',
                $values
            );
        }
    }

    /**
     * Update password
     */
    public function updatePassword(string $newPassword): void
    {
        $passwordHash = Security::hashPassword($newPassword);
        
        Database::execute(
            'UPDATE users SET password_hash = ? WHERE id = ?',
            [$passwordHash, $this->id]
        );
    }

    /**
     * Verify password
     */
    public function verifyPassword(string $password): bool
    {
        return Security::verifyPassword($password, $this->password_hash);
    }

    /**
     * Check if user is locked
     */
    public function isLocked(): bool
    {
        return $this->status === 'locked' || 
               ($this->locked_until && strtotime($this->locked_until) > time());
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && !$this->isLocked();
    }

    /**
     * Check if user has role
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    /**
     * Check if user can access admin functions
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user can manage opportunities
     */
    public function canManageOpportunities(): bool
    {
        return in_array($this->role, ['admin', 'capture_manager', 'proposal_manager']);
    }

    /**
     * Check if user can manage proposals
     */
    public function canManageProposals(): bool
    {
        return in_array($this->role, ['admin', 'proposal_manager', 'sales_engineer']);
    }

    /**
     * Check if user can manage suppliers
     */
    public function canManageSuppliers(): bool
    {
        return in_array($this->role, ['admin', 'vendor_manager']);
    }

    /**
     * Check if user can manage finances
     */
    public function canManageFinances(): bool
    {
        return in_array($this->role, ['admin', 'accountant']);
    }

    /**
     * Update last login
     */
    public function updateLastLogin(): void
    {
        Database::execute(
            'UPDATE users SET last_login_at = NOW() WHERE id = ?',
            [$this->id]
        );
    }

    /**
     * Get user's full name
     */
    public function getFullName(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get all users
     */
    public static function all(): array
    {
        $data = Database::query('SELECT * FROM users ORDER BY created_at DESC');
        
        return array_map([self::class, 'fromArray'], $data);
    }

    /**
     * Delete user
     */
    public function delete(): void
    {
        Database::execute('DELETE FROM users WHERE id = ?', [$this->id]);
    }

    /**
     * Get role permissions
     */
    public static function getRolePermissions(): array
    {
        return [
            'admin' => [
                'users.manage',
                'settings.manage',
                'opportunities.manage',
                'opportunities.score',
                'proposals.manage',
                'suppliers.manage',
                'finances.manage',
                'audit.view',
            ],
            'capture_manager' => [
                'opportunities.manage',
                'opportunities.score',
                'proposals.view',
                'suppliers.view',
            ],
            'proposal_manager' => [
                'opportunities.view',
                'proposals.manage',
                'suppliers.view',
            ],
            'sales_engineer' => [
                'opportunities.view',
                'proposals.manage',
                'suppliers.view',
            ],
            'vendor_manager' => [
                'opportunities.view',
                'proposals.view',
                'suppliers.manage',
            ],
            'accountant' => [
                'opportunities.view',
                'proposals.view',
                'suppliers.view',
                'finances.manage',
            ],
            'viewer' => [
                'opportunities.view',
                'proposals.view',
                'suppliers.view',
            ],
        ];
    }

    /**
     * Check if user has permission
     */
    public function hasPermission(string $permission): bool
    {
        $permissions = self::getRolePermissions()[$this->role] ?? [];
        return in_array($permission, $permissions);
    }
}