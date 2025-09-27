<?php

declare(strict_types=1);

namespace GovTribe\Controllers;

use GovTribe\Services\AuthService;
use GovTribe\Utils\Security;

/**
 * Authentication controller
 */
class AuthController extends BaseController
{
    private AuthService $authService;

    public function __construct()
    {
        parent::__construct();
        $this->authService = new AuthService();
    }

    /**
     * Show login form
     */
    public function showLogin(): void
    {
        // Redirect if already authenticated
        if ($this->authService->isAuthenticated()) {
            $this->redirect('/dashboard');
            return;
        }

        $errors = $this->getValidationErrors();
        $oldInput = $this->getOldInput();
        $flashMessages = $this->getFlashMessages();

        $this->view('auth/login', [
            'errors' => $errors,
            'old' => $oldInput,
            'flash' => $flashMessages,
            'csrf_token' => $this->getCsrfToken()
        ]);
    }

    /**
     * Handle login form submission
     */
    public function login(): void
    {
        $data = $this->getRequestData();
        
        // Validate required fields
        $errors = $this->validateRequired($data, ['email', 'password']);
        
        if (!empty($errors)) {
            $this->handleValidationErrors($errors);
            return;
        }

        $email = $this->sanitize($data['email']);
        $password = $data['password']; // Don't sanitize password
        
        // Validate email format
        if (!$this->validateEmail($email)) {
            $this->handleValidationErrors(['email' => 'Invalid email format']);
            return;
        }

        // Get client info for session
        $ipAddress = Security::getClientIp();
        $userAgent = Security::getUserAgent();

        // Attempt authentication
        $user = $this->authService->authenticate($email, $password, $ipAddress, $userAgent);
        
        if (!$user) {
            $this->flash('error', 'Invalid email or password');
            $this->redirect('/login');
            return;
        }

        // Success - redirect to intended page or dashboard
        $redirectTo = $_GET['redirect'] ?? '/dashboard';
        $this->redirect($redirectTo);
    }

    /**
     * Handle logout
     */
    public function logout(): void
    {
        $this->authService->logout();
        $this->redirect('/login');
    }

    /**
     * Show change password form
     */
    public function showChangePassword(): void
    {
        $user = $this->requireAuth();
        
        $errors = $this->getValidationErrors();
        $flashMessages = $this->getFlashMessages();

        $this->view('auth/change-password', [
            'user' => $user,
            'errors' => $errors,
            'flash' => $flashMessages,
            'csrf_token' => $this->getCsrfToken()
        ]);
    }

    /**
     * Handle change password form submission
     */
    public function changePassword(): void
    {
        $user = $this->requireAuth();
        $data = $this->getRequestData();
        
        // Validate required fields
        $errors = $this->validateRequired($data, ['current_password', 'new_password', 'confirm_password']);
        
        if (!empty($errors)) {
            $this->handleValidationErrors($errors);
            return;
        }

        $currentPassword = $data['current_password'];
        $newPassword = $data['new_password'];
        $confirmPassword = $data['confirm_password'];

        // Validate password confirmation
        if ($newPassword !== $confirmPassword) {
            $this->handleValidationErrors(['confirm_password' => 'Password confirmation does not match']);
            return;
        }

        // Validate password strength
        if (strlen($newPassword) < 8) {
            $this->handleValidationErrors(['new_password' => 'Password must be at least 8 characters long']);
            return;
        }

        // Attempt to change password
        $success = $this->authService->changePassword($user, $currentPassword, $newPassword);
        
        if (!$success) {
            $this->flash('error', 'Current password is incorrect');
            $this->redirect('/change-password');
            return;
        }

        $this->flash('success', 'Password changed successfully');
        $this->redirect('/dashboard');
    }
}