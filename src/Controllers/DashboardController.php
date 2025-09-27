<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\AuthController;
use App\Models\Opportunity;
use App\Utils\Router;

class DashboardController
{
    /**
     * Show dashboard
     */
    public function index(): void
    {
        AuthController::requireAuth();

        $user = AuthController::getCurrentUser();
        $stats = Opportunity::getDashboardStats();

        $this->renderView('dashboard/index', [
            'title' => 'Dashboard',
            'user' => $user,
            'stats' => $stats,
        ]);
    }

    /**
     * Render view with data
     */
    private function renderView(string $view, array $data = []): void
    {
        extract($data);
        
        $viewFile = dirname(__DIR__, 2) . "/views/{$view}.php";
        
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            throw new \RuntimeException("View not found: {$view}");
        }
    }
}