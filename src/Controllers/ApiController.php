<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\AuthController;
use App\Models\Opportunity;
use App\Services\IngestionService;
use App\Utils\Router;

class ApiController
{
    /**
     * Get opportunities as JSON
     */
    public function opportunities(): void
    {
        AuthController::requireAuth();

        header('Content-Type: application/json');

        try {
            $filters = $this->getFilters();
            $opportunities = Opportunity::getFiltered($filters, 100, 0);

            echo json_encode([
                'success' => true,
                'data' => $opportunities,
                'count' => count($opportunities)
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Trigger sync via API
     */
    public function sync(): void
    {
        AuthController::requireRole('admin');

        header('Content-Type: application/json');

        // Validate CSRF token
        $csrfToken = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!\App\Utils\Security::validateCsrfToken($csrfToken)) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'error' => 'Invalid CSRF token'
            ]);
            return;
        }

        try {
            $ingestionService = new IngestionService();
            $results = $ingestionService->ingestOpportunities();

            echo json_encode([
                'success' => true,
                'results' => $results
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get filter parameters from request
     */
    private function getFilters(): array
    {
        $filters = [];

        if (!empty($_GET['status'])) {
            $filters['status'] = \App\Utils\Security::sanitizeInput($_GET['status']);
        }

        if (!empty($_GET['agency_id'])) {
            $filters['agency_id'] = (int) $_GET['agency_id'];
        }

        if (!empty($_GET['naics_code'])) {
            $filters['naics_code'] = \App\Utils\Security::sanitizeInput($_GET['naics_code']);
        }

        if (!empty($_GET['set_aside'])) {
            $filters['set_aside'] = \App\Utils\Security::sanitizeInput($_GET['set_aside']);
        }

        if (!empty($_GET['notice_type'])) {
            $filters['notice_type'] = \App\Utils\Security::sanitizeInput($_GET['notice_type']);
        }

        if (isset($_GET['active'])) {
            $filters['active'] = $_GET['active'] === '1';
        }

        if (!empty($_GET['search'])) {
            $filters['search'] = \App\Utils\Security::sanitizeInput($_GET['search']);
        }

        return $filters;
    }
}