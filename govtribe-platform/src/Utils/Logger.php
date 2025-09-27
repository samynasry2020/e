<?php

declare(strict_types=1);

namespace GovTribe\Utils;

use RuntimeException;

/**
 * Application logger
 */
class Logger
{
    private static array $config = [];
    private static ?Logger $instance = null;
    private string $logFile;

    private function __construct(array $config)
    {
        $this->config = $config;
        $this->logFile = $config['file'] ?? '/tmp/govtribe.log';
        
        // Ensure log directory exists
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            if (!mkdir($logDir, 0755, true)) {
                throw new RuntimeException("Cannot create log directory: {$logDir}");
            }
        }
    }

    public static function init(array $config): void
    {
        self::$config = $config;
        self::$instance = null;
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self(self::$config);
        }

        return self::$instance;
    }

    /**
     * Log a message with specified level
     */
    public function log(string $level, string $message, array $context = []): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = empty($context) ? '' : ' ' . json_encode($context);
        $logEntry = "[{$timestamp}] {$level}: {$message}{$contextStr}" . PHP_EOL;

        $this->writeToFile($logEntry);
        $this->rotateIfNeeded();
    }

    /**
     * Log emergency message
     */
    public function emergency(string $message, array $context = []): void
    {
        $this->log('EMERGENCY', $message, $context);
    }

    /**
     * Log alert message
     */
    public function alert(string $message, array $context = []): void
    {
        $this->log('ALERT', $message, $context);
    }

    /**
     * Log critical message
     */
    public function critical(string $message, array $context = []): void
    {
        $this->log('CRITICAL', $message, $context);
    }

    /**
     * Log error message
     */
    public function error(string $message, array $context = []): void
    {
        $this->log('ERROR', $message, $context);
    }

    /**
     * Log warning message
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log('WARNING', $message, $context);
    }

    /**
     * Log notice message
     */
    public function notice(string $message, array $context = []): void
    {
        $this->log('NOTICE', $message, $context);
    }

    /**
     * Log info message
     */
    public function info(string $message, array $context = []): void
    {
        $this->log('INFO', $message, $context);
    }

    /**
     * Log debug message
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log('DEBUG', $message, $context);
    }

    /**
     * Write log entry to file
     */
    private function writeToFile(string $logEntry): void
    {
        $result = file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
        
        if ($result === false) {
            error_log("Failed to write to log file: {$this->logFile}");
        }
    }

    /**
     * Rotate log file if it exceeds max size
     */
    private function rotateIfNeeded(): void
    {
        $maxSize = $this->config['max_size'] ?? 10 * 1024 * 1024; // 10MB
        
        if (!file_exists($this->logFile) || filesize($this->logFile) < $maxSize) {
            return;
        }

        $maxFiles = $this->config['max_files'] ?? 7;
        
        // Rotate existing files
        for ($i = $maxFiles - 1; $i >= 1; $i--) {
            $oldFile = $this->logFile . ".{$i}";
            $newFile = $this->logFile . "." . ($i + 1);
            
            if (file_exists($oldFile)) {
                rename($oldFile, $newFile);
            }
        }

        // Move current log to .1
        rename($this->logFile, $this->logFile . '.1');
        
        // Create new log file
        touch($this->logFile);
        chmod($this->logFile, 0644);
    }

    /**
     * Get recent log entries
     */
    public function getRecent(int $lines = 100): array
    {
        if (!file_exists($this->logFile)) {
            return [];
        }

        $content = file_get_contents($this->logFile);
        if ($content === false) {
            return [];
        }

        $logLines = explode(PHP_EOL, trim($content));
        return array_slice($logLines, -$lines);
    }

    /**
     * Clear log file
     */
    public function clear(): void
    {
        if (file_exists($this->logFile)) {
            file_put_contents($this->logFile, '');
        }
    }
}