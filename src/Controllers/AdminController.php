<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\AuthController;
use App\Models\User;
use App\Services\IngestionService;
use App\Services\SamApiService;
use App\Utils\Config;
use App\Utils\Database;
use App\Utils\Router;
use App\Utils\Security;

class AdminController
{
    /**
     * Show admin dashboard
     */
    public function index(): void
    {
        AuthController::requireRole('admin');

        $user = AuthController::getCurrentUser();
        $stats = $this->getSystemStats();

        $this->renderView('admin/index', [
            'title' => 'Admin Dashboard',
            'user' => $user,
            'stats' => $stats,
        ]);
    }

    /**
     * Show users management
     */
    public function users(): void
    {
        AuthController::requireRole('admin');

        $user = AuthController::getCurrentUser();
        $users = User::all();

        $this->renderView('admin/users', [
            'title' => 'User Management',
            'user' => $user,
            'users' => $users,
        ]);
    }

    /**
     * Show settings page
     */
    public function settings(): void
    {
        AuthController::requireRole('admin');

        $user = AuthController::getCurrentUser();
        $settings = $this->getSettings();

        $this->renderView('admin/settings', [
            'title' => 'System Settings',
            'user' => $user,
            'settings' => $settings,
        ]);
    }

    /**
     * Update settings
     */
    public function updateSettings(): void
    {
        AuthController::requireRole('admin');

        // Validate CSRF token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Security::validateCsrfToken($csrfToken)) {
            Router::redirect('/admin/settings');
            return;
        }

        $settings = [
            'sam_api_key' => Security::sanitizeInput($_POST['sam_api_key'] ?? ''),
            'sam_api_environment' => Security::sanitizeInput($_POST['sam_api_environment'] ?? 'prod'),
            'default_naics_codes' => json_encode($_POST['naics_codes'] ?? []),
            'default_set_aside_codes' => json_encode($_POST['set_aside_codes'] ?? []),
            'default_posted_days' => (int) ($_POST['posted_days'] ?? 30),
            'attachment_fetching_enabled' => isset($_POST['attachment_fetching']) ? 'true' : 'false',
        ];

        foreach ($settings as $key => $value) {
            Database::execute(
                'INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = ?',
                [$key, $value, $value]
            );
        }

        Router::redirect('/admin/settings?success=1');
    }

    /**
     * Trigger manual sync
     */
    public function syncNow(): void
    {
        AuthController::requireRole('admin');

        // Validate CSRF token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Security::validateCsrfToken($csrfToken)) {
            Router::redirect('/admin');
            return;
        }

        try {
            $ingestionService = new IngestionService();
            $results = $ingestionService->ingestOpportunities();

            $_SESSION['sync_results'] = $results;
            Router::redirect('/admin?sync=completed');
        } catch (\Exception $e) {
            $_SESSION['sync_error'] = $e->getMessage();
            Router::redirect('/admin?sync=error');
        }
    }

    /**
     * Get system statistics
     */
    private function getSystemStats(): array
    {
        $stats = [];

        // Database statistics
        $stats['opportunities'] = Database::queryOne('SELECT COUNT(*) as count FROM opportunities')['count'] ?? 0;
        $stats['agencies'] = Database::queryOne('SELECT COUNT(*) as count FROM agencies')['count'] ?? 0;
        $stats['users'] = Database::queryOne('SELECT COUNT(*) as count FROM users')['count'] ?? 0;
        $stats['files'] = Database::queryOne('SELECT COUNT(*) as count FROM files')['count'] ?? 0;

        // Recent activity
        $stats['recent_opportunities'] = Database::queryOne(
            'SELECT COUNT(*) as count FROM opportunities WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)'
        )['count'] ?? 0;

        // System health
        $stats['database_status'] = Database::testConnection() ? 'Connected' : 'Disconnected';
        
        try {
            $samApi = new SamApiService();
            $testResult = $samApi->testConnection();
            $stats['sam_api_status'] = $testResult['success'] ? 'Connected' : 'Error: ' . $testResult['message'];
        } catch (\Exception $e) {
            $stats['sam_api_status'] = 'Error: ' . $e->getMessage();
        }

        return $stats;
    }

    /**
     * Get current settings
     */
    private function getSettings(): array
    {
        $settingsData = Database::query('SELECT `key`, `value` FROM settings');
        $settings = [];

        foreach ($settingsData as $setting) {
            $settings[$setting['key']] = $setting['value'];
        }

        // Default values
        $defaults = [
            'sam_api_key' => '',
            'sam_api_environment' => 'prod',
            'default_naics_codes' => '["334111", "541512"]',
            'default_set_aside_codes' => '["WOSB", "EDWOSB"]',
            'default_posted_days' => '30',
            'attachment_fetching_enabled' => 'false',
        ];

        foreach ($defaults as $key => $defaultValue) {
            if (!isset($settings[$key])) {
                $settings[$key] = $defaultValue;
            }
        }

        // Parse JSON values
        $settings['naics_codes'] = json_decode($settings['default_naics_codes'], true) ?: [];
        $settings['set_aside_codes'] = json_decode($settings['default_set_aside_codes'], true) ?: [];

        return $settings;
    }

    /**
     * Render view with data
     */
    private function renderView(string $view, array $data = []): void
    {
        extract($data);
        
        $viewFile = dirname(__DIR__, 2) . "/views/{$view}.php";
        
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            throw new \RuntimeException("View not found: {$view}");
        }
    }
}