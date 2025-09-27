<?php

declare(strict_types=1);

namespace GovTribe\Controllers;

use GovTribe\Core\Database;
use GovTribe\Core\Auth;
use GovTribe\Services\{SamApiClient, IngestionService};
use GovTribe\Models\User;

class AdminController extends BaseController
{
    public function index(): string
    {
        $this->requireRole('Admin');

        // Get system overview stats
        $stats = $this->getSystemStats();
        
        // Get recent activity
        $recentActivity = $this->getRecentActivity();
        
        // Get system health
        $health = $this->getSystemHealth();

        return $this->render('admin/index', [
            'title' => 'Administration',
            'stats' => $stats,
            'recent_activity' => $recentActivity,
            'health' => $health
        ]);
    }

    public function settings(): string
    {
        $this->requireRole('Admin');

        // Get current settings
        $settings = $this->getCurrentSettings();

        return $this->render('admin/settings', [
            'title' => 'System Settings',
            'settings' => $settings
        ]);
    }

    public function updateSettings(): string
    {
        $this->requireRole('Admin');

        $updates = [
            'sam_api_key' => $_POST['sam_api_key'] ?? '',
            'sam_use_alpha' => isset($_POST['sam_use_alpha']) ? 'true' : 'false',
            'default_naics_codes' => json_encode(array_filter(explode(',', $_POST['default_naics_codes'] ?? ''))),
            'default_set_aside_types' => json_encode(array_filter(explode(',', $_POST['default_set_aside_types'] ?? ''))),
            'default_posted_days' => max(1, min(365, (int)($_POST['default_posted_days'] ?? 30))),
            'preferred_agencies' => json_encode(array_filter(explode("\n", $_POST['preferred_agencies'] ?? ''))),
            'fetch_attachments' => isset($_POST['fetch_attachments']) ? 'true' : 'false',
            'email_smtp_host' => $_POST['email_smtp_host'] ?? '',
            'email_smtp_port' => max(1, (int)($_POST['email_smtp_port'] ?? 587)),
            'email_smtp_username' => $_POST['email_smtp_username'] ?? '',
            'email_from_address' => $_POST['email_from_address'] ?? '',
            'email_from_name' => $_POST['email_from_name'] ?? 'GovTribe Platform',
            'timezone' => $_POST['timezone'] ?? 'America/Los_Angeles',
            'files_max_size' => max(1048576, (int)($_POST['files_max_size'] ?? 104857600)),
            'backup_retention_days' => max(1, (int)($_POST['backup_retention_days'] ?? 30))
        ];

        // Update SMTP password only if provided
        if (!empty($_POST['email_smtp_password'])) {
            $updates['email_smtp_password'] = $_POST['email_smtp_password'];
        }

        Database::beginTransaction();
        try {
            foreach ($updates as $key => $value) {
                Database::query(
                    'INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)',
                    [$key, $value]
                );
            }

            Database::commit();
            
            $this->logAction('update_settings', 'system', null, $updates);
            $this->flashMessage('success', 'Settings updated successfully');
            
        } catch (\Exception $e) {
            Database::rollback();
            $this->flashMessage('error', 'Failed to update settings: ' . $e->getMessage());
        }

        return $this->redirect('/admin/settings');
    }

    public function users(): string
    {
        $this->requireRole('Admin');

        $users = User::all();
        $roles = User::getRoles();
        $statuses = User::getStatuses();

        return $this->render('admin/users', [
            'title' => 'User Management',
            'users' => $users,
            'roles' => $roles,
            'statuses' => $statuses
        ]);
    }

    public function createUser(): string
    {
        $this->requireRole('Admin');

        $errors = $this->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
            'role' => 'required',
            'first_name' => 'required',
            'last_name' => 'required'
        ], $_POST);

        if (!empty($errors)) {
            $this->flashMessage('error', 'Please correct the validation errors');
            return $this->back();
        }

        // Check if email already exists
        if (User::findByEmail($_POST['email'])) {
            $this->flashMessage('error', 'Email already exists');
            return $this->back();
        }

        try {
            $userId = User::create([
                'email' => $this->sanitizeInput($_POST['email']),
                'password_hash' => Auth::hashPassword($_POST['password']),
                'role' => $_POST['role'],
                'first_name' => $this->sanitizeInput($_POST['first_name']),
                'last_name' => $this->sanitizeInput($_POST['last_name']),
                'status' => 'Active'
            ]);

            $this->logAction('create_user', 'user', $userId, [
                'email' => $_POST['email'],
                'role' => $_POST['role']
            ]);

            $this->flashMessage('success', 'User created successfully');
        } catch (\Exception $e) {
            $this->flashMessage('error', 'Failed to create user: ' . $e->getMessage());
        }

        return $this->redirect('/admin/users');
    }

    public function ingestion(): string
    {
        $this->requireRole('Admin');

        $ingestionService = new IngestionService();
        $lastResult = $ingestionService->getLastIngestionResult();
        
        // Get ingestion history
        $history = $this->getIngestionHistory();
        
        // Get current settings
        $settings = $this->getCurrentSettings();

        return $this->render('admin/ingestion', [
            'title' => 'Data Ingestion',
            'last_result' => $lastResult,
            'history' => $history,
            'settings' => $settings
        ]);
    }

    public function syncOpportunities(): string
    {
        $this->requireRole('Admin');

        $dryRun = isset($_POST['dry_run']);
        $customFilters = [];

        // Build custom filters if provided
        if (!empty($_POST['posted_from'])) {
            $customFilters['postedFrom'] = date('m/d/Y', strtotime($_POST['posted_from']));
        }
        if (!empty($_POST['posted_to'])) {
            $customFilters['postedTo'] = date('m/d/Y', strtotime($_POST['posted_to']));
        }

        try {
            $ingestionService = new IngestionService($dryRun);
            $result = $ingestionService->ingestOpportunities($customFilters);
            
            $this->logAction('manual_ingestion', 'system', null, [
                'dry_run' => $dryRun,
                'filters' => $customFilters,
                'result' => $result['summary']
            ]);

            return $this->json($result);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function testApiConnection(): string
    {
        $this->requireRole('Admin');

        try {
            $samClient = new SamApiClient();
            $result = $samClient->testConnection();
            
            $this->logAction('test_api_connection', 'system', null, $result);
            
            return $this->json($result);
            
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function auditLog(): string
    {
        $this->requireRole('Admin');

        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 50;
        $offset = ($page - 1) * $perPage;

        $filters = [
            'user_id' => $_GET['user'] ?? '',
            'entity' => $_GET['entity'] ?? '',
            'action' => $_GET['action'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? ''
        ];

        $query = $this->buildAuditQuery($filters);
        
        $logs = Database::fetchAll(
            $query['sql'] . " LIMIT {$perPage} OFFSET {$offset}",
            $query['params']
        );

        $totalResult = Database::fetchOne(
            str_replace('SELECT al.*, u.email as user_email', 'SELECT COUNT(*) as count', $query['sql']),
            $query['params']
        );
        $total = $totalResult['count'] ?? 0;

        return $this->render('admin/audit', [
            'title' => 'Audit Log',
            'logs' => $logs,
            'filters' => $filters,
            'pagination' => [
                'current' => $page,
                'total' => ceil($total / $perPage),
                'per_page' => $perPage,
                'total_items' => $total
            ]
        ]);
    }

    private function getSystemStats(): array
    {
        return [
            'total_opportunities' => Database::fetchOne('SELECT COUNT(*) as count FROM opportunities')['count'] ?? 0,
            'active_opportunities' => Database::fetchOne('SELECT COUNT(*) as count FROM opportunities WHERE active = 1')['count'] ?? 0,
            'total_users' => Database::fetchOne('SELECT COUNT(*) as count FROM users')['count'] ?? 0,
            'active_users' => Database::fetchOne('SELECT COUNT(*) as count FROM users WHERE status = "Active"')['count'] ?? 0,
            'total_proposals' => Database::fetchOne('SELECT COUNT(*) as count FROM proposals')['count'] ?? 0,
            'total_awards' => Database::fetchOne('SELECT COUNT(*) as count FROM awards')['count'] ?? 0,
            'storage_used' => $this->getStorageUsed(),
            'database_size' => $this->getDatabaseSize()
        ];
    }

    private function getRecentActivity(): array
    {
        return Database::fetchAll(
            'SELECT al.*, u.email as user_email 
             FROM audit_log al 
             LEFT JOIN users u ON al.user_id = u.id 
             ORDER BY al.created_at DESC 
             LIMIT 20'
        );
    }

    private function getSystemHealth(): array
    {
        $health = ['status' => 'ok', 'issues' => []];

        // Check database connection
        try {
            Database::query('SELECT 1');
        } catch (\Exception $e) {
            $health['status'] = 'error';
            $health['issues'][] = 'Database connection failed';
        }

        // Check files directory
        $filesRoot = \GovTribe\Core\Config::get('files.root');
        if (!is_dir($filesRoot) || !is_writable($filesRoot)) {
            $health['status'] = 'warning';
            $health['issues'][] = 'Files directory not writable';
        }

        // Check SAM API
        try {
            $samClient = new SamApiClient();
            $testResult = $samClient->testConnection();
            if (!$testResult['success']) {
                $health['status'] = 'warning';
                $health['issues'][] = 'SAM API connection issue';
            }
        } catch (\Exception $e) {
            $health['status'] = 'warning';
            $health['issues'][] = 'SAM API not configured';
        }

        return $health;
    }

    private function getCurrentSettings(): array
    {
        $settings = Database::fetchAll('SELECT `key`, `value` FROM settings');
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting['key']] = $setting['value'];
        }

        return $result;
    }

    private function getIngestionHistory(): array
    {
        $logFile = __DIR__ . '/../../storage/logs/ingestion.log';
        if (!file_exists($logFile)) {
            return [];
        }

        $lines = array_slice(file($logFile, FILE_IGNORE_NEW_LINES), -20);
        $history = [];

        foreach ($lines as $line) {
            $data = json_decode($line, true);
            if ($data) {
                $history[] = $data;
            }
        }

        return array_reverse($history);
    }

    private function buildAuditQuery(array $filters): array
    {
        $sql = 'SELECT al.*, u.email as user_email 
                FROM audit_log al 
                LEFT JOIN users u ON al.user_id = u.id';
        
        $params = [];
        $conditions = [];

        if (!empty($filters['user_id'])) {
            $conditions[] = 'al.user_id = ?';
            $params[] = $filters['user_id'];
        }

        if (!empty($filters['entity'])) {
            $conditions[] = 'al.entity = ?';
            $params[] = $filters['entity'];
        }

        if (!empty($filters['action'])) {
            $conditions[] = 'al.action = ?';
            $params[] = $filters['action'];
        }

        if (!empty($filters['date_from'])) {
            $conditions[] = 'al.created_at >= ?';
            $params[] = $filters['date_from'] . ' 00:00:00';
        }

        if (!empty($filters['date_to'])) {
            $conditions[] = 'al.created_at <= ?';
            $params[] = $filters['date_to'] . ' 23:59:59';
        }

        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= ' ORDER BY al.created_at DESC';

        return ['sql' => $sql, 'params' => $params];
    }

    private function getStorageUsed(): int
    {
        $filesRoot = \GovTribe\Core\Config::get('files.root');
        if (!is_dir($filesRoot)) {
            return 0;
        }

        $size = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($filesRoot, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            $size += $file->getSize();
        }

        return $size;
    }

    private function getDatabaseSize(): int
    {
        $result = Database::fetchOne(
            'SELECT ROUND(SUM(data_length + index_length)) as size 
             FROM information_schema.tables 
             WHERE table_schema = DATABASE()'
        );

        return (int)($result['size'] ?? 0);
    }
}