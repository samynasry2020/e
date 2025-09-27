<?php
return [
  'db' => [
    'host' => '127.0.0.1',
    'port' => 3306,
    'database' => 'govtribe_private',
    'user' => 'app_user',
    'pass' => 'change_me',
    'charset' => 'utf8mb4'
  ],
  'sam' => [
    'api_key' => '',
    'use_alpha' => false,
    'base_url_prod' => 'https://api.sam.gov/opportunities/v2/search',
    'base_url_alpha' => 'https://api-alpha.sam.gov/opportunities/v2/search'
  ],
  'app' => [
    'tz' => 'America/Los_Angeles',
    'files_root' => __DIR__ . '/../storage/uploads',
    'log_file' => __DIR__ . '/../storage/logs/app.log'
  ],
  'security' => [
    'session_name' => 'app_session',
    'csrf_token_key' => '_csrf',
    'login_max_attempts' => 5,
    'login_window_minutes' => 15
  ]
];

