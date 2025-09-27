<?php

declare(strict_types=1);

namespace GovTribe\Middleware;

use GovTribe\Core\Auth;

class CsrfMiddleware
{
    public function handle(): void
    {
        if (in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            
            if (!Auth::verifyCsrfToken($token)) {
                http_response_code(403);
                if ($this->isAjaxRequest()) {
                    header('Content-Type: application/json');
                    echo json_encode(['error' => 'Invalid CSRF token']);
                } else {
                    echo 'Invalid CSRF token';
                }
                exit;
            }
        }
    }

    private function isAjaxRequest(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}