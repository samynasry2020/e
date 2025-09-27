<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use App\Utils\Database;
use App\Utils\Logger;
use App\Utils\Router;
use App\Utils\Security;

class AuthController
{
    /**
     * Show login form
     */
    public function showLogin(): void
    {
        // Redirect if already logged in
        if (self::isLoggedIn()) {
            Router::redirect('/dashboard');
            return;
        }

        $this->renderView('auth/login', [
            'title' => 'Login',
            'csrf_token' => Security::generateCsrfToken(),
        ]);
    }

    /**
     * Handle login attempt
     */
    public function login(): void
    {
        // Validate CSRF token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Security::validateCsrfToken($csrfToken)) {
            $this->redirectWithError('/login', 'Invalid security token');
            return;
        }

        $email = Security::sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $mfaCode = Security::sanitizeInput($_POST['mfa_code'] ?? '');

        // Validate input
        if (empty($email) || empty($password)) {
            $this->redirectWithError('/login', 'Email and password are required');
            return;
        }

        if (!Security::validateEmail($email)) {
            $this->redirectWithError('/login', 'Invalid email format');
            return;
        }

        // Check if user is locked out
        if (Security::isUserLockedOut($email)) {
            $this->redirectWithError('/login', 'Account is temporarily locked due to multiple failed login attempts');
            return;
        }

        // Find user
        $user = User::findByEmail($email);
        if (!$user) {
            Security::recordFailedLogin($email);
            $this->redirectWithError('/login', 'Invalid email or password');
            return;
        }

        // Check if user is active
        if (!$user->isActive()) {
            $this->redirectWithError('/login', 'Account is not active');
            return;
        }

        // Verify password
        if (!$user->verifyPassword($password)) {
            Security::recordFailedLogin($email);
            $this->redirectWithError('/login', 'Invalid email or password');
            return;
        }

        // Check MFA if enabled
        if ($user->mfa_secret) {
            if (empty($mfaCode)) {
                // Show MFA form
                $this->renderView('auth/mfa', [
                    'title' => 'Two-Factor Authentication',
                    'email' => $email,
                    'csrf_token' => Security::generateCsrfToken(),
                ]);
                return;
            }

            if (!Security::verifyMfaCode($user->mfa_secret, $mfaCode)) {
                $this->redirectWithError('/login', 'Invalid two-factor authentication code');
                return;
            }
        }

        // Login successful
        self::loginUser($user);
        
        // Reset failed login attempts
        Security::resetFailedLogins($email);
        
        // Update last login
        $user->updateLastLogin();

        Logger::info('User logged in successfully', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);

        // Redirect to intended page or dashboard
        $redirectTo = $_SESSION['redirect_after_login'] ?? '/dashboard';
        unset($_SESSION['redirect_after_login']);
        
        Router::redirect($redirectTo);
    }

    /**
     * Handle logout
     */
    public function logout(): void
    {
        if (isset($_SESSION['user_id'])) {
            Logger::info('User logged out', [
                'user_id' => $_SESSION['user_id'],
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
        }

        // Destroy session
        session_destroy();
        
        // Redirect to login
        Router::redirect('/login');
    }

    /**
     * Login user and create session
     */
    private static function loginUser(User $user): void
    {
        // Regenerate session ID for security
        session_regenerate_id(true);

        // Set session variables
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_role'] = $user->role;
        $_SESSION['user_name'] = $user->getFullName();
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();

        // Store session in database
        self::storeSession($user->id);
    }

    /**
     * Store session in database
     */
    private static function storeSession(int $userId): void
    {
        $sessionId = session_id();
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

        Database::execute(
            'INSERT INTO sessions (id, user_id, ip_address, user_agent) VALUES (?, ?, ?, ?)',
            [$sessionId, $userId, $ipAddress, $userAgent]
        );
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    /**
     * Get current user
     */
    public static function getCurrentUser(): ?User
    {
        if (!self::isLoggedIn() || !isset($_SESSION['user_id'])) {
            return null;
        }

        return User::findById((int) $_SESSION['user_id']);
    }

    /**
     * Require authentication
     */
    public static function requireAuth(): void
    {
        if (!self::isLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '/';
            Router::redirect('/login');
        }
    }

    /**
     * Require specific role
     */
    public static function requireRole(string $role): void
    {
        self::requireAuth();
        
        $user = self::getCurrentUser();
        if (!$user || !$user->hasRole($role)) {
            Router::redirect('/dashboard');
        }
    }

    /**
     * Require any of the given roles
     */
    public static function requireAnyRole(array $roles): void
    {
        self::requireAuth();
        
        $user = self::getCurrentUser();
        if (!$user || !$user->hasAnyRole($roles)) {
            Router::redirect('/dashboard');
        }
    }

    /**
     * Require permission
     */
    public static function requirePermission(string $permission): void
    {
        self::requireAuth();
        
        $user = self::getCurrentUser();
        if (!$user || !$user->hasPermission($permission)) {
            Router::redirect('/dashboard');
        }
    }

    /**
     * Clean up expired sessions
     */
    public static function cleanupSessions(): void
    {
        $lifetime = Database::getConnection()->query('SELECT @@session_timeout')->fetchColumn() ?: 7200;
        
        Database::execute(
            'DELETE FROM sessions WHERE last_seen < DATE_SUB(NOW(), INTERVAL ? SECOND)',
            [$lifetime]
        );
    }

    /**
     * Render view with data
     */
    private function renderView(string $view, array $data = []): void
    {
        extract($data);
        
        $viewFile = dirname(__DIR__, 2) . "/views/{$view}.php";
        
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            throw new \RuntimeException("View not found: {$view}");
        }
    }

    /**
     * Redirect with error message
     */
    private function redirectWithError(string $url, string $message): void
    {
        $_SESSION['error'] = $message;
        Router::redirect($url);
    }
}