Private GovTribe-Style Platform (PHP 8.2 + MySQL 8)

Setup
1) Copy config/env.example.php to .env.php and set DB + SAM API settings.
2) Create database and user in MySQL 8.
3) Run migration:

```bash
php scripts/migrate.php
```

4) Serve app (Apache/Nginx) with document root at public/.
5) Visit /?r=health for a quick health check.

Environment (.env.php)
```php
<?php
return [
  'db' => [ 'host' => '127.0.0.1', 'port' => 3306, 'database' => 'govtribe_private', 'user' => 'app_user', 'pass' => 'change_me', 'charset' => 'utf8mb4'],
  'sam' => [ 'api_key' => '', 'use_alpha' => false ],
  'app' => [ 'tz' => 'America/Los_Angeles', 'files_root' => __DIR__.'/storage/uploads', 'log_file' => __DIR__.'/storage/logs/app.log' ],
  'security' => [ 'session_name' => 'app_session', 'csrf_token_key' => '_csrf' ]
];
```

Cron (example)
```
0 */12 * * * php /var/www/app/jobs/sam_ingest.php >> /var/www/app/storage/logs/app.log 2>&1
30 7 * * * php /var/www/app/jobs/email_digest.php >> /var/www/app/storage/logs/app.log 2>&1
0 3 * * 0 php /var/www/app/jobs/cleanup.php >> /var/www/app/storage/logs/app.log 2>&1
```

# companies_house_pro1

A new Flutter project.

## Getting Started

This project is a starting point for a Flutter application.

A few resources to get you started if this is your first Flutter project:

- [Lab: Write your first Flutter app](https://docs.flutter.dev/get-started/codelab)
- [Cookbook: Useful Flutter samples](https://docs.flutter.dev/cookbook)

For help getting started with Flutter development, view the
[online documentation](https://docs.flutter.dev/), which offers tutorials,
samples, guidance on mobile development, and a full API reference.
