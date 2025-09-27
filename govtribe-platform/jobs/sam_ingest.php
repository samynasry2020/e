<?php

declare(strict_types=1);

/**
 * SAM.gov Ingestion Job
 * Runs every 12 hours via cron to fetch new opportunities
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
    echo "Starting SAM.gov ingestion job at " . date('Y-m-d H:i:s') . "\n";
    
    // Initialize application
    $app = App::initialize($config);
    $logger = Logger::getInstance();
    
    // Get services
    $ingestionService = $app->getIngestionService();
    $scoringService = $app->getScoringService();
    
    // Run ingestion
    $results = $ingestionService->ingest();
    
    echo "Ingestion completed:\n";
    echo "  Fetched: " . $results['fetched'] . "\n";
    echo "  Created: " . $results['created'] . "\n";
    echo "  Updated: " . $results['updated'] . "\n";
    echo "  Rejected: " . $results['rejected'] . "\n";
    
    if (!empty($results['errors'])) {
        echo "  Errors: " . count($results['errors']) . "\n";
        foreach ($results['errors'] as $error) {
            echo "    - " . $error . "\n";
        }
    }
    
    // Score new opportunities
    if ($results['created'] > 0 || $results['updated'] > 0) {
        echo "Scoring new/updated opportunities...\n";
        $scoringResults = $scoringService->scoreAllUnscored();
        echo "Scored: " . $scoringResults['scored'] . " opportunities\n";
        
        if (!empty($scoringResults['errors'])) {
            echo "Scoring errors: " . count($scoringResults['errors']) . "\n";
        }
    }
    
    echo "Job completed successfully at " . date('Y-m-d H:i:s') . "\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    
    // Log error
    if (isset($logger)) {
        $logger->error('SAM ingestion job failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
    
    exit(1);
}