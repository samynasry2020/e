<?php

declare(strict_types=1);

/**
 * Health Check Endpoint
 * 
 * Simple health check for monitoring systems
 */

header('Content-Type: application/json');

$health = [
    'status' => 'ok',
    'timestamp' => date('c'),
    'version' => '1.0.0',
    'checks' => []
];

try {
    // Include bootstrap
    require_once dirname(__DIR__) . '/src/bootstrap.php';
    
    use App\Utils\Config;
    use App\Utils\Database;
    use App\Services\SamApiService;
    
    // Check database connection
    $dbCheck = Database::testConnection();
    $health['checks']['database'] = [
        'status' => $dbCheck ? 'ok' : 'error',
        'message' => $dbCheck ? 'Connected' : 'Connection failed'
    ];
    
    if (!$dbCheck) {
        $health['status'] = 'error';
    }
    
    // Check SAM API (if configured)
    $apiKey = Config::get('sam.api_key', '');
    if (!empty($apiKey)) {
        try {
            $samApi = new SamApiService();
            $apiTest = $samApi->testConnection();
            $health['checks']['sam_api'] = [
                'status' => $apiTest['success'] ? 'ok' : 'warning',
                'message' => $apiTest['message']
            ];
        } catch (Exception $e) {
            $health['checks']['sam_api'] = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    } else {
        $health['checks']['sam_api'] = [
            'status' => 'warning',
            'message' => 'API key not configured'
        ];
    }
    
    // Check file system
    $uploadPath = Config::get('upload.path', '/workspace/uploads');
    $logPath = Config::get('logging.path', '/workspace/storage/logs');
    
    $health['checks']['filesystem'] = [
        'status' => 'ok',
        'upload_path_writable' => is_writable($uploadPath),
        'log_path_writable' => is_writable($logPath)
    ];
    
    if (!is_writable($uploadPath) || !is_writable($logPath)) {
        $health['status'] = 'warning';
    }
    
    // Check PHP version
    $health['checks']['php'] = [
        'status' => 'ok',
        'version' => PHP_VERSION,
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time')
    ];
    
    // Get basic stats
    try {
        $stats = Database::queryOne('SELECT COUNT(*) as opportunities FROM opportunities');
        $health['stats'] = [
            'opportunities' => (int) ($stats['opportunities'] ?? 0),
            'uptime' => sys_getloadavg()[0] ?? 0
        ];
    } catch (Exception $e) {
        // Ignore stats errors
    }
    
} catch (Exception $e) {
    $health['status'] = 'error';
    $health['error'] = $e->getMessage();
}

// Set HTTP status code based on health
if ($health['status'] === 'error') {
    http_response_code(503);
} elseif ($health['status'] === 'warning') {
    http_response_code(200); // Still operational
}

echo json_encode($health, JSON_PRETTY_PRINT);