<?php

declare(strict_types=1);

namespace GovTribe\Services;

use GovTribe\Models\User;
use GovTribe\Models\Session;
use GovTribe\Utils\Security;
use GovTribe\Utils\Logger;

/**
 * Authentication service
 */
class AuthService
{
    private Logger $logger;
    private User $currentUser;

    public function __construct()
    {
        $this->logger = Logger::getInstance();
        $this->currentUser = null;
    }

    /**
     * Authenticate user with email and password
     */
    public function authenticate(string $email, string $password, string $ipAddress, string $userAgent): ?User
    {
        // Check rate limiting
        if (Security::isRateLimited("login_{$ipAddress}")) {
            $this->logger->warning('Login attempt blocked due to rate limiting', [
                'email' => $email,
                'ip' => $ipAddress
            ]);
            return null;
        }

        // Find user by email
        $user = User::findByEmail($email);
        
        if (!$user) {
            $this->recordFailedAttempt($email, $ipAddress);
            return null;
        }

        // Check if user is active
        if (!$user->isActive()) {
            $this->logger->warning('Login attempt for inactive user', [
                'email' => $email,
                'user_id' => $user->id,
                'ip' => $ipAddress
            ]);
            $this->recordFailedAttempt($email, $ipAddress);
            return null;
        }

        // Verify password
        if (!$user->verifyPassword($password)) {
            $this->logger->warning('Invalid password attempt', [
                'email' => $email,
                'user_id' => $user->id,
                'ip' => $ipAddress
            ]);
            $this->recordFailedAttempt($email, $ipAddress);
            return null;
        }

        // Create session
        $session = Session::createForUser($user->id, $ipAddress, $userAgent);
        
        // Start secure session
        Security::startSecureSession();
        $_SESSION['user_id'] = $user->id;
        $_SESSION['session_id'] = $session->id;
        $_SESSION['role'] = $user->getRole();
        $_SESSION['login_time'] = time();

        // Update last login
        $user->updateLastLogin();

        $this->logger->info('User logged in successfully', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->getRole(),
            'ip' => $ipAddress
        ]);

        $this->currentUser = $user;
        return $user;
    }

    /**
     * Logout current user
     */
    public function logout(): bool
    {
        if (!$this->isAuthenticated()) {
            return false;
        }

        $userId = $this->getCurrentUserId();
        $sessionId = $_SESSION['session_id'] ?? null;

        // Remove session from database
        if ($sessionId) {
            $session = Session::findById($sessionId);
            if ($session) {
                $session->delete();
            }
        }

        // Destroy PHP session
        Security::destroySession();

        $this->logger->info('User logged out', [
            'user_id' => $userId,
            'session_id' => $sessionId
        ]);

        $this->currentUser = null;
        return true;
    }

    /**
     * Check if user is authenticated
     */
    public function isAuthenticated(): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return false;
        }

        if (!isset($_SESSION['user_id'], $_SESSION['session_id'])) {
            return false;
        }

        // Verify session in database
        $session = Session::findById($_SESSION['session_id']);
        if (!$session || !$session->isValid()) {
            $this->logout();
            return false;
        }

        // Update session activity
        $session->updateLastSeen();

        return true;
    }

    /**
     * Get current authenticated user
     */
    public function getCurrentUser(): ?User
    {
        if (!$this->isAuthenticated()) {
            return null;
        }

        if ($this->currentUser === null) {
            $this->currentUser = User::find($_SESSION['user_id']);
        }

        return $this->currentUser;
    }

    /**
     * Get current user ID
     */
    public function getCurrentUserId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Check if current user has permission
     */
    public function hasPermission(string $permission): bool
    {
        $user = $this->getCurrentUser();
        return $user ? $user->can($permission) : false;
    }

    /**
     * Check if current user has role
     */
    public function hasRole(string $role): bool
    {
        $user = $this->getCurrentUser();
        return $user ? $user->hasRole($role) : false;
    }

    /**
     * Check if current user has any of the specified roles
     */
    public function hasAnyRole(array $roles): bool
    {
        $user = $this->getCurrentUser();
        return $user ? $user->hasAnyRole($roles) : false;
    }

    /**
     * Check if current user is admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('Admin');
    }

    /**
     * Require authentication (redirect if not authenticated)
     */
    public function requireAuth(): User
    {
        $user = $this->getCurrentUser();
        
        if (!$user) {
            header('Location: /login');
            exit;
        }

        return $user;
    }

    /**
     * Require specific permission
     */
    public function requirePermission(string $permission): User
    {
        $user = $this->requireAuth();
        
        if (!$user->can($permission)) {
            http_response_code(403);
            echo '<h1>403 - Forbidden</h1><p>You do not have permission to access this resource.</p>';
            exit;
        }

        return $user;
    }

    /**
     * Require admin role
     */
    public function requireAdmin(): User
    {
        $user = $this->requireAuth();
        
        if (!$user->isAdmin()) {
            http_response_code(403);
            echo '<h1>403 - Forbidden</h1><p>Administrator access required.</p>';
            exit;
        }

        return $user;
    }

    /**
     * Record failed login attempt
     */
    private function recordFailedAttempt(string $email, string $ipAddress): void
    {
        Security::recordRateLimitAttempt("login_{$ipAddress}");
        
        $this->logger->warning('Failed login attempt', [
            'email' => $email,
            'ip' => $ipAddress
        ]);
    }

    /**
     * Change user password
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword): bool
    {
        // Verify current password
        if (!$user->verifyPassword($currentPassword)) {
            return false;
        }

        // Set new password
        $user->setPassword($newPassword);
        $success = $user->save();

        if ($success) {
            $this->logger->info('User password changed', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
        }

        return $success;
    }

    /**
     * Reset user password (admin only)
     */
    public function resetPassword(User $user, string $newPassword): bool
    {
        $user->setPassword($newPassword);
        $success = $user->save();

        if ($success) {
            // Revoke all sessions for security
            $user->revokeAllSessions();
            
            $this->logger->info('User password reset by admin', [
                'user_id' => $user->id,
                'email' => $user->email,
                'reset_by' => $this->getCurrentUserId()
            ]);
        }

        return $success;
    }

    /**
     * Get user session info
     */
    public function getSessionInfo(): array
    {
        if (!$this->isAuthenticated()) {
            return [];
        }

        $sessionId = $_SESSION['session_id'] ?? null;
        $session = $sessionId ? Session::findById($sessionId) : null;

        return [
            'session_id' => $sessionId,
            'login_time' => $_SESSION['login_time'] ?? null,
            'last_seen' => $session ? $session->last_seen : null,
            'ip_address' => $session ? $session->ip_address : null,
            'user_agent' => $session ? $session->user_agent : null
        ];
    }

    /**
     * Clean up expired sessions
     */
    public function cleanupExpiredSessions(): int
    {
        return Session::cleanupExpired();
    }
}