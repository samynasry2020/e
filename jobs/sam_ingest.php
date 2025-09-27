#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * SAM.gov Ingestion Job
 * 
 * This script runs every 12 hours to fetch new opportunities from SAM.gov
 * Run via cron: 0 */12 * * * /usr/bin/php /path/to/jobs/sam_ingest.php
 */

// Set up environment
require_once dirname(__DIR__) . '/src/bootstrap.php';

use App\Services\IngestionService;
use App\Utils\Config;
use App\Utils\Logger;

// Set timezone
date_default_timezone_set(Config::get('app.timezone', 'America/Los_Angeles'));

// Set memory limit for large imports
ini_set('memory_limit', '512M');

// Set execution time limit (5 minutes max)
set_time_limit(300);

Logger::info('Starting SAM.gov ingestion job');

try {
    // Initialize ingestion service
    $ingestionService = new IngestionService();
    
    // Build search parameters (last 12 hours)
    $params = [
        'postedFrom' => date('m/d/Y', strtotime('-12 hours')),
        'postedTo' => date('m/d/Y'),
        'limit' => 1000, // Maximum allowed
    ];
    
    Logger::info('Ingesting opportunities', $params);
    
    // Run ingestion
    $results = $ingestionService->ingestOpportunities($params);
    
    // Log results
    Logger::info('Ingestion job completed', [
        'total_fetched' => $results['total_fetched'],
        'created' => $results['created'],
        'updated' => $results['updated'],
        'rejected' => $results['rejected'],
        'errors_count' => count($results['errors'] ?? []),
        'duration' => $results['end_time'] ?? 'unknown'
    ]);
    
    // Log errors if any
    if (!empty($results['errors'])) {
        Logger::warning('Ingestion errors encountered', [
            'errors' => $results['errors']
        ]);
    }
    
    // Check for consecutive failures
    $this->checkConsecutiveFailures();
    
    echo "Ingestion completed successfully\n";
    echo "Created: {$results['created']}\n";
    echo "Updated: {$results['updated']}\n";
    echo "Rejected: {$results['rejected']}\n";
    echo "Total fetched: {$results['total_fetched']}\n";
    
} catch (Exception $e) {
    Logger::error('Ingestion job failed', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    
    echo "Ingestion failed: " . $e->getMessage() . "\n";
    
    // Check for consecutive failures and send alert
    $this->checkConsecutiveFailures(true);
    
    exit(1);
}

Logger::info('SAM.gov ingestion job finished');

/**
 * Check for consecutive failures and send alerts
 */
function checkConsecutiveFailures(bool $isFailure = false): void
{
    $lockFile = Config::get('storage.path', '/workspace/storage') . '/ingestion_failures.lock';
    $failureCount = 0;
    
    if (file_exists($lockFile)) {
        $failureCount = (int) file_get_contents($lockFile);
    }
    
    if ($isFailure) {
        $failureCount++;
        file_put_contents($lockFile, (string) $failureCount);
        
        // Send alert after 3 consecutive failures
        if ($failureCount >= 3) {
            Logger::critical('Multiple consecutive ingestion failures detected', [
                'failure_count' => $failureCount
            ]);
            
            // TODO: Send email alert to administrators
            // EmailService::sendAlert('Ingestion Failures', "SAM.gov ingestion has failed {$failureCount} times consecutively.");
        }
    } else {
        // Reset failure count on success
        if (file_exists($lockFile)) {
            unlink($lockFile);
        }
    }
}