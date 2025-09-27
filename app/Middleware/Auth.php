<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Database;

final class Auth
{
    public static function requireLogin(): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('UPDATE sessions SET last_seen = NOW() WHERE id = ?');
        $stmt->execute([session_id()]);
    }

    public static function requireRole(array $roles): void
    {
        self::requireLogin();
        $role = $_SESSION['role'] ?? null;
        if (!in_array($role, $roles, true)) {
            http_response_code(403);
            header('Content-Type: text/plain');
            echo 'Forbidden';
            exit;
        }
    }
}

