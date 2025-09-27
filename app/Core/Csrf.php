<?php
declare(strict_types=1);

namespace App\Core;

final class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function rotate(): void
    {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    public static function validateFromRequest(): void
    {
        $sent = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        $valid = $_SESSION['csrf_token'] ?? '';
        if (!is_string($sent) || !is_string($valid) || $sent === '' || !hash_equals($valid, $sent)) {
            http_response_code(400);
            header('Content-Type: text/plain');
            echo 'Invalid CSRF token';
            exit;
        }
    }
}

