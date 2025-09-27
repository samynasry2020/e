<?php

declare(strict_types=1);

namespace App\Utils;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;
    private static array $config = [];

    /**
     * Initialize database connection
     */
    public static function init(): void
    {
        self::$config = [
            'host' => Config::get('database.host'),
            'port' => Config::get('database.port'),
            'name' => Config::get('database.name'),
            'user' => Config::get('database.user'),
            'pass' => Config::get('database.pass'),
        ];

        self::connect();
    }

    /**
     * Get database connection
     */
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            self::connect();
        }

        return self::$connection;
    }

    /**
     * Create database connection
     */
    private static function connect(): void
    {
        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                self::$config['host'],
                self::$config['port'],
                self::$config['name']
            );

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            ];

            self::$connection = new PDO($dsn, self::$config['user'], self::$config['pass'], $options);
        } catch (PDOException $e) {
            Logger::error('Database connection failed: ' . $e->getMessage());
            throw new \RuntimeException('Database connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Test database connection
     */
    public static function testConnection(): bool
    {
        try {
            self::getConnection()->query('SELECT 1');
            return true;
        } catch (PDOException $e) {
            Logger::error('Database connection test failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Execute a query and return all results
     */
    public static function query(string $sql, array $params = []): array
    {
        try {
            $stmt = self::getConnection()->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            Logger::error('Query failed: ' . $e->getMessage() . ' SQL: ' . $sql);
            throw new \RuntimeException('Query failed: ' . $e->getMessage());
        }
    }

    /**
     * Execute a query and return single result
     */
    public static function queryOne(string $sql, array $params = []): ?array
    {
        try {
            $stmt = self::getConnection()->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            return $result ?: null;
        } catch (PDOException $e) {
            Logger::error('Query failed: ' . $e->getMessage() . ' SQL: ' . $sql);
            throw new \RuntimeException('Query failed: ' . $e->getMessage());
        }
    }

    /**
     * Execute a query and return affected rows count
     */
    public static function execute(string $sql, array $params = []): int
    {
        try {
            $stmt = self::getConnection()->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            Logger::error('Execute failed: ' . $e->getMessage() . ' SQL: ' . $sql);
            throw new \RuntimeException('Execute failed: ' . $e->getMessage());
        }
    }

    /**
     * Get last inserted ID
     */
    public static function lastInsertId(): string
    {
        return self::getConnection()->lastInsertId();
    }

    /**
     * Begin transaction
     */
    public static function beginTransaction(): bool
    {
        return self::getConnection()->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public static function commit(): bool
    {
        return self::getConnection()->commit();
    }

    /**
     * Rollback transaction
     */
    public static function rollback(): bool
    {
        return self::getConnection()->rollback();
    }

    /**
     * Check if we're in a transaction
     */
    public static function inTransaction(): bool
    {
        return self::getConnection()->inTransaction();
    }

    /**
     * Quote a string for safe use in SQL
     */
    public static function quote(string $string): string
    {
        return self::getConnection()->quote($string);
    }

    /**
     * Get database configuration (without sensitive data)
     */
    public static function getConfig(): array
    {
        return [
            'host' => self::$config['host'],
            'port' => self::$config['port'],
            'name' => self::$config['name'],
            'user' => self::$config['user'],
            // Don't include password in config output
        ];
    }
}