<?php

declare(strict_types=1);

/**
 * Cleanup Job
 * Runs weekly to clean up old data, logs, and temporary files
 */

// Set timezone
date_default_timezone_set('America/Los_Angeles');

// Simple autoloader
spl_autoload_register(function ($class) {
    $prefix = 'GovTribe\\';
    $base_dir = __DIR__ . '/../src/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            [$key, $value] = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Load configuration
$config = require __DIR__ . '/../config/app.php';

// Initialize application
use GovTribe\App;
use GovTribe\Utils\Logger;

try {
    echo "Starting cleanup job at " . date('Y-m-d H:i:s') . "\n";
    
    // Initialize application
    $app = App::initialize($config);
    $logger = Logger::getInstance();
    
    // Run maintenance tasks
    $results = $app->runMaintenance();
    
    echo "Cleanup completed:\n";
    foreach ($results as $task => $result) {
        if (is_array($result)) {
            echo "  {$task}:\n";
            foreach ($result as $key => $value) {
                echo "    {$key}: {$value}\n";
            }
        } else {
            echo "  {$task}: {$result}\n";
        }
    }
    
    echo "Job completed successfully at " . date('Y-m-d H:i:s') . "\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    
    // Log error
    if (isset($logger)) {
        $logger->error('Cleanup job failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
    
    exit(1);
}