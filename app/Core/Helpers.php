<?php
declare(strict_types=1);

namespace App\Core;

final class Helpers
{
    public static function requirePost(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            header('Allow: POST');
            echo 'Method Not Allowed';
            exit;
        }
    }
}

