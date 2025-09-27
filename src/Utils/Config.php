<?php

declare(strict_types=1);

namespace App\Utils;

class Config
{
    private static ?array $config = null;
    private static ?array $env = null;

    /**
     * Load configuration from .env file and cache it
     */
    public static function load(): void
    {
        if (self::$config !== null) {
            return;
        }

        self::loadEnv();
        self::$config = [
            'app' => [
                'name' => self::env('APP_NAME', 'GovTribe Platform'),
                'env' => self::env('APP_ENV', 'production'),
                'debug' => self::env('APP_DEBUG', 'false') === 'true',
                'timezone' => self::env('APP_TIMEZONE', 'America/Los_Angeles'),
                'url' => self::env('APP_URL', 'http://localhost'),
            ],
            'database' => [
                'host' => self::env('DB_HOST', 'localhost'),
                'port' => (int) self::env('DB_PORT', '3306'),
                'name' => self::env('DB_NAME', 'govtribe_platform'),
                'user' => self::env('DB_USER', 'govtribe_user'),
                'pass' => self::env('DB_PASS', ''),
            ],
            'sam' => [
                'api_key' => self::env('SAM_API_KEY', ''),
                'environment' => self::env('SAM_API_ENVIRONMENT', 'prod'),
                'base_url' => self::env('SAM_API_BASE_URL', 'https://api.sam.gov/opportunities/v2/search'),
                'alpha_url' => self::env('SAM_API_ALPHA_URL', 'https://api-alpha.sam.gov/opportunities/v2/search'),
            ],
            'security' => [
                'session_lifetime' => (int) self::env('SESSION_LIFETIME', '7200'),
                'csrf_token_lifetime' => (int) self::env('CSRF_TOKEN_LIFETIME', '3600'),
                'login_max_attempts' => (int) self::env('LOGIN_MAX_ATTEMPTS', '5'),
                'login_lockout_time' => (int) self::env('LOGIN_LOCKOUT_TIME', '900'),
            ],
            'upload' => [
                'max_size' => (int) self::env('UPLOAD_MAX_SIZE', '10485760'), // 10MB
                'allowed_types' => explode(',', self::env('UPLOAD_ALLOWED_TYPES', 'pdf,doc,docx,xls,xlsx,txt')),
                'path' => self::env('UPLOAD_PATH', '/workspace/uploads'),
            ],
            'email' => [
                'smtp_host' => self::env('SMTP_HOST', ''),
                'smtp_port' => (int) self::env('SMTP_PORT', '587'),
                'smtp_username' => self::env('SMTP_USERNAME', ''),
                'smtp_password' => self::env('SMTP_PASSWORD', ''),
                'from_name' => self::env('SMTP_FROM_NAME', 'GovTribe Platform'),
                'from_email' => self::env('SMTP_FROM_EMAIL', 'noreply@yourcompany.com'),
            ],
            'backup' => [
                'path' => self::env('BACKUP_PATH', '/workspace/backups'),
                'retention_days' => (int) self::env('BACKUP_RETENTION_DAYS', '30'),
            ],
            'logging' => [
                'level' => self::env('LOG_LEVEL', 'info'),
                'path' => self::env('LOG_PATH', '/workspace/storage/logs'),
            ],
        ];
    }

    /**
     * Load environment variables from .env file
     */
    private static function loadEnv(): void
    {
        if (self::$env !== null) {
            return;
        }

        $envFile = dirname(__DIR__, 2) . '/.env';
        self::$env = [];

        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && !str_starts_with($line, '#')) {
                    [$key, $value] = explode('=', $line, 2);
                    self::$env[trim($key)] = trim($value, '"\'');
                }
            }
        }

        // Override with actual environment variables if they exist
        foreach ($_ENV as $key => $value) {
            self::$env[$key] = $value;
        }
    }

    /**
     * Get environment variable with default value
     */
    private static function env(string $key, string $default = ''): string
    {
        return self::$env[$key] ?? $default;
    }

    /**
     * Get configuration value using dot notation
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        self::load();

        $keys = explode('.', $key);
        $value = self::$config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    /**
     * Get all configuration
     */
    public static function all(): array
    {
        self::load();
        return self::$config;
    }

    /**
     * Set configuration value using dot notation
     */
    public static function set(string $key, mixed $value): void
    {
        self::load();

        $keys = explode('.', $key);
        $config = &self::$config;

        foreach ($keys as $k) {
            if (!isset($config[$k])) {
                $config[$k] = [];
            }
            $config = &$config[$k];
        }

        $config = $value;
    }

    /**
     * Check if configuration key exists
     */
    public static function has(string $key): bool
    {
        self::load();

        $keys = explode('.', $key);
        $value = self::$config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return false;
            }
            $value = $value[$k];
        }

        return true;
    }
}