#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Daily Email Digest Job
 * 
 * Sends daily digest emails to users with relevant opportunities and updates
 * Should be run via cron daily at 7:30 AM: 30 7 * * *
 */

require_once __DIR__ . '/../vendor/autoload.php';

use GovTribe\Core\{Config, Database};
use GovTribe\Services\EmailService;

// Initialize configuration
Config::load();
date_default_timezone_set(Config::get('app.timezone', 'UTC'));

// Logging function
function logMessage(string $message, string $level = 'INFO'): void {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] [{$level}] Email Digest: {$message}" . PHP_EOL;
    
    echo $logMessage;
    
    $logFile = __DIR__ . '/../storage/logs/jobs.log';
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

try {
    logMessage("Starting daily email digest job");
    
    // Get digest data
    $digestData = getDigestData();
    
    // Get users who should receive digest
    $users = getDigestUsers();
    
    if (empty($users)) {
        logMessage("No users configured to receive digest");
        exit(0);
    }
    
    $emailService = new EmailService();
    $sentCount = 0;
    
    foreach ($users as $user) {
        try {
            // Personalize digest for user role
            $personalizedData = personalizeDigest($digestData, $user);
            
            // Skip if no relevant content
            if (isDigestEmpty($personalizedData)) {
                continue;
            }
            
            // Send digest email
            $emailService->sendDigest($user, $personalizedData);
            $sentCount++;
            
            logMessage("Sent digest to: " . $user['email']);
            
        } catch (\Exception $e) {
            logMessage("Failed to send digest to {$user['email']}: " . $e->getMessage(), 'ERROR');
        }
    }
    
    logMessage("Email digest job completed - Sent: {$sentCount}");
    
} catch (\Exception $e) {
    logMessage("Job failed with exception: " . $e->getMessage(), 'ERROR');
    exit(1);
}

/**
 * Get digest data for the last 24 hours
 */
function getDigestData(): array {
    $data = [];
    
    // New opportunities (last 24h)
    $data['new_opportunities'] = Database::fetchAll(
        'SELECT o.*, a.name as agency_name 
         FROM opportunities o 
         LEFT JOIN agencies a ON o.agency_id = a.id 
         WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR) 
         AND o.active = 1 
         ORDER BY o.score DESC, o.created_at DESC 
         LIMIT 10'
    );
    
    // Updated opportunities (last 24h)
    $data['updated_opportunities'] = Database::fetchAll(
        'SELECT o.*, a.name as agency_name 
         FROM opportunities o 
         LEFT JOIN agencies a ON o.agency_id = a.id 
         WHERE o.updated_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR) 
         AND o.updated_at != o.created_at 
         AND o.active = 1 
         ORDER BY o.updated_at DESC 
         LIMIT 5'
    );
    
    // Opportunities due soon (next 7 days)
    $data['due_soon'] = Database::fetchAll(
        'SELECT o.*, a.name as agency_name 
         FROM opportunities o 
         LEFT JOIN agencies a ON o.agency_id = a.id 
         WHERE o.due_at BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY) 
         AND o.status IN ("New", "Review", "Pursue") 
         AND o.active = 1 
         ORDER BY o.due_at ASC 
         LIMIT 10'
    );
    
    // High-scoring opportunities needing review
    $data['high_score_review'] = Database::fetchAll(
        'SELECT o.*, a.name as agency_name 
         FROM opportunities o 
         LEFT JOIN agencies a ON o.agency_id = a.id 
         WHERE o.score >= 70 
         AND o.status = "New" 
         AND o.active = 1 
         ORDER BY o.score DESC, o.created_at DESC 
         LIMIT 5'
    );
    
    // Recent awards
    $data['recent_awards'] = Database::fetchAll(
        'SELECT aw.*, o.title as opportunity_title 
         FROM awards aw 
         INNER JOIN opportunities o ON aw.opportunity_id = o.id 
         WHERE aw.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
         ORDER BY aw.created_at DESC 
         LIMIT 5'
    );
    
    // Pipeline summary
    $data['pipeline_summary'] = Database::fetchAll(
        'SELECT status, COUNT(*) as count 
         FROM opportunities 
         WHERE active = 1 
         GROUP BY status 
         ORDER BY FIELD(status, "New", "Review", "Pursue", "No-Bid", "Awarded", "Lost")'
    );
    
    return $data;
}

/**
 * Get users who should receive digest emails
 */
function getDigestUsers(): array {
    return Database::fetchAll(
        'SELECT * FROM users 
         WHERE status = "Active" 
         AND role IN ("Admin", "Capture Manager", "Proposal Manager") 
         ORDER BY role, email'
    );
}

/**
 * Personalize digest content based on user role
 */
function personalizeDigest(array $data, array $user): array {
    $personalized = $data;
    
    // Filter content based on role permissions
    switch ($user['role']) {
        case 'Proposal Manager':
            // Focus on opportunities in Pursue status and proposals
            $personalized['new_opportunities'] = array_filter(
                $data['new_opportunities'],
                fn($opp) => $opp['status'] !== 'No-Bid'
            );
            break;
            
        case 'Capture Manager':
            // Show all opportunities but emphasize scoring
            break;
            
        case 'Accountant':
            // Focus on awards and financial data
            $personalized['new_opportunities'] = array_slice($data['new_opportunities'], 0, 3);
            break;
            
        case 'Viewer':
            // Read-only summary
            $personalized['new_opportunities'] = array_slice($data['new_opportunities'], 0, 5);
            $personalized['updated_opportunities'] = [];
            break;
    }
    
    return $personalized;
}

/**
 * Check if digest has any meaningful content
 */
function isDigestEmpty(array $data): bool {
    return empty($data['new_opportunities']) && 
           empty($data['updated_opportunities']) && 
           empty($data['due_soon']) && 
           empty($data['high_score_review']) && 
           empty($data['recent_awards']);
}