<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Database;
use App\Core\View;
use PDO;

final class AuthController
{
    public function showLogin(): void
    {
        if (!empty($_SESSION['user_id'])) {
            header('Location: /');
            return;
        }
        View::render('auth/login.php', ['page_title' => 'Login']);
    }

    public function login(): void
    {
        Csrf::validateFromRequest();

        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $this->renderLoginError('Email and password are required.');
            return;
        }

        $pdo = Database::pdo();
        $stmt = $pdo->prepare('SELECT id, password_hash, status, role FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->throttle($email);
            $this->renderLoginError('Invalid credentials.');
            return;
        }
        if ($user['status'] !== 'active') {
            $this->renderLoginError('Account disabled.');
            return;
        }

        if (password_needs_rehash($user['password_hash'], PASSWORD_ARGON2ID)) {
            $new = password_hash($password, PASSWORD_ARGON2ID);
            $upd = $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
            $upd->execute([$new, $user['id']]);
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['login_at'] = time();
        Csrf::rotate();

        $this->persistSession((int)$user['id']);
        header('Location: /');
    }

    public function logout(): void
    {
        session_regenerate_id(true);
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', $params['secure'], $params['httponly']);
        }
        session_destroy();
        header('Location: /login');
    }

    private function renderLoginError(string $message): void
    {
        View::render('auth/login.php', [
            'page_title' => 'Login',
            'error' => $message,
        ]);
    }

    private function throttle(string $key): void
    {
        $k = 'login_attempts:' . sha1($key . '|' . ($_SERVER['REMOTE_ADDR'] ?? '')); 
        $_SESSION[$k] = ($_SESSION[$k] ?? 0) + 1;
        if ($_SESSION[$k] > 5) {
            usleep(1500000); // 1.5s
        }
    }

    private function persistSession(int $userId): void
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('INSERT INTO sessions (id, user_id, ip, ua, last_seen) VALUES (?, ?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE user_id = VALUES(user_id), ip = VALUES(ip), ua = VALUES(ua), last_seen = NOW()');
        $stmt->execute([
            session_id(),
            $userId,
            $_SERVER['REMOTE_ADDR'] ?? null,
            substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
        ]);
    }
}

