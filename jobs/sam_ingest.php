#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * SAM.gov Ingestion Job
 * 
 * This script runs automated ingestion of opportunities from SAM.gov API
 * Should be run via cron every 12 hours: 0 */12 * * *
 */

require_once __DIR__ . '/../vendor/autoload.php';

use GovTribe\Core\Config;
use GovTribe\Services\IngestionService;

// Initialize configuration
Config::load();

// Set timezone
date_default_timezone_set(Config::get('app.timezone', 'UTC'));

// Logging function
function logMessage(string $message, string $level = 'INFO'): void {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] [{$level}] SAM Ingest: {$message}" . PHP_EOL;
    
    echo $logMessage;
    
    $logFile = __DIR__ . '/../storage/logs/jobs.log';
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

// Error handler
set_error_handler(function($severity, $message, $file, $line) {
    logMessage("PHP Error: {$message} in {$file}:{$line}", 'ERROR');
});

// Exception handler
set_exception_handler(function($exception) {
    logMessage("Uncaught exception: " . $exception->getMessage(), 'ERROR');
    exit(1);
});

try {
    logMessage("Starting SAM.gov ingestion job");
    
    // Create ingestion service
    $ingestionService = new IngestionService();
    
    // Run ingestion with default filters
    $result = $ingestionService->ingestOpportunities();
    
    if ($result['success']) {
        $summary = $result['summary'];
        logMessage(sprintf(
            "Ingestion completed successfully - Created: %d, Updated: %d, Rejected: %d, Total fetched: %d",
            $summary['created'],
            $summary['updated'],
            $summary['rejected'],
            $summary['total_fetched']
        ));
        
        // Send success notification if configured
        if ($summary['created'] > 0 || $summary['updated'] > 0) {
            sendNotification("New opportunities ingested", $result);
        }
        
    } else {
        logMessage("Ingestion failed: " . ($result['error'] ?? 'Unknown error'), 'ERROR');
        
        // Send failure notification
        sendFailureNotification($result['error'] ?? 'Unknown error');
        exit(1);
    }
    
} catch (\Exception $e) {
    logMessage("Job failed with exception: " . $e->getMessage(), 'ERROR');
    sendFailureNotification($e->getMessage());
    exit(1);
}

logMessage("SAM.gov ingestion job completed");

/**
 * Send success notification email
 */
function sendNotification(string $subject, array $result): void {
    try {
        $emailService = new \GovTribe\Services\EmailService();
        
        $body = "SAM.gov ingestion completed successfully:\n\n";
        $body .= "Created: " . $result['summary']['created'] . "\n";
        $body .= "Updated: " . $result['summary']['updated'] . "\n";
        $body .= "Rejected: " . $result['summary']['rejected'] . "\n";
        $body .= "Total fetched: " . $result['summary']['total_fetched'] . "\n\n";
        $body .= "Timestamp: " . date('Y-m-d H:i:s') . "\n";
        
        $emailService->sendSystemNotification($subject, $body);
        
    } catch (\Exception $e) {
        logMessage("Failed to send notification: " . $e->getMessage(), 'WARNING');
    }
}

/**
 * Send failure notification email
 */
function sendFailureNotification(string $error): void {
    try {
        $emailService = new \GovTribe\Services\EmailService();
        
        $subject = "SAM.gov Ingestion Failed";
        $body = "SAM.gov ingestion job failed:\n\n";
        $body .= "Error: " . $error . "\n";
        $body .= "Timestamp: " . date('Y-m-d H:i:s') . "\n";
        $body .= "Server: " . gethostname() . "\n\n";
        $body .= "Please check the system logs and resolve the issue.";
        
        $emailService->sendSystemAlert($subject, $body);
        
    } catch (\Exception $e) {
        logMessage("Failed to send failure notification: " . $e->getMessage(), 'WARNING');
    }
}