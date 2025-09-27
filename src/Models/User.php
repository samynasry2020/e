<?php

declare(strict_types=1);

namespace GovTribe\Models;

use GovTribe\Core\Database;

class User
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getId(): int
    {
        return (int)$this->data['id'];
    }

    public function getEmail(): string
    {
        return $this->data['email'];
    }

    public function getRole(): string
    {
        return $this->data['role'];
    }

    public function getStatus(): string
    {
        return $this->data['status'];
    }

    public function getFirstName(): ?string
    {
        return $this->data['first_name'] ?? null;
    }

    public function getLastName(): ?string
    {
        return $this->data['last_name'] ?? null;
    }

    public function getFullName(): string
    {
        $parts = array_filter([$this->getFirstName(), $this->getLastName()]);
        return implode(' ', $parts) ?: $this->getEmail();
    }

    public function getLastLogin(): ?string
    {
        return $this->data['last_login'] ?? null;
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public static function find(int $id): ?array
    {
        return Database::fetchOne('SELECT * FROM users WHERE id = ?', [$id]);
    }

    public static function findByEmail(string $email): ?array
    {
        return Database::fetchOne('SELECT * FROM users WHERE email = ?', [$email]);
    }

    public static function create(array $data): int
    {
        $requiredFields = ['email', 'password_hash', 'role'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }

        return Database::insert('users', $data);
    }

    public static function update(int $id, array $data): bool
    {
        return Database::update('users', $data, ['id' => $id]) > 0;
    }

    public static function delete(int $id): bool
    {
        return Database::delete('users', ['id' => $id]) > 0;
    }

    public static function all(array $filters = []): array
    {
        $sql = 'SELECT * FROM users';
        $params = [];

        if (!empty($filters)) {
            $conditions = [];
            if (isset($filters['role'])) {
                $conditions[] = 'role = :role';
                $params['role'] = $filters['role'];
            }
            if (isset($filters['status'])) {
                $conditions[] = 'status = :status';
                $params['status'] = $filters['status'];
            }
            if (!empty($conditions)) {
                $sql .= ' WHERE ' . implode(' AND ', $conditions);
            }
        }

        $sql .= ' ORDER BY created_at DESC';

        return Database::fetchAll($sql, $params);
    }

    public static function getRoles(): array
    {
        return [
            'Admin' => 'Administrator',
            'Capture Manager' => 'Capture Manager',
            'Proposal Manager' => 'Proposal Manager',
            'Sales Engineer' => 'Sales Engineer/Estimator',
            'Vendor Manager' => 'Vendor Manager',
            'Accountant' => 'Accountant',
            'Viewer' => 'Viewer/Auditor'
        ];
    }

    public static function getStatuses(): array
    {
        return [
            'Active' => 'Active',
            'Inactive' => 'Inactive',
            'Suspended' => 'Suspended'
        ];
    }
}