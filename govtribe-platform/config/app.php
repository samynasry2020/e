<?php

declare(strict_types=1);

/**
 * GovTribe Platform Configuration
 * Main application configuration file
 */

return [
    'app' => [
        'name' => 'GovTribe Platform',
        'version' => '1.0.0',
        'env' => $_ENV['APP_ENV'] ?? 'production',
        'debug' => (bool)($_ENV['APP_DEBUG'] ?? false),
        'url' => $_ENV['APP_URL'] ?? 'http://localhost',
        'timezone' => 'America/Los_Angeles',
    ],

    'database' => [
        'host' => $_ENV['DB_HOST'] ?? 'localhost',
        'port' => (int)($_ENV['DB_PORT'] ?? 3306),
        'name' => $_ENV['DB_NAME'] ?? 'govtribe_platform',
        'username' => $_ENV['DB_USERNAME'] ?? 'root',
        'password' => $_ENV['DB_PASSWORD'] ?? '',
        'charset' => 'utf8mb4',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        ],
    ],

    'security' => [
        'session' => [
            'lifetime' => 7200, // 2 hours
            'name' => 'GOVTRIBE_SESSION',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict',
        ],
        'csrf' => [
            'token_name' => '_token',
            'expire' => 3600, // 1 hour
        ],
        'password' => [
            'algorithm' => PASSWORD_ARGON2ID,
            'options' => [
                'memory_cost' => 65536,
                'time_cost' => 4,
                'threads' => 3,
            ],
        ],
        'login' => [
            'max_attempts' => 5,
            'lockout_duration' => 900, // 15 minutes
        ],
    ],

    'paths' => [
        'storage' => __DIR__ . '/../storage',
        'uploads' => __DIR__ . '/../storage/uploads',
        'logs' => __DIR__ . '/../storage/logs',
        'backups' => __DIR__ . '/../storage/backups',
        'temp' => sys_get_temp_dir(),
    ],

    'files' => [
        'max_size' => 50 * 1024 * 1024, // 50MB
        'allowed_types' => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'image/jpeg',
            'image/png',
        ],
        'allowed_extensions' => [
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'jpg', 'jpeg', 'png'
        ],
    ],

    'sam' => [
        'api' => [
            'base_url' => $_ENV['SAM_API_BASE_URL'] ?? 'https://api.sam.gov/opportunities/v2',
            'alpha_url' => $_ENV['SAM_API_ALPHA_URL'] ?? 'https://api-alpha.sam.gov/opportunities/v2',
            'timeout' => 30,
            'retry_attempts' => 4,
            'retry_delay' => 1000, // milliseconds
        ],
        'defaults' => [
            'posted_days' => 30,
            'max_range_days' => 365,
            'page_size' => 100,
            'max_page_size' => 1000,
        ],
    ],

    'email' => [
        'smtp' => [
            'host' => $_ENV['SMTP_HOST'] ?? '',
            'port' => (int)($_ENV['SMTP_PORT'] ?? 587),
            'username' => $_ENV['SMTP_USERNAME'] ?? '',
            'password' => $_ENV['SMTP_PASSWORD'] ?? '',
            'encryption' => $_ENV['SMTP_ENCRYPTION'] ?? 'tls',
        ],
        'from' => [
            'email' => $_ENV['MAIL_FROM_EMAIL'] ?? 'noreply@govtribe.local',
            'name' => $_ENV['MAIL_FROM_NAME'] ?? 'GovTribe Platform',
        ],
    ],

    'logging' => [
        'level' => $_ENV['LOG_LEVEL'] ?? 'info',
        'file' => __DIR__ . '/../storage/logs/app.log',
        'max_files' => 7,
        'max_size' => 10 * 1024 * 1024, // 10MB
    ],

    'jobs' => [
        'sam_ingest' => [
            'schedule' => '0 */12 * * *', // Every 12 hours
            'timeout' => 300, // 5 minutes
        ],
        'email_digest' => [
            'schedule' => '30 7 * * *', // Daily at 7:30 AM
            'timeout' => 60,
        ],
        'cleanup' => [
            'schedule' => '0 2 * * 0', // Weekly on Sunday at 2 AM
            'timeout' => 300,
        ],
    ],
];