<?php

declare(strict_types=1);

namespace GovTribe\Core;

use PDO;
use PDOException;
use PDOStatement;

class Database
{
    private static ?PDO $connection = null;
    private static array $queryLog = [];

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            self::connect();
        }
        
        return self::$connection;
    }

    private static function connect(): void
    {
        $host = Config::get('db.host');
        $port = Config::get('db.port');
        $name = Config::get('db.name');
        $user = Config::get('db.user');
        $pass = Config::get('db.pass');

        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ];

        try {
            self::$connection = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new \RuntimeException('Database connection failed');
        }
    }

    public static function query(string $sql, array $params = []): PDOStatement
    {
        $connection = self::getConnection();
        
        if (Config::get('app.debug')) {
            self::$queryLog[] = [
                'sql' => $sql,
                'params' => $params,
                'timestamp' => microtime(true)
            ];
        }

        try {
            $stmt = $connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Query failed: " . $e->getMessage() . " SQL: " . $sql);
            throw $e;
        }
    }

    public static function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = self::query($sql, $params);
        $result = $stmt->fetch();
        return $result === false ? null : $result;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        $stmt = self::query($sql, $params);
        return $stmt->fetchAll();
    }

    public static function insert(string $table, array $data): int
    {
        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ':' . $col, $columns);
        
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        self::query($sql, $data);
        return (int)self::getConnection()->lastInsertId();
    }

    public static function update(string $table, array $data, array $where): int
    {
        $setClause = [];
        foreach (array_keys($data) as $column) {
            $setClause[] = "{$column} = :{$column}";
        }

        $whereClause = [];
        $whereParams = [];
        foreach ($where as $column => $value) {
            $whereClause[] = "{$column} = :where_{$column}";
            $whereParams["where_{$column}"] = $value;
        }

        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s',
            $table,
            implode(', ', $setClause),
            implode(' AND ', $whereClause)
        );

        $params = array_merge($data, $whereParams);
        $stmt = self::query($sql, $params);
        return $stmt->rowCount();
    }

    public static function delete(string $table, array $where): int
    {
        $whereClause = [];
        foreach (array_keys($where) as $column) {
            $whereClause[] = "{$column} = :{$column}";
        }

        $sql = sprintf(
            'DELETE FROM %s WHERE %s',
            $table,
            implode(' AND ', $whereClause)
        );

        $stmt = self::query($sql, $where);
        return $stmt->rowCount();
    }

    public static function beginTransaction(): bool
    {
        return self::getConnection()->beginTransaction();
    }

    public static function commit(): bool
    {
        return self::getConnection()->commit();
    }

    public static function rollback(): bool
    {
        return self::getConnection()->rollback();
    }

    public static function inTransaction(): bool
    {
        return self::getConnection()->inTransaction();
    }

    public static function getQueryLog(): array
    {
        return self::$queryLog;
    }

    public static function clearQueryLog(): void
    {
        self::$queryLog = [];
    }
}