<?php

declare(strict_types=1);

namespace GovTribe\Middleware;

use GovTribe\Core\Auth;

class RoleMiddleware
{
    private string $requiredRole;

    public function __construct(string $requiredRole = '')
    {
        $this->requiredRole = $requiredRole;
    }

    public function handle(): void
    {
        $user = Auth::user();
        
        if (!$user) {
            http_response_code(401);
            header('Location: /login');
            exit;
        }

        if (!empty($this->requiredRole) && !Auth::hasRole($this->requiredRole)) {
            http_response_code(403);
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Insufficient permissions']);
            } else {
                echo 'Access denied';
            }
            exit;
        }
    }

    private function isAjaxRequest(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}