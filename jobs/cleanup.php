#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * System Cleanup Job
 * 
 * Performs maintenance tasks like cleaning old logs, expired sessions, etc.
 * Should be run via cron weekly: 0 2 * * 0
 */

require_once __DIR__ . '/../vendor/autoload.php';

use GovTribe\Core\{Config, Database};

// Initialize configuration
Config::load();
date_default_timezone_set(Config::get('app.timezone', 'UTC'));

// Logging function
function logMessage(string $message, string $level = 'INFO'): void {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] [{$level}] Cleanup: {$message}" . PHP_EOL;
    
    echo $logMessage;
    
    $logFile = __DIR__ . '/../storage/logs/jobs.log';
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

try {
    logMessage("Starting system cleanup job");
    
    // Clean expired sessions
    $expiredSessions = cleanExpiredSessions();
    logMessage("Cleaned {$expiredSessions} expired sessions");
    
    // Clean old audit logs (keep 1 year)
    $oldAuditLogs = cleanOldAuditLogs();
    logMessage("Cleaned {$oldAuditLogs} old audit log entries");
    
    // Clean old opportunity changes (keep 6 months)
    $oldChanges = cleanOldOpportunityChanges();
    logMessage("Cleaned {$oldChanges} old opportunity changes");
    
    // Rotate log files
    $rotatedLogs = rotateLogFiles();
    logMessage("Rotated {$rotatedLogs} log files");
    
    // Clean orphaned files
    $orphanedFiles = cleanOrphanedFiles();
    logMessage("Cleaned {$orphanedFiles} orphaned files");
    
    // Clean old backups
    $oldBackups = cleanOldBackups();
    logMessage("Cleaned {$oldBackups} old backup files");
    
    // Optimize database tables
    optimizeDatabaseTables();
    logMessage("Optimized database tables");
    
    logMessage("System cleanup job completed");
    
} catch (\Exception $e) {
    logMessage("Job failed with exception: " . $e->getMessage(), 'ERROR');
    exit(1);
}

/**
 * Remove expired sessions (older than 30 days)
 */
function cleanExpiredSessions(): int {
    $result = Database::query(
        'DELETE FROM sessions WHERE last_seen < DATE_SUB(NOW(), INTERVAL 30 DAY)'
    );
    
    return $result->rowCount();
}

/**
 * Clean old audit log entries (keep 1 year)
 */
function cleanOldAuditLogs(): int {
    $result = Database::query(
        'DELETE FROM audit_log WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR)'
    );
    
    return $result->rowCount();
}

/**
 * Clean old opportunity changes (keep 6 months)
 */
function cleanOldOpportunityChanges(): int {
    $result = Database::query(
        'DELETE FROM opportunity_changes WHERE changed_at < DATE_SUB(NOW(), INTERVAL 6 MONTH)'
    );
    
    return $result->rowCount();
}

/**
 * Rotate log files (keep last 4 weeks)
 */
function rotateLogFiles(): int {
    $logsDir = __DIR__ . '/../storage/logs';
    $rotated = 0;
    
    if (!is_dir($logsDir)) {
        return 0;
    }
    
    $logFiles = glob($logsDir . '/*.log');
    
    foreach ($logFiles as $logFile) {
        if (filesize($logFile) > 10 * 1024 * 1024) { // 10MB
            $rotatedFile = $logFile . '.' . date('Y-m-d');
            if (rename($logFile, $rotatedFile)) {
                touch($logFile); // Create new empty log file
                $rotated++;
            }
        }
    }
    
    // Remove old rotated logs (older than 4 weeks)
    $oldLogs = glob($logsDir . '/*.log.*');
    foreach ($oldLogs as $oldLog) {
        if (filemtime($oldLog) < strtotime('-4 weeks')) {
            unlink($oldLog);
        }
    }
    
    return $rotated;
}

/**
 * Clean orphaned files (files not referenced in database)
 */
function cleanOrphanedFiles(): int {
    $filesRoot = Config::get('files.root');
    if (!is_dir($filesRoot)) {
        return 0;
    }
    
    $cleaned = 0;
    
    // Get all file paths from database
    $dbFiles = Database::fetchAll('SELECT DISTINCT path FROM files WHERE path IS NOT NULL');
    $dbPaths = array_column($dbFiles, 'path');
    
    // Scan filesystem
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($filesRoot, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $relativePath = str_replace($filesRoot . '/', '', $file->getPathname());
            
            // Skip if file is in database
            if (in_array($relativePath, $dbPaths)) {
                continue;
            }
            
            // Skip recent files (less than 24 hours old)
            if ($file->getMTime() > strtotime('-24 hours')) {
                continue;
            }
            
            // Remove orphaned file
            if (unlink($file->getPathname())) {
                $cleaned++;
            }
        }
    }
    
    return $cleaned;
}

/**
 * Clean old backup files
 */
function cleanOldBackups(): int {
    $backupsDir = __DIR__ . '/../storage/backups';
    if (!is_dir($backupsDir)) {
        return 0;
    }
    
    $retentionDays = (int)Database::fetchOne(
        'SELECT value FROM settings WHERE `key` = "backup_retention_days"'
    )['value'] ?? 30;
    
    $cleaned = 0;
    $backupFiles = glob($backupsDir . '/*');
    
    foreach ($backupFiles as $backupFile) {
        if (filemtime($backupFile) < strtotime("-{$retentionDays} days")) {
            if (unlink($backupFile)) {
                $cleaned++;
            }
        }
    }
    
    return $cleaned;
}

/**
 * Optimize database tables
 */
function optimizeDatabaseTables(): void {
    $tables = [
        'opportunities', 'opportunity_naics', 'opportunity_changes',
        'documents', 'files', 'audit_log', 'sessions'
    ];
    
    foreach ($tables as $table) {
        try {
            Database::query("OPTIMIZE TABLE {$table}");
        } catch (\Exception $e) {
            logMessage("Failed to optimize table {$table}: " . $e->getMessage(), 'WARNING');
        }
    }
}