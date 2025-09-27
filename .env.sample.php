<?php
declare(strict_types=1);

return [
    'app' => [
        'env' => 'dev',
        'timezone_display' => 'America/Los_Angeles',
        'log_path' => __DIR__ . '/storage/logs/app.log',
        'base_url' => 'http://localhost',
    ],
    'db' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'database' => 'govtribe',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
    ],
    'session' => [
        'name' => 'APPSESSID',
    ],
    'files' => [
        'root' => __DIR__ . '/uploads',
        'max_size_bytes' => 50 * 1024 * 1024,
        'allowed_mime' => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
        ],
    ],
    'email' => [
        'smtp_host' => 'smtp.example.com',
        'smtp_port' => 587,
        'smtp_user' => 'user@example.com',
        'smtp_pass' => 'changeme',
        'from_email' => 'no-reply@example.com',
        'from_name' => 'Gov Platform',
    ],
    'sam' => [
        'api_key' => '',
        'environment' => 'prod', // 'alpha' or 'prod'
        'default_filters' => [
            'posted_window_days' => 30,
            'ptype' => ['o', 'k'],
            'naics' => ['334111', '541512'],
            'set_aside' => [],
            'only_open' => true,
            'limit' => 100,
        ],
    ],
];

