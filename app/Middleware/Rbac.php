<?php
declare(strict_types=1);

namespace App\Middleware;

final class Rbac
{
    public static function enforce(array $allowedRoles): void
    {
        Auth::requireLogin();
        $role = $_SESSION['role'] ?? null;
        if (!in_array($role, $allowedRoles, true)) {
            http_response_code(403);
            header('Content-Type: text/plain');
            echo 'Forbidden';
            exit;
        }
    }
}

