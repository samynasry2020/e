<?php

declare(strict_types=1);

namespace GovTribe\Utils;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Database connection and utility class
 */
class Database
{
    private static ?PDO $instance = null;
    private static array $config = [];

    /**
     * Initialize database connection
     */
    public static function init(array $config): void
    {
        self::$config = $config;
        self::$instance = null; // Force new connection
    }

    /**
     * Get database connection instance
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$instance = self::createConnection();
        }

        return self::$instance;
    }

    /**
     * Create new database connection
     */
    private static function createConnection(): PDO
    {
        if (empty(self::$config)) {
            throw new RuntimeException('Database configuration not initialized');
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            self::$config['host'],
            self::$config['port'],
            self::$config['name'],
            self::$config['charset']
        );

        try {
            $pdo = new PDO(
                $dsn,
                self::$config['username'],
                self::$config['password'],
                self::$config['options']
            );

            // Set timezone to UTC
            $pdo->exec("SET time_zone = '+00:00'");

            return $pdo;
        } catch (PDOException $e) {
            throw new RuntimeException(
                'Database connection failed: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Execute a query and return PDOStatement
     */
    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $pdo = self::getInstance();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Execute a query and return all results
     */
    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    /**
     * Execute a query and return single result
     */
    public static function fetchOne(string $sql, array $params = [])
    {
        return self::query($sql, $params)->fetch();
    }

    /**
     * Execute a query and return single value
     */
    public static function fetchValue(string $sql, array $params = [])
    {
        return self::query($sql, $params)->fetchColumn();
    }

    /**
     * Execute a query and return affected row count
     */
    public static function execute(string $sql, array $params = []): int
    {
        return self::query($sql, $params)->rowCount();
    }

    /**
     * Begin transaction
     */
    public static function beginTransaction(): bool
    {
        return self::getInstance()->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public static function commit(): bool
    {
        return self::getInstance()->commit();
    }

    /**
     * Rollback transaction
     */
    public static function rollback(): bool
    {
        return self::getInstance()->rollback();
    }

    /**
     * Get last insert ID
     */
    public static function lastInsertId(): string
    {
        return self::getInstance()->lastInsertId();
    }

    /**
     * Check if connection is alive
     */
    public static function isConnected(): bool
    {
        try {
            self::getInstance()->query('SELECT 1');
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Escape string for LIKE queries
     */
    public static function escapeLike(string $string): string
    {
        return str_replace(['%', '_'], ['\%', '\_'], $string);
    }

    /**
     * Build WHERE clause from conditions
     */
    public static function buildWhere(array $conditions): array
    {
        if (empty($conditions)) {
            return ['', []];
        }

        $where = [];
        $params = [];

        foreach ($conditions as $field => $value) {
            if ($value === null) {
                $where[] = "{$field} IS NULL";
            } elseif (is_array($value)) {
                $placeholders = [];
                foreach ($value as $i => $v) {
                    $key = "{$field}_{$i}";
                    $placeholders[] = ":{$key}";
                    $params[$key] = $v;
                }
                $where[] = "{$field} IN (" . implode(',', $placeholders) . ")";
            } else {
                $key = "where_{$field}";
                $where[] = "{$field} = :{$key}";
                $params[$key] = $value;
            }
        }

        return ['WHERE ' . implode(' AND ', $where), $params];
    }

    /**
     * Build ORDER BY clause
     */
    public static function buildOrderBy(array $orderBy): string
    {
        if (empty($orderBy)) {
            return '';
        }

        $clauses = [];
        foreach ($orderBy as $field => $direction) {
            $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
            $clauses[] = "{$field} {$direction}";
        }

        return 'ORDER BY ' . implode(', ', $clauses);
    }

    /**
     * Build LIMIT clause
     */
    public static function buildLimit(int $limit, int $offset = 0): string
    {
        return "LIMIT {$offset}, {$limit}";
    }
}