<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\AuthController;
use App\Models\Agency;
use App\Models\Opportunity;
use App\Utils\Router;
use App\Utils\Security;

class OpportunityController
{
    /**
     * Show opportunities list
     */
    public function index(): void
    {
        AuthController::requireAuth();

        $user = AuthController::getCurrentUser();
        
        // Get filter parameters
        $filters = $this->getFilters();
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 50;
        $offset = ($page - 1) * $limit;

        // Get opportunities
        $opportunities = Opportunity::getFiltered($filters, $limit, $offset);
        $totalCount = Opportunity::getCount($filters);
        $totalPages = ceil($totalCount / $limit);

        // Load related data for each opportunity
        foreach ($opportunities as $opportunity) {
            $opportunity->loadRelations();
        }

        // Get filter options
        $agencies = Agency::all();
        $naicsCodes = $this->getNaicsCodes();
        $setAsideOptions = $this->getSetAsideOptions();

        $this->renderView('opportunities/index', [
            'title' => 'Opportunities',
            'user' => $user,
            'opportunities' => $opportunities,
            'agencies' => $agencies,
            'naics_codes' => $naicsCodes,
            'set_aside_options' => $setAsideOptions,
            'filters' => $filters,
            'pagination' => [
                'page' => $page,
                'total_pages' => $totalPages,
                'total_count' => $totalCount,
                'limit' => $limit,
            ],
        ]);
    }

    /**
     * Show opportunity detail
     */
    public function show(array $params): void
    {
        AuthController::requireAuth();

        $user = AuthController::getCurrentUser();
        $opportunityId = (int) $params['id'];

        $opportunity = Opportunity::findById($opportunityId);
        if (!$opportunity) {
            $this->renderView('errors/404', ['title' => 'Opportunity Not Found']);
            return;
        }

        $opportunity->loadRelations();
        $changes = $opportunity->getChanges();

        $this->renderView('opportunities/show', [
            'title' => $opportunity->title,
            'user' => $user,
            'opportunity' => $opportunity,
            'changes' => $changes,
        ]);
    }

    /**
     * Update opportunity score
     */
    public function updateScore(array $params): void
    {
        AuthController::requirePermission('opportunities.score');

        $opportunityId = (int) $params['id'];
        $opportunity = Opportunity::findById($opportunityId);
        
        if (!$opportunity) {
            Router::redirect('/opportunities');
            return;
        }

        // Validate CSRF token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Security::validateCsrfToken($csrfToken)) {
            Router::redirect('/opportunities/' . $opportunityId);
            return;
        }

        $score = (int) ($_POST['score'] ?? 0);
        $reasons = $_POST['reasons'] ?? [];

        $opportunity->updateScore($score, $reasons);

        Router::redirect('/opportunities/' . $opportunityId);
    }

    /**
     * Update opportunity status
     */
    public function updateStatus(array $params): void
    {
        AuthController::requirePermission('opportunities.manage');

        $opportunityId = (int) $params['id'];
        $opportunity = Opportunity::findById($opportunityId);
        
        if (!$opportunity) {
            Router::redirect('/opportunities');
            return;
        }

        // Validate CSRF token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Security::validateCsrfToken($csrfToken)) {
            Router::redirect('/opportunities/' . $opportunityId);
            return;
        }

        $status = Security::sanitizeInput($_POST['status'] ?? '');
        $note = Security::sanitizeInput($_POST['note'] ?? '');

        $opportunity->updateStatus($status, $note);

        Router::redirect('/opportunities/' . $opportunityId);
    }

    /**
     * Get filter parameters from request
     */
    private function getFilters(): array
    {
        $filters = [];

        if (!empty($_GET['status'])) {
            $filters['status'] = Security::sanitizeInput($_GET['status']);
        }

        if (!empty($_GET['agency_id'])) {
            $filters['agency_id'] = (int) $_GET['agency_id'];
        }

        if (!empty($_GET['naics_code'])) {
            $filters['naics_code'] = Security::sanitizeInput($_GET['naics_code']);
        }

        if (!empty($_GET['set_aside'])) {
            $filters['set_aside'] = Security::sanitizeInput($_GET['set_aside']);
        }

        if (!empty($_GET['notice_type'])) {
            $filters['notice_type'] = Security::sanitizeInput($_GET['notice_type']);
        }

        if (!empty($_GET['score_min'])) {
            $filters['score_min'] = (int) $_GET['score_min'];
        }

        if (!empty($_GET['score_max'])) {
            $filters['score_max'] = (int) $_GET['score_max'];
        }

        if (isset($_GET['active'])) {
            $filters['active'] = $_GET['active'] === '1';
        }

        if (!empty($_GET['search'])) {
            $filters['search'] = Security::sanitizeInput($_GET['search']);
        }

        return $filters;
    }

    /**
     * Get NAICS codes for filter
     */
    private function getNaicsCodes(): array
    {
        return [
            '334111' => '334111 - Electronic Computer Manufacturing',
            '541512' => '541512 - Computer Systems Design Services',
        ];
    }

    /**
     * Get set-aside options for filter
     */
    private function getSetAsideOptions(): array
    {
        return [
            'WOSB' => 'Women-Owned Small Business (WOSB)',
            'EDWOSB' => 'Economically Disadvantaged WOSB (EDWOSB)',
            'VOSB' => 'Veteran-Owned Small Business (VOSB)',
            'SDVOSB' => 'Service-Disabled Veteran-Owned Small Business (SDVOSB)',
            '8(a)' => '8(a) Business Development Program',
            'HUBZone' => 'Historically Underutilized Business Zones (HUBZone)',
        ];
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