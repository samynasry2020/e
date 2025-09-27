<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Database;
use App\Core\View;
use App\Middleware\Auth as AuthMiddleware;

final class AdminController
{
    public function settings(): void
    {
        AuthMiddleware::requireRole(['Admin']);
        $pdo = Database::pdo();
        $stmt = $pdo->query("SELECT `key`, `value` FROM settings");
        $settings = [];
        foreach ($stmt->fetchAll() as $row) {
            $settings[$row['key']] = $row['value'];
        }
        View::render('admin/settings.php', [
            'page_title' => 'Admin Settings',
            'settings' => $settings,
        ]);
    }

    public function saveSettings(): void
    {
        AuthMiddleware::requireRole(['Admin']);
        Csrf::validateFromRequest();
        $pdo = Database::pdo();

        $upsert = $pdo->prepare('INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)');
        $samApiKey = (string)($_POST['sam_api_key'] ?? '');
        $environment = (string)($_POST['sam_environment'] ?? 'prod');
        $defaultFilters = [
            'posted_window_days' => (int)($_POST['posted_window_days'] ?? 30),
            'ptype' => ['o','k'],
            'naics' => array_values(array_filter(array_map('trim', explode(',', (string)($_POST['naics'] ?? '334111,541512'))))),
            'set_aside' => array_values(array_filter(array_map('trim', explode(',', (string)($_POST['set_aside'] ?? ''))))),
            'only_open' => isset($_POST['only_open']),
            'limit' => 100,
        ];

        $upsert->execute(['sam_api_key', $samApiKey]);
        $upsert->execute(['sam_environment', $environment]);
        $upsert->execute(['default_filters', json_encode($defaultFilters, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)]);

        header('Location: /admin');
    }
}

