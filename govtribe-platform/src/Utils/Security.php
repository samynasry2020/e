<?php

declare(strict_types=1);

namespace GovTribe\Utils;

use RuntimeException;

/**
 * Security utilities for authentication, CSRF, and data protection
 */
class Security
{
    private static string $appKey = '';
    private static string $sessionKey = '';

    /**
     * Initialize security with encryption keys
     */
    public static function init(string $appKey, string $sessionKey): void
    {
        self::$appKey = $appKey;
        self::$sessionKey = $sessionKey;
        
        if (strlen($appKey) < 32) {
            throw new RuntimeException('APP_KEY must be at least 32 characters long');
        }
        
        if (strlen($sessionKey) < 32) {
            throw new RuntimeException('SESSION_ENCRYPTION_KEY must be at least 32 characters long');
        }
    }

    /**
     * Hash password using Argon2ID
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3,
        ]);
    }

    /**
     * Verify password against hash
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Generate CSRF token
     */
    public static function generateCsrfToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['_csrf_token_time'] = time();
        }

        return $_SESSION['_csrf_token'];
    }

    /**
     * Verify CSRF token
     */
    public static function verifyCsrfToken(string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['_csrf_token']) || !isset($_SESSION['_csrf_token_time'])) {
            return false;
        }

        // Token expires after 1 hour
        if (time() - $_SESSION['_csrf_token_time'] > 3600) {
            unset($_SESSION['_csrf_token'], $_SESSION['_csrf_token_time']);
            return false;
        }

        return hash_equals($_SESSION['_csrf_token'], $token);
    }

    /**
     * Generate random string
     */
    public static function generateRandomString(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Encrypt data
     */
    public static function encrypt(string $data): string
    {
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', self::$appKey, 0, $iv);
        
        if ($encrypted === false) {
            throw new RuntimeException('Encryption failed');
        }

        return base64_encode($iv . $encrypted);
    }

    /**
     * Decrypt data
     */
    public static function decrypt(string $encryptedData): string
    {
        $data = base64_decode($encryptedData);
        if ($data === false) {
            throw new RuntimeException('Invalid encrypted data');
        }

        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        
        $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', self::$appKey, 0, $iv);
        
        if ($decrypted === false) {
            throw new RuntimeException('Decryption failed');
        }

        return $decrypted;
    }

    /**
     * Hash file content with SHA-256
     */
    public static function hashFile(string $filePath): string
    {
        if (!file_exists($filePath)) {
            throw new RuntimeException("File not found: {$filePath}");
        }

        $hash = hash_file('sha256', $filePath);
        if ($hash === false) {
            throw new RuntimeException('File hashing failed');
        }

        return $hash;
    }

    /**
     * Sanitize input for XSS prevention
     */
    public static function sanitizeInput(string $input): string
    {
        return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Sanitize output for display
     */
    public static function sanitizeOutput(string $output): string
    {
        return htmlspecialchars($output, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Validate email address
     */
    public static function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate file type against whitelist
     */
    public static function isValidFileType(string $mimeType, array $allowedTypes): bool
    {
        return in_array($mimeType, $allowedTypes, true);
    }

    /**
     * Generate secure session ID
     */
    public static function generateSessionId(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Start secure session
     */
    public static function startSecureSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        // Configure secure session settings
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_secure', '1');
        ini_set('session.cookie_samesite', 'Strict');
        ini_set('session.use_strict_mode', '1');
        ini_set('session.cookie_lifetime', '0'); // Session cookie (not persistent)

        session_start();

        // Regenerate session ID periodically
        if (!isset($_SESSION['_last_regeneration'])) {
            $_SESSION['_last_regeneration'] = time();
        } elseif (time() - $_SESSION['_last_regeneration'] > 300) { // 5 minutes
            session_regenerate_id(true);
            $_SESSION['_last_regeneration'] = time();
        }
    }

    /**
     * Destroy session and clear all data
     */
    public static function destroySession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params['path'],
                    $params['domain'],
                    $params['secure'],
                    $params['httponly']
                );
            }
            
            session_destroy();
        }
    }

    /**
     * Get client IP address
     */
    public static function getClientIp(): string
    {
        $headers = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ips = explode(',', $_SERVER[$header]);
                $ip = trim($ips[0]);
                
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Get user agent
     */
    public static function getUserAgent(): string
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    }

    /**
     * Rate limiting check
     */
    public static function isRateLimited(string $key, int $maxAttempts = 5, int $windowSeconds = 900): bool
    {
        $cacheFile = sys_get_temp_dir() . '/rate_limit_' . md5($key);
        
        if (!file_exists($cacheFile)) {
            return false;
        }

        $data = json_decode(file_get_contents($cacheFile), true);
        if (!$data || !isset($data['attempts'], $data['first_attempt'])) {
            return false;
        }

        // Reset if window has passed
        if (time() - $data['first_attempt'] > $windowSeconds) {
            unlink($cacheFile);
            return false;
        }

        return $data['attempts'] >= $maxAttempts;
    }

    /**
     * Record rate limit attempt
     */
    public static function recordRateLimitAttempt(string $key): void
    {
        $cacheFile = sys_get_temp_dir() . '/rate_limit_' . md5($key);
        
        $data = [
            'attempts' => 1,
            'first_attempt' => time(),
            'last_attempt' => time(),
        ];

        if (file_exists($cacheFile)) {
            $existing = json_decode(file_get_contents($cacheFile), true);
            if ($existing && isset($existing['attempts'], $existing['first_attempt'])) {
                // Reset if window has passed
                if (time() - $existing['first_attempt'] > 900) { // 15 minutes
                    $data = [
                        'attempts' => 1,
                        'first_attempt' => time(),
                        'last_attempt' => time(),
                    ];
                } else {
                    $data = [
                        'attempts' => $existing['attempts'] + 1,
                        'first_attempt' => $existing['first_attempt'],
                        'last_attempt' => time(),
                    ];
                }
            }
        }

        file_put_contents($cacheFile, json_encode($data));
    }
}