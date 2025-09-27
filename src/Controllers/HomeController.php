<?php

declare(strict_types=1);

namespace GovTribe\Controllers;

use GovTribe\Core\Auth;
use GovTribe\Core\Database;
use GovTribe\Services\IngestionService;

class HomeController extends BaseController
{
    public function dashboard(): string
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }

        // Get dashboard statistics
        $stats = $this->getDashboardStats();
        
        // Get recent opportunities
        $recentOpportunities = $this->getRecentOpportunities();
        
        // Get pipeline data
        $pipelineData = $this->getPipelineData();
        
        // Get last ingestion result
        $ingestionService = new IngestionService();
        $lastIngestion = $ingestionService->getLastIngestionResult();

        return $this->render('dashboard', [
            'title' => 'Dashboard',
            'stats' => $stats,
            'recent_opportunities' => $recentOpportunities,
            'pipeline_data' => $pipelineData,
            'last_ingestion' => $lastIngestion,
            'flash_messages' => $this->getFlashMessages()
        ]);
    }

    public function health(): string
    {
        $health = [
            'status' => 'ok',
            'timestamp' => date('c'),
            'checks' => []
        ];

        // Database check
        try {
            Database::query('SELECT 1');
            $health['checks']['database'] = 'ok';
        } catch (\Exception $e) {
            $health['checks']['database'] = 'error';
            $health['status'] = 'error';
        }

        // Files directory check
        $filesRoot = \GovTribe\Core\Config::get('files.root');
        if (is_dir($filesRoot) && is_writable($filesRoot)) {
            $health['checks']['files'] = 'ok';
        } else {
            $health['checks']['files'] = 'error';
            $health['status'] = 'error';
        }

        // SAM API check (if key is configured)
        try {
            $samClient = new \GovTribe\Services\SamApiClient();
            $testResult = $samClient->testConnection();
            $health['checks']['sam_api'] = $testResult['success'] ? 'ok' : 'warning';
        } catch (\Exception $e) {
            $health['checks']['sam_api'] = 'warning';
        }

        return $this->json($health, $health['status'] === 'ok' ? 200 : 503);
    }

    private function getDashboardStats(): array
    {
        $stats = [];

        // New opportunities (last 24 hours)
        $stats['new_24h'] = Database::fetchOne(
            'SELECT COUNT(*) as count FROM opportunities WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)'
        )['count'] ?? 0;

        // Due within 7 days
        $stats['due_7d'] = Database::fetchOne(
            'SELECT COUNT(*) as count FROM opportunities WHERE due_at BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY) AND status IN ("New", "Review", "Pursue")'
        )['count'] ?? 0;

        // Total active opportunities
        $stats['total_active'] = Database::fetchOne(
            'SELECT COUNT(*) as count FROM opportunities WHERE active = 1 AND status IN ("New", "Review", "Pursue")'
        )['count'] ?? 0;

        // Total in pipeline
        $stats['in_pipeline'] = Database::fetchOne(
            'SELECT COUNT(*) as count FROM opportunities WHERE status = "Pursue"'
        )['count'] ?? 0;

        // Awards this year
        $stats['awards_year'] = Database::fetchOne(
            'SELECT COUNT(*) as count FROM awards WHERE YEAR(award_date) = YEAR(NOW())'
        )['count'] ?? 0;

        // Total award value this year
        $stats['award_value_year'] = Database::fetchOne(
            'SELECT COALESCE(SUM(award_amount), 0) as total FROM awards WHERE YEAR(award_date) = YEAR(NOW())'
        )['total'] ?? 0;

        return $stats;
    }

    private function getRecentOpportunities(): array
    {
        return Database::fetchAll(
            'SELECT o.*, a.name as agency_name 
             FROM opportunities o 
             LEFT JOIN agencies a ON o.agency_id = a.id 
             WHERE o.active = 1 
             ORDER BY o.created_at DESC 
             LIMIT 10'
        );
    }

    private function getPipelineData(): array
    {
        $pipeline = Database::fetchAll(
            'SELECT status, COUNT(*) as count 
             FROM opportunities 
             WHERE active = 1 
             GROUP BY status 
             ORDER BY FIELD(status, "New", "Review", "Pursue", "No-Bid", "Awarded", "Lost")'
        );

        $data = [
            'New' => 0,
            'Review' => 0,
            'Pursue' => 0,
            'No-Bid' => 0,
            'Awarded' => 0,
            'Lost' => 0
        ];

        foreach ($pipeline as $item) {
            $data[$item['status']] = (int)$item['count'];
        }

        return $data;
    }
}