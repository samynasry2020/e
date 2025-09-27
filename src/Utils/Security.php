<?php

declare(strict_types=1);

namespace App\Utils;

class Security
{
    /**
     * Generate CSRF token
     */
    public static function generateCsrfToken(): string
    {
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time();
        }

        // Regenerate token if it's expired
        $lifetime = Config::get('security.csrf_token_lifetime', 3600);
        if (time() - $_SESSION['csrf_token_time'] > $lifetime) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time();
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Validate CSRF token
     */
    public static function validateCsrfToken(string $token): bool
    {
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
            return false;
        }

        // Check if token is expired
        $lifetime = Config::get('security.csrf_token_lifetime', 3600);
        if (time() - $_SESSION['csrf_token_time'] > $lifetime) {
            unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Hash password using Argon2ID
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536, // 64 MB
            'time_cost' => 4,       // 4 iterations
            'threads' => 3,         // 3 threads
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
     * Generate secure random string
     */
    public static function generateRandomString(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Sanitize input string
     */
    public static function sanitizeInput(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate email address
     */
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate file upload
     */
    public static function validateFileUpload(array $file): array
    {
        $errors = [];

        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'File upload failed with error code: ' . $file['error'];
            return $errors;
        }

        // Check file size
        $maxSize = Config::get('upload.max_size', 10485760); // 10MB
        if ($file['size'] > $maxSize) {
            $errors[] = 'File size exceeds maximum allowed size of ' . number_format($maxSize / 1024 / 1024, 1) . 'MB';
        }

        // Check file type
        $allowedTypes = Config::get('upload.allowed_types', ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt']);
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($extension, $allowedTypes)) {
            $errors[] = 'File type not allowed. Allowed types: ' . implode(', ', $allowedTypes);
        }

        // Check MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowedMimes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'txt' => 'text/plain',
        ];

        if (isset($allowedMimes[$extension]) && $mimeType !== $allowedMimes[$extension]) {
            $errors[] = 'File MIME type does not match file extension';
        }

        return $errors;
    }

    /**
     * Generate file hash for deduplication
     */
    public static function generateFileHash(string $filePath): string
    {
        return hash_file('sha256', $filePath);
    }

    /**
     * Secure file upload with validation and deduplication
     */
    public static function secureFileUpload(array $file, string $destinationDir = null): array
    {
        $errors = self::validateFileUpload($file);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Use default upload path if not specified
        if ($destinationDir === null) {
            $destinationDir = Config::get('upload.path', '/workspace/uploads');
        }

        // Create destination directory if it doesn't exist
        if (!is_dir($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }

        // Generate unique filename
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $hash = self::generateFileHash($file['tmp_name']);
        $filename = $hash . '.' . $extension;
        $destinationPath = $destinationDir . '/' . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $destinationPath)) {
            return [
                'success' => true,
                'filename' => $filename,
                'path' => $destinationPath,
                'hash' => $hash,
                'size' => $file['size'],
                'original_name' => $file['name'],
                'mime_type' => mime_content_type($destinationPath),
            ];
        } else {
            return ['success' => false, 'errors' => ['Failed to move uploaded file']];
        }
    }

    /**
     * Check if user is locked out due to failed login attempts
     */
    public static function isUserLockedOut(string $email): bool
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('
            SELECT locked_until 
            FROM users 
            WHERE email = ? AND locked_until > NOW()
        ');
        $stmt->execute([$email]);
        
        return $stmt->fetchColumn() !== false;
    }

    /**
     * Record failed login attempt
     */
    public static function recordFailedLogin(string $email): void
    {
        $db = Database::getConnection();
        $lockoutTime = Config::get('security.login_lockout_time', 900); // 15 minutes
        $maxAttempts = Config::get('security.login_max_attempts', 5);

        $stmt = $db->prepare('
            UPDATE users 
            SET login_attempts = login_attempts + 1,
                locked_until = CASE 
                    WHEN login_attempts + 1 >= ? THEN DATE_ADD(NOW(), INTERVAL ? SECOND)
                    ELSE locked_until
                END
            WHERE email = ?
        ');
        $stmt->execute([$maxAttempts, $lockoutTime, $email]);
    }

    /**
     * Reset failed login attempts
     */
    public static function resetFailedLogins(string $email): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('
            UPDATE users 
            SET login_attempts = 0, locked_until = NULL 
            WHERE email = ?
        ');
        $stmt->execute([$email]);
    }

    /**
     * Generate MFA secret
     */
    public static function generateMfaSecret(): string
    {
        return base32_encode(random_bytes(20));
    }

    /**
     * Generate MFA QR code data
     */
    public static function generateMfaQrData(string $email, string $secret): string
    {
        $issuer = Config::get('app.name', 'GovTribe Platform');
        return "otpauth://totp/{$email}?secret={$secret}&issuer={$issuer}";
    }

    /**
     * Verify MFA code
     */
    public static function verifyMfaCode(string $secret, string $code): bool
    {
        $timeSlice = floor(time() / 30);
        $secretKey = base32_decode($secret);

        // Check current time slice and ±1 time slice for clock skew
        for ($i = -1; $i <= 1; $i++) {
            $calculatedCode = self::calculateTotpCode($secretKey, $timeSlice + $i);
            if (hash_equals($calculatedCode, $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Calculate TOTP code
     */
    private static function calculateTotpCode(string $secretKey, int $timeSlice): string
    {
        $time = pack('N*', 0, $timeSlice);
        $hash = hash_hmac('sha1', $time, $secretKey, true);
        $offset = ord($hash[19]) & 0xf;
        $code = (
            ((ord($hash[$offset]) & 0x7f) << 24) |
            ((ord($hash[$offset + 1]) & 0xff) << 16) |
            ((ord($hash[$offset + 2]) & 0xff) << 8) |
            (ord($hash[$offset + 3]) & 0xff)
        ) % 1000000;

        return str_pad((string) $code, 6, '0', STR_PAD_LEFT);
    }
}

/**
 * Base32 encoding/decoding functions
 */
function base32_encode(string $data): string
{
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $output = '';
    $v = 0;
    $vbits = 0;

    for ($i = 0; $i < strlen($data); $i++) {
        $v <<= 8;
        $v += ord($data[$i]);
        $vbits += 8;

        while ($vbits >= 5) {
            $vbits -= 5;
            $output .= $alphabet[($v >> $vbits) & 31];
        }
    }

    if ($vbits > 0) {
        $v <<= (5 - $vbits);
        $output .= $alphabet[$v & 31];
    }

    return $output;
}

function base32_decode(string $data): string
{
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $output = '';
    $v = 0;
    $vbits = 0;

    for ($i = 0; $i < strlen($data); $i++) {
        $v <<= 5;
        $v += strpos($alphabet, $data[$i]);
        $vbits += 5;

        if ($vbits >= 8) {
            $vbits -= 8;
            $output .= chr(($v >> $vbits) & 255);
        }
    }

    return $output;
}