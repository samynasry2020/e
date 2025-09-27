<?php

declare(strict_types=1);

namespace GovTribe\Core;

use GovTribe\Models\User;

class Auth
{
    private const SESSION_KEY = 'user_id';
    private const CSRF_TOKEN_KEY = 'csrf_token';
    private static ?User $currentUser = null;

    public static function attempt(string $email, string $password): bool
    {
        $user = User::findByEmail($email);
        
        if (!$user || !self::verifyPassword($password, $user['password_hash'])) {
            self::incrementLoginAttempts($email);
            return false;
        }

        if ($user['status'] !== 'Active') {
            return false;
        }

        if (self::isAccountLocked($user)) {
            return false;
        }

        self::login($user);
        self::resetLoginAttempts($user['id']);
        
        return true;
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION[self::SESSION_KEY] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['login_time'] = time();

        // Update last login
        Database::update('users', [
            'last_login' => date('Y-m-d H:i:s'),
            'login_attempts' => 0
        ], ['id' => $user['id']]);

        // Create session record
        Database::insert('sessions', [
            'id' => session_id(),
            'user_id' => $user['id'],
            'ip_address' => self::getClientIp(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        ]);

        self::$currentUser = new User($user);
    }

    public static function logout(): void
    {
        if (isset($_SESSION[self::SESSION_KEY])) {
            // Remove session from database
            Database::delete('sessions', ['id' => session_id()]);
        }

        session_unset();
        session_destroy();
        self::$currentUser = null;
    }

    public static function check(): bool
    {
        return isset($_SESSION[self::SESSION_KEY]) && self::user() !== null;
    }

    public static function user(): ?User
    {
        if (self::$currentUser !== null) {
            return self::$currentUser;
        }

        if (!isset($_SESSION[self::SESSION_KEY])) {
            return null;
        }

        $userData = User::find($_SESSION[self::SESSION_KEY]);
        if (!$userData || $userData['status'] !== 'Active') {
            self::logout();
            return null;
        }

        // Update session last seen
        Database::update('sessions', [
            'last_seen' => date('Y-m-d H:i:s')
        ], ['id' => session_id()]);

        self::$currentUser = new User($userData);
        return self::$currentUser;
    }

    public static function id(): ?int
    {
        $user = self::user();
        return $user ? $user->getId() : null;
    }

    public static function hasRole(string $role): bool
    {
        $user = self::user();
        return $user && $user->getRole() === $role;
    }

    public static function hasAnyRole(array $roles): bool
    {
        $user = self::user();
        return $user && in_array($user->getRole(), $roles);
    }

    public static function can(string $permission): bool
    {
        $user = self::user();
        if (!$user) {
            return false;
        }

        return match ($user->getRole()) {
            'Admin' => true,
            'Capture Manager' => in_array($permission, [
                'opportunities.view', 'opportunities.create', 'opportunities.update',
                'opportunities.score', 'opportunities.triage'
            ]),
            'Proposal Manager' => in_array($permission, [
                'opportunities.view', 'proposals.view', 'proposals.create',
                'proposals.update', 'submissions.create'
            ]),
            'Sales Engineer' => in_array($permission, [
                'opportunities.view', 'boms.view', 'boms.create', 'boms.update',
                'quotes.view', 'quotes.create'
            ]),
            'Vendor Manager' => in_array($permission, [
                'opportunities.view', 'suppliers.view', 'suppliers.create',
                'suppliers.update', 'rfqs.create', 'quotes.view'
            ]),
            'Accountant' => in_array($permission, [
                'opportunities.view', 'awards.view', 'awards.create',
                'awards.update', 'invoices.view', 'invoices.create'
            ]),
            'Viewer' => in_array($permission, [
                'opportunities.view', 'proposals.view', 'boms.view',
                'suppliers.view', 'awards.view', 'invoices.view'
            ]),
            default => false
        };
    }

    public static function generateCsrfToken(): string
    {
        if (!isset($_SESSION[self::CSRF_TOKEN_KEY])) {
            $_SESSION[self::CSRF_TOKEN_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::CSRF_TOKEN_KEY];
    }

    public static function verifyCsrfToken(string $token): bool
    {
        return isset($_SESSION[self::CSRF_TOKEN_KEY]) && 
               hash_equals($_SESSION[self::CSRF_TOKEN_KEY], $token);
    }

    public static function hashPassword(string $password): string
    {
        $algo = Config::get('security.password_hash_algo', PASSWORD_ARGON2ID);
        return password_hash($password, constant($algo));
    }

    private static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    private static function incrementLoginAttempts(string $email): void
    {
        $user = User::findByEmail($email);
        if ($user) {
            $attempts = $user['login_attempts'] + 1;
            $updateData = ['login_attempts' => $attempts];
            
            // Lock account after 5 failed attempts for 15 minutes
            if ($attempts >= 5) {
                $updateData['locked_until'] = date('Y-m-d H:i:s', time() + 900);
            }
            
            Database::update('users', $updateData, ['id' => $user['id']]);
        }
    }

    private static function resetLoginAttempts(int $userId): void
    {
        Database::update('users', [
            'login_attempts' => 0,
            'locked_until' => null
        ], ['id' => $userId]);
    }

    private static function isAccountLocked(array $user): bool
    {
        if (!$user['locked_until']) {
            return false;
        }
        
        return strtotime($user['locked_until']) > time();
    }

    private static function getClientIp(): string
    {
        $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = trim(explode(',', $_SERVER[$key])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
}