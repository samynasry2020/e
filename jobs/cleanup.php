#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * System Cleanup Job
 * 
 * This script runs weekly to clean up old logs, files, and sessions
 * Run via cron: 0 2 * * 0 /usr/bin/php /path/to/jobs/cleanup.php
 */

// Set up environment
require_once dirname(__DIR__) . '/src/bootstrap.php';

use App\Models\File;
use App\Utils\AuthController;
use App\Utils\Config;
use App\Utils\Logger;

// Set timezone
date_default_timezone_set(Config::get('app.timezone', 'America/Los_Angeles'));

Logger::info('Starting system cleanup job');

try {
    $cleanupStats = [
        'sessions_cleaned' => 0,
        'logs_cleaned' => 0,
        'files_cleaned' => 0,
        'backups_cleaned' => 0,
        'audit_logs_cleaned' => 0,
    ];
    
    // Clean up expired sessions
    $cleanupStats['sessions_cleaned'] = cleanupExpiredSessions();
    
    // Clean up old log files
    $cleanupStats['logs_cleaned'] = cleanupOldLogFiles();
    
    // Clean up orphaned files
    $cleanupStats['files_cleaned'] = File::cleanupOrphanedFiles();
    
    // Clean up old backups
    $cleanupStats['backups_cleaned'] = cleanupOldBackups();
    
    // Clean up old audit logs
    $cleanupStats['audit_logs_cleaned'] = cleanupOldAuditLogs();
    
    // Clean up old opportunity changes
    $cleanupStats['opportunity_changes_cleaned'] = cleanupOldOpportunityChanges();
    
    Logger::info('System cleanup job completed', $cleanupStats);
    
    echo "Cleanup completed successfully:\n";
    echo "Sessions cleaned: {$cleanupStats['sessions_cleaned']}\n";
    echo "Log files cleaned: {$cleanupStats['logs_cleaned']}\n";
    echo "Orphaned files cleaned: {$cleanupStats['files_cleaned']}\n";
    echo "Old backups cleaned: {$cleanupStats['backups_cleaned']}\n";
    echo "Audit logs cleaned: {$cleanupStats['audit_logs_cleaned']}\n";
    echo "Opportunity changes cleaned: {$cleanupStats['opportunity_changes_cleaned']}\n";
    
} catch (Exception $e) {
    Logger::error('System cleanup job failed', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    
    echo "Cleanup job failed: " . $e->getMessage() . "\n";
    exit(1);
}

Logger::info('System cleanup job finished');

/**
 * Clean up expired sessions
 */
function cleanupExpiredSessions(): int
{
    try {
        $result = Database::execute(
            'DELETE FROM sessions WHERE last_seen < DATE_SUB(NOW(), INTERVAL 30 DAY)'
        );
        
        Logger::info('Cleaned up expired sessions', ['count' => $result]);
        return $result;
        
    } catch (Exception $e) {
        Logger::error('Failed to cleanup sessions', ['error' => $e->getMessage()]);
        return 0;
    }
}

/**
 * Clean up old log files
 */
function cleanupOldLogFiles(): int
{
    try {
        $logPath = Config::get('logging.path', '/workspace/storage/logs');
        $files = glob($logPath . '/app-*.log');
        $cleaned = 0;
        $cutoff = time() - (30 * 24 * 60 * 60); // 30 days ago
        
        foreach ($files as $file) {
            if (filemtime($file) < $cutoff) {
                if (unlink($file)) {
                    $cleaned++;
                }
            }
        }
        
        Logger::info('Cleaned up old log files', ['count' => $cleaned]);
        return $cleaned;
        
    } catch (Exception $e) {
        Logger::error('Failed to cleanup log files', ['error' => $e->getMessage()]);
        return 0;
    }
}

/**
 * Clean up old backup files
 */
function cleanupOldBackups(): int
{
    try {
        $backupPath = Config::get('backup.path', '/workspace/backups');
        $retentionDays = Config::get('backup.retention_days', 30);
        
        if (!is_dir($backupPath)) {
            return 0;
        }
        
        $files = glob($backupPath . '/*');
        $cleaned = 0;
        $cutoff = time() - ($retentionDays * 24 * 60 * 60);
        
        foreach ($files as $file) {
            if (is_file($file) && filemtime($file) < $cutoff) {
                if (unlink($file)) {
                    $cleaned++;
                }
            }
        }
        
        Logger::info('Cleaned up old backup files', ['count' => $cleaned]);
        return $cleaned;
        
    } catch (Exception $e) {
        Logger::error('Failed to cleanup backup files', ['error' => $e->getMessage()]);
        return 0;
    }
}

/**
 * Clean up old audit logs
 */
function cleanupOldAuditLogs(): int
{
    try {
        $result = Database::execute(
            'DELETE FROM audit_log WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR)'
        );
        
        Logger::info('Cleaned up old audit logs', ['count' => $result]);
        return $result;
        
    } catch (Exception $e) {
        Logger::error('Failed to cleanup audit logs', ['error' => $e->getMessage()]);
        return 0;
    }
}

/**
 * Clean up old opportunity changes
 */
function cleanupOldOpportunityChanges(): int
{
    try {
        $result = Database::execute(
            'DELETE FROM opportunity_changes WHERE changed_at < DATE_SUB(NOW(), INTERVAL 6 MONTH)'
        );
        
        Logger::info('Cleaned up old opportunity changes', ['count' => $result]);
        return $result;
        
    } catch (Exception $e) {
        Logger::error('Failed to cleanup opportunity changes', ['error' => $e->getMessage()]);
        return 0;
    }
}