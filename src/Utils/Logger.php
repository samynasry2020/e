<?php

declare(strict_types=1);

namespace App\Utils;

class Logger
{
    private static array $levels = [
        'debug' => 0,
        'info' => 1,
        'warning' => 2,
        'error' => 3,
        'critical' => 4,
    ];

    /**
     * Log a debug message
     */
    public static function debug(string $message, array $context = []): void
    {
        self::log('debug', $message, $context);
    }

    /**
     * Log an info message
     */
    public static function info(string $message, array $context = []): void
    {
        self::log('info', $message, $context);
    }

    /**
     * Log a warning message
     */
    public static function warning(string $message, array $context = []): void
    {
        self::log('warning', $message, $context);
    }

    /**
     * Log an error message
     */
    public static function error(string $message, array $context = []): void
    {
        self::log('error', $message, $context);
    }

    /**
     * Log a critical message
     */
    public static function critical(string $message, array $context = []): void
    {
        self::log('critical', $message, $context);
    }

    /**
     * Write log entry
     */
    public static function log(string $level, string $message, array $context = []): void
    {
        $minLevel = Config::get('logging.level', 'info');
        
        if (self::$levels[$level] < self::$levels[$minLevel]) {
            return;
        }

        $timestamp = date('Y-m-d H:i:s');
        $contextStr = empty($context) ? '' : ' ' . json_encode($context);
        $logEntry = "[{$timestamp}] {$level}: {$message}{$contextStr}" . PHP_EOL;

        $logFile = self::getLogFile();
        $logDir = dirname($logFile);

        // Create log directory if it doesn't exist
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        // Write to log file
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);

        // Rotate log file if it's too large (10MB)
        if (file_exists($logFile) && filesize($logFile) > 10 * 1024 * 1024) {
            self::rotateLogFile($logFile);
        }

        // Also output to stderr for critical errors
        if ($level === 'critical') {
            error_log($logEntry);
        }
    }

    /**
     * Get current log file path
     */
    private static function getLogFile(): string
    {
        $logPath = Config::get('logging.path', '/workspace/storage/logs');
        $date = date('Y-m-d');
        return $logPath . "/app-{$date}.log";
    }

    /**
     * Rotate log file when it gets too large
     */
    private static function rotateLogFile(string $logFile): void
    {
        $timestamp = date('Y-m-d-H-i-s');
        $rotatedFile = str_replace('.log', "-{$timestamp}.log", $logFile);
        
        if (rename($logFile, $rotatedFile)) {
            self::info('Log file rotated', ['from' => $logFile, 'to' => $rotatedFile]);
        }

        // Clean up old rotated files (keep last 7 days)
        self::cleanupOldLogs(dirname($logFile));
    }

    /**
     * Clean up old log files
     */
    private static function cleanupOldLogs(string $logDir): void
    {
        $files = glob($logDir . '/app-*.log');
        $cutoff = time() - (7 * 24 * 60 * 60); // 7 days ago

        foreach ($files as $file) {
            if (filemtime($file) < $cutoff) {
                unlink($file);
                self::info('Cleaned up old log file', ['file' => $file]);
            }
        }
    }

    /**
     * Get recent log entries
     */
    public static function getRecentEntries(int $limit = 100): array
    {
        $logFile = self::getLogFile();
        $entries = [];

        if (!file_exists($logFile)) {
            return $entries;
        }

        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $lines = array_reverse(array_slice($lines, -$limit));

        foreach ($lines as $line) {
            if (preg_match('/^\[([^\]]+)\] (\w+): (.+)$/', $line, $matches)) {
                $entries[] = [
                    'timestamp' => $matches[1],
                    'level' => $matches[2],
                    'message' => $matches[3],
                ];
            }
        }

        return $entries;
    }

    /**
     * Clear all log files
     */
    public static function clearLogs(): void
    {
        $logPath = Config::get('logging.path', '/workspace/storage/logs');
        $files = glob($logPath . '/app-*.log');

        foreach ($files as $file) {
            unlink($file);
        }

        self::info('All log files cleared');
    }
}