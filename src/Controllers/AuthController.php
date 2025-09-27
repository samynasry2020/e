<?php

declare(strict_types=1);

namespace GovTribe\Controllers;

use GovTribe\Core\Auth;

class AuthController extends BaseController
{
    public function showLogin(): string
    {
        // Redirect if already logged in
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }

        return $this->render('auth/login', [
            'title' => 'Login',
            'flash_messages' => $this->getFlashMessages()
        ]);
    }

    public function login(): void
    {
        $email = $this->sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        // Validate input
        $errors = $this->validate([
            'email' => 'required|email',
            'password' => 'required'
        ], ['email' => $email, 'password' => $password]);

        if (!empty($errors)) {
            $this->flashMessage('error', 'Please check your input and try again.');
            $this->back();
        }

        // Attempt login
        if (Auth::attempt($email, $password)) {
            // Set remember me cookie if requested
            if ($remember) {
                $expiry = time() + (30 * 24 * 60 * 60); // 30 days
                setcookie('remember_token', session_id(), $expiry, '/', '', true, true);
            }

            $this->logAction('login', 'user', Auth::id());
            $this->redirect('/dashboard');
        } else {
            $this->flashMessage('error', 'Invalid email or password.');
            $this->back();
        }
    }

    public function logout(): void
    {
        $userId = Auth::id();
        
        Auth::logout();
        
        // Clear remember me cookie
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/', '', true, true);
        }

        $this->logAction('logout', 'user', $userId);
        $this->redirect('/login');
    }
}