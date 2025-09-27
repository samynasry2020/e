<?php
declare(strict_types=1);

namespace App\Core;

final class View
{
    public static function render(string $template, array $params = []): void
    {
        $templatePath = ROOT_PATH . '/app/Views/' . ltrim($template, '/');
        if (!is_file($templatePath)) {
            http_response_code(500);
            header('Content-Type: text/plain');
            echo 'View not found';
            return;
        }
        extract($params, EXTR_OVERWRITE);
        include ROOT_PATH . '/app/Views/_layout/header.php';
        include $templatePath;
        include ROOT_PATH . '/app/Views/_layout/footer.php';
    }
}

