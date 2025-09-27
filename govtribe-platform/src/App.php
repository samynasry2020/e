<?php

declare(strict_types=1);

namespace GovTribe;

use GovTribe\Utils\Database;
use GovTribe\Utils\Logger;
use GovTribe\Utils\Security;
use GovTribe\Services\AuthService;
use GovTribe\Services\SamApiService;
use GovTribe\Services\IngestionService;
use GovTribe\Services\ScoringService;
use GovTribe\Services\PipelineService;

/**
 * Main application class
 */
class App
{
    private static ?App $instance = null;
    private array $config;
    private AuthService $authService;
    private SamApiService $samApiService;
    private IngestionService $ingestionService;
    private ScoringService $scoringService;
    private PipelineService $pipelineService;

    private function __construct(array $config)
    {
        $this->config = $config;
        $this->initializeServices();
    }

    /**
     * Get application instance
     */
    public static function getInstance(array $config = null): App
    {
        if (self::$instance === null) {
            if ($config === null) {
                throw new \RuntimeException('Application not initialized');
            }
            self::$instance = new self($config);
        }

        return self::$instance;
    }

    /**
     * Initialize application
     */
    public static function initialize(array $config): App
    {
        // Initialize core services
        Database::init($config['database']);
        Logger::init($config['logging']);
        
        Security::init(
            $_ENV['APP_KEY'] ?? 'default_key_change_in_production_32_chars',
            $_ENV['SESSION_ENCRYPTION_KEY'] ?? 'default_session_key_change_in_prod'
        );

        return self::getInstance($config);
    }

    /**
     * Initialize all services
     */
    private function initializeServices(): void
    {
        // Load settings from database
        $settings = $this->loadSettings();

        // Initialize SAM API service
        $this->samApiService = new SamApiService($this->config['sam']);
        if (!empty($settings['sam_api_key'])) {
            $this->samApiService->setApiKey($settings['sam_api_key']);
        }
        $this->samApiService->setUseAlpha((bool)($settings['sam_use_alpha'] ?? false));

        // Initialize ingestion service
        $this->ingestionService = new IngestionService($this->samApiService, $settings);

        // Initialize scoring service
        $this->scoringService = new ScoringService($settings);

        // Initialize pipeline service
        $this->authService = new AuthService();
        $this->pipelineService = new PipelineService($this->authService);
    }

    /**
     * Load settings from database
     */
    private function loadSettings(): array
    {
        try {
            $sql = "SELECT setting_key, setting_value FROM settings";
            $settingsData = Database::fetchAll($sql);
            
            $settings = [];
            foreach ($settingsData as $row) {
                $key = $row['setting_key'];
                $value = $row['setting_value'];
                
                // Decode JSON values
                if (in_array($key, ['default_naics_codes', 'default_set_aside_codes', 'preferred_agencies', 'email_smtp'])) {
                    $settings[$key] = json_decode($value, true) ?: [];
                } else {
                    $settings[$key] = $value;
                }
            }
            
            // Set defaults
            $settings['allowed_naics_codes'] = $settings['default_naics_codes'] ?? ['334111', '541512'];
            $settings['allowed_set_aside_codes'] = $settings['default_set_aside_codes'] ?? ['WOSB', 'EDWOSB'];
            $settings['preferred_set_aside_codes'] = $settings['default_set_aside_codes'] ?? ['WOSB', 'EDWOSB'];
            $settings['posted_days'] = (int)($settings['default_posted_days'] ?? 30);
            $settings['filter_set_aside'] = !empty($settings['default_set_aside_codes']);
            $settings['preferred_agencies'] = $settings['preferred_agencies'] ?? [];
            
            return $settings;
            
        } catch (\Exception $e) {
            Logger::getInstance()->error('Failed to load settings from database', [
                'error' => $e->getMessage()
            ]);
            
            // Return default settings
            return [
                'allowed_naics_codes' => ['334111', '541512'],
                'allowed_set_aside_codes' => ['WOSB', 'EDWOSB'],
                'preferred_set_aside_codes' => ['WOSB', 'EDWOSB'],
                'posted_days' => 30,
                'filter_set_aside' => false,
                'preferred_agencies' => [],
                'fetch_attachments' => true,
                'files_root' => $this->config['paths']['uploads']
            ];
        }
    }

    /**
     * Get configuration
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Get auth service
     */
    public function getAuthService(): AuthService
    {
        return $this->authService;
    }

    /**
     * Get SAM API service
     */
    public function getSamApiService(): SamApiService
    {
        return $this->samApiService;
    }

    /**
     * Get ingestion service
     */
    public function getIngestionService(): IngestionService
    {
        return $this->ingestionService;
    }

    /**
     * Get scoring service
     */
    public function getScoringService(): ScoringService
    {
        return $this->scoringService;
    }

    /**
     * Get pipeline service
     */
    public function getPipelineService(): PipelineService
    {
        return $this->pipelineService;
    }

    /**
     * Update settings in database
     */
    public function updateSettings(array $settings): bool
    {
        try {
            Database::beginTransaction();
            
            foreach ($settings as $key => $value) {
                if (is_array($value)) {
                    $value = json_encode($value);
                }
                
                $sql = "INSERT INTO settings (setting_key, setting_value, updated_at) 
                        VALUES (:key, :value, NOW())
                        ON DUPLICATE KEY UPDATE 
                        setting_value = VALUES(setting_value), 
                        updated_at = NOW()";
                
                Database::execute($sql, [
                    'key' => $key,
                    'value' => $value
                ]);
            }
            
            Database::commit();
            
            // Reinitialize services with new settings
            $this->initializeServices();
            
            Logger::getInstance()->info('Settings updated successfully', [
                'updated_keys' => array_keys($settings)
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Database::rollback();
            Logger::getInstance()->error('Failed to update settings', [
                'error' => $e->getMessage(),
                'settings' => array_keys($settings)
            ]);
            return false;
        }
    }

    /**
     * Get application health status
     */
    public function getHealthStatus(): array
    {
        $health = [
            'status' => 'healthy',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0.0',
            'checks' => []
        ];

        // Database connection
        try {
            Database::getInstance()->query('SELECT 1');
            $health['checks']['database'] = ['status' => 'ok', 'message' => 'Connected'];
        } catch (\Exception $e) {
            $health['checks']['database'] = ['status' => 'error', 'message' => $e->getMessage()];
            $health['status'] = 'unhealthy';
        }

        // SAM API connection
        try {
            $apiHealth = $this->samApiService->testConnection();
            $health['checks']['sam_api'] = $apiHealth;
            if (!$apiHealth['success']) {
                $health['status'] = 'degraded';
            }
        } catch (\Exception $e) {
            $health['checks']['sam_api'] = ['status' => 'error', 'message' => $e->getMessage()];
            $health['status'] = 'degraded';
        }

        // File system
        try {
            $uploadsDir = $this->config['paths']['uploads'];
            if (!is_dir($uploadsDir)) {
                mkdir($uploadsDir, 0755, true);
            }
            if (!is_writable($uploadsDir)) {
                throw new \RuntimeException('Uploads directory not writable');
            }
            $health['checks']['filesystem'] = ['status' => 'ok', 'message' => 'Writable'];
        } catch (\Exception $e) {
            $health['checks']['filesystem'] = ['status' => 'error', 'message' => $e->getMessage()];
            $health['status'] = 'unhealthy';
        }

        // PHP extensions
        $requiredExtensions = ['pdo', 'pdo_mysql', 'curl', 'json', 'openssl'];
        $missingExtensions = [];
        
        foreach ($requiredExtensions as $ext) {
            if (!extension_loaded($ext)) {
                $missingExtensions[] = $ext;
            }
        }
        
        if (!empty($missingExtensions)) {
            $health['checks']['php_extensions'] = [
                'status' => 'error', 
                'message' => 'Missing extensions: ' . implode(', ', $missingExtensions)
            ];
            $health['status'] = 'unhealthy';
        } else {
            $health['checks']['php_extensions'] = ['status' => 'ok', 'message' => 'All required extensions loaded'];
        }

        return $health;
    }

    /**
     * Run maintenance tasks
     */
    public function runMaintenance(): array
    {
        $results = [];

        try {
            // Clean up expired sessions
            $expiredSessions = \GovTribe\Models\Session::cleanupExpired();
            $results['expired_sessions'] = $expiredSessions;

            // Archive old completed opportunities
            $archivedOpportunities = $this->pipelineService->archiveCompletedOpportunities();
            $results['archived_opportunities'] = $archivedOpportunities;

            // Clean up old log files
            $logCleanup = $this->cleanupLogFiles();
            $results['log_cleanup'] = $logCleanup;

            // Clean up temporary files
            $tempCleanup = $this->cleanupTempFiles();
            $results['temp_cleanup'] = $tempCleanup;

            Logger::getInstance()->info('Maintenance completed', $results);

        } catch (\Exception $e) {
            Logger::getInstance()->error('Maintenance failed', [
                'error' => $e->getMessage()
            ]);
            $results['error'] = $e->getMessage();
        }

        return $results;
    }

    /**
     * Clean up old log files
     */
    private function cleanupLogFiles(): array
    {
        $logDir = $this->config['paths']['logs'];
        $maxFiles = $this->config['logging']['max_files'] ?? 7;
        $maxAge = 7 * 24 * 3600; // 7 days in seconds
        
        $cleaned = 0;
        
        if (is_dir($logDir)) {
            $files = glob($logDir . '/app.log.*');
            $files = array_filter($files, function($file) use ($maxAge) {
                return filemtime($file) < (time() - $maxAge);
            });
            
            foreach ($files as $file) {
                if (unlink($file)) {
                    $cleaned++;
                }
            }
        }
        
        return ['files_removed' => $cleaned];
    }

    /**
     * Clean up temporary files
     */
    private function cleanupTempFiles(): array
    {
        $tempDir = $this->config['paths']['temp'];
        $maxAge = 24 * 3600; // 24 hours
        
        $cleaned = 0;
        $size = 0;
        
        if (is_dir($tempDir)) {
            $files = glob($tempDir . '/rate_limit_*');
            
            foreach ($files as $file) {
                if (filemtime($file) < (time() - $maxAge)) {
                    $size += filesize($file);
                    if (unlink($file)) {
                        $cleaned++;
                    }
                }
            }
        }
        
        return [
            'files_removed' => $cleaned,
            'bytes_freed' => $size
        ];
    }
}