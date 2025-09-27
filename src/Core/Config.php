<?php

declare(strict_types=1);

namespace GovTribe\Core;

class Config
{
    private static array $config = [];
    private static bool $loaded = false;

    public static function load(): void
    {
        if (self::$loaded) {
            return;
        }

        $envFile = __DIR__ . '/../../.env.php';
        if (file_exists($envFile)) {
            self::$config = require $envFile;
        } else {
            // Load from environment variables
            self::$config = [
                'db' => [
                    'host' => $_ENV['DB_HOST'] ?? 'localhost',
                    'port' => (int)($_ENV['DB_PORT'] ?? 3306),
                    'name' => $_ENV['DB_NAME'] ?? 'govtribe_platform',
                    'user' => $_ENV['DB_USER'] ?? 'root',
                    'pass' => $_ENV['DB_PASS'] ?? '',
                ],
                'sam' => [
                    'api_key' => $_ENV['SAM_API_KEY'] ?? '',
                    'endpoint' => $_ENV['SAM_API_ENDPOINT'] ?? 'https://api.sam.gov/opportunities/v2/search',
                    'alpha_endpoint' => $_ENV['SAM_API_ALPHA_ENDPOINT'] ?? 'https://api-alpha.sam.gov/opportunities/v2/search',
                    'use_alpha' => filter_var($_ENV['SAM_USE_ALPHA'] ?? false, FILTER_VALIDATE_BOOLEAN),
                ],
                'app' => [
                    'env' => $_ENV['APP_ENV'] ?? 'production',
                    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'timezone' => $_ENV['APP_TIMEZONE'] ?? 'America/Los_Angeles',
                    'url' => $_ENV['APP_URL'] ?? 'http://localhost',
                ],
                'security' => [
                    'session_lifetime' => (int)($_ENV['SESSION_LIFETIME'] ?? 7200),
                    'csrf_token_name' => $_ENV['CSRF_TOKEN_NAME'] ?? 'csrf_token',
                    'password_hash_algo' => $_ENV['PASSWORD_HASH_ALGO'] ?? 'PASSWORD_ARGON2ID',
                ],
                'files' => [
                    'root' => $_ENV['FILES_ROOT'] ?? __DIR__ . '/../../storage/uploads',
                    'max_size' => (int)($_ENV['FILES_MAX_SIZE'] ?? 104857600),
                    'allowed_types' => explode(',', $_ENV['FILES_ALLOWED_TYPES'] ?? 'pdf,doc,docx,xls,xlsx,txt'),
                ],
                'email' => [
                    'host' => $_ENV['SMTP_HOST'] ?? '',
                    'port' => (int)($_ENV['SMTP_PORT'] ?? 587),
                    'username' => $_ENV['SMTP_USERNAME'] ?? '',
                    'password' => $_ENV['SMTP_PASSWORD'] ?? '',
                    'from_email' => $_ENV['SMTP_FROM_EMAIL'] ?? '',
                    'from_name' => $_ENV['SMTP_FROM_NAME'] ?? 'GovTribe Platform',
                ],
                'backup' => [
                    'retention_days' => (int)($_ENV['BACKUP_RETENTION_DAYS'] ?? 30),
                ],
            ];
        }

        self::$loaded = true;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::load();
        
        $keys = explode('.', $key);
        $value = self::$config;
        
        foreach ($keys as $k) {
            if (!is_array($value) || !array_key_exists($k, $value)) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }

    public static function set(string $key, mixed $value): void
    {
        self::load();
        
        $keys = explode('.', $key);
        $config = &self::$config;
        
        foreach ($keys as $k) {
            if (!isset($config[$k]) || !is_array($config[$k])) {
                $config[$k] = [];
            }
            $config = &$config[$k];
        }
        
        $config = $value;
    }

    public static function all(): array
    {
        self::load();
        return self::$config;
    }
}