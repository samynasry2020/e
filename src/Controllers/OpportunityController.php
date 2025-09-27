<?php

declare(strict_types=1);

namespace GovTribe\Controllers;

use GovTribe\Core\Database;
use GovTribe\Core\Auth;

class OpportunityController extends BaseController
{
    public function index(): string
    {
        $this->requirePermission('opportunities.view');

        // Get filters from query parameters
        $filters = $this->getFilters();
        
        // Build query with filters
        $query = $this->buildOpportunityQuery($filters);
        $countQuery = $this->buildOpportunityCountQuery($filters);
        
        // Pagination
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 25;
        $offset = ($page - 1) * $perPage;
        
        // Get total count
        $totalResult = Database::fetchOne($countQuery['sql'], $countQuery['params']);
        $total = $totalResult['count'] ?? 0;
        
        // Get opportunities
        $opportunities = Database::fetchAll(
            $query['sql'] . " LIMIT {$perPage} OFFSET {$offset}",
            $query['params']
        );
        
        // Get filter options
        $filterOptions = $this->getFilterOptions();
        
        return $this->render('opportunities/index', [
            'title' => 'Opportunities',
            'opportunities' => $opportunities,
            'filters' => $filters,
            'filter_options' => $filterOptions,
            'pagination' => [
                'current' => $page,
                'total' => ceil($total / $perPage),
                'per_page' => $perPage,
                'total_items' => $total
            ]
        ]);
    }

    public function show(int $id): string
    {
        $this->requirePermission('opportunities.view');

        $opportunity = $this->getOpportunityWithDetails($id);
        if (!$opportunity) {
            throw new \Exception('Opportunity not found', 404);
        }

        // Get related data
        $documents = $this->getOpportunityDocuments($id);
        $contacts = $this->getOpportunityContacts($id);
        $naicsCodes = $this->getOpportunityNaics($id);
        $proposals = $this->getOpportunityProposals($id);
        $boms = $this->getOpportunityBoms($id);
        $submissions = $this->getOpportunitySubmissions($id);
        $awards = $this->getOpportunityAwards($id);
        $changes = $this->getOpportunityChanges($id);

        return $this->render('opportunities/show', [
            'title' => $opportunity['title'],
            'opportunity' => $opportunity,
            'documents' => $documents,
            'contacts' => $contacts,
            'naics_codes' => $naicsCodes,
            'proposals' => $proposals,
            'boms' => $boms,
            'submissions' => $submissions,
            'awards' => $awards,
            'changes' => $changes
        ]);
    }

    public function updateScore(int $id): string
    {
        $this->requirePermission('opportunities.score');

        $opportunity = Database::fetchOne('SELECT * FROM opportunities WHERE id = ?', [$id]);
        if (!$opportunity) {
            return $this->json(['error' => 'Opportunity not found'], 404);
        }

        $score = max(0, min(100, (int)($_POST['score'] ?? 0)));
        $reasons = $this->sanitizeInput($_POST['reasons'] ?? '');

        Database::update('opportunities', [
            'score' => $score,
            'score_reasons' => $reasons
        ], ['id' => $id]);

        $this->logAction('update_score', 'opportunity', $id, [
            'old_score' => $opportunity['score'],
            'new_score' => $score,
            'reasons' => $reasons
        ]);

        return $this->json([
            'success' => true,
            'message' => 'Score updated successfully'
        ]);
    }

    public function updateStatus(int $id): string
    {
        $this->requirePermission('opportunities.update');

        $opportunity = Database::fetchOne('SELECT * FROM opportunities WHERE id = ?', [$id]);
        if (!$opportunity) {
            return $this->json(['error' => 'Opportunity not found'], 404);
        }

        $status = $_POST['status'] ?? '';
        $note = $this->sanitizeInput($_POST['note'] ?? '');

        $allowedStatuses = ['New', 'Review', 'Pursue', 'No-Bid', 'Awarded', 'Lost'];
        if (!in_array($status, $allowedStatuses)) {
            return $this->json(['error' => 'Invalid status'], 400);
        }

        Database::update('opportunities', [
            'status' => $status
        ], ['id' => $id]);

        $this->logAction('update_status', 'opportunity', $id, [
            'old_status' => $opportunity['status'],
            'new_status' => $status,
            'note' => $note
        ]);

        return $this->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }

    public function documents(int $id): string
    {
        $this->requirePermission('opportunities.view');

        $opportunity = Database::fetchOne('SELECT * FROM opportunities WHERE id = ?', [$id]);
        if (!$opportunity) {
            throw new \Exception('Opportunity not found', 404);
        }

        $documents = $this->getOpportunityDocuments($id);

        return $this->render('opportunities/documents', [
            'title' => 'Documents - ' . $opportunity['title'],
            'opportunity' => $opportunity,
            'documents' => $documents
        ]);
    }

    public function downloadDocument(int $id, int $docId): void
    {
        $this->requirePermission('opportunities.view');

        $document = Database::fetchOne(
            'SELECT d.*, f.* FROM documents d 
             LEFT JOIN files f ON d.file_id = f.id 
             WHERE d.id = ? AND d.opportunity_id = ?',
            [$docId, $id]
        );

        if (!$document) {
            throw new \Exception('Document not found', 404);
        }

        if (!$document['file_id'] || !$document['path']) {
            // Redirect to source URL if no local file
            if ($document['source_url']) {
                header('Location: ' . $document['source_url']);
                exit;
            }
            throw new \Exception('File not available', 404);
        }

        $filePath = rtrim(\GovTribe\Core\Config::get('files.root'), '/') . '/' . $document['path'];
        if (!file_exists($filePath)) {
            throw new \Exception('File not found on disk', 404);
        }

        // Set headers for download
        header('Content-Type: ' . $document['mime']);
        header('Content-Length: ' . $document['size']);
        header('Content-Disposition: attachment; filename="' . $document['original_name'] . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Output file
        readfile($filePath);
        exit;
    }

    private function getFilters(): array
    {
        return [
            'search' => $_GET['search'] ?? '',
            'status' => $_GET['status'] ?? '',
            'agency' => $_GET['agency'] ?? '',
            'naics' => $_GET['naics'] ?? '',
            'set_aside' => $_GET['set_aside'] ?? '',
            'score_min' => $_GET['score_min'] ?? '',
            'score_max' => $_GET['score_max'] ?? '',
            'due_from' => $_GET['due_from'] ?? '',
            'due_to' => $_GET['due_to'] ?? '',
            'active' => $_GET['active'] ?? '1',
            'sort' => $_GET['sort'] ?? 'created_at',
            'order' => $_GET['order'] ?? 'desc'
        ];
    }

    private function buildOpportunityQuery(array $filters): array
    {
        $sql = 'SELECT o.*, a.name as agency_name 
                FROM opportunities o 
                LEFT JOIN agencies a ON o.agency_id = a.id';
        
        $params = [];
        $conditions = [];

        // Search in title and description
        if (!empty($filters['search'])) {
            $conditions[] = 'MATCH(o.title, o.description) AGAINST (? IN NATURAL LANGUAGE MODE)';
            $params[] = $filters['search'];
        }

        // Status filter
        if (!empty($filters['status'])) {
            $conditions[] = 'o.status = ?';
            $params[] = $filters['status'];
        }

        // Agency filter
        if (!empty($filters['agency'])) {
            $conditions[] = 'o.agency_id = ?';
            $params[] = $filters['agency'];
        }

        // NAICS filter
        if (!empty($filters['naics'])) {
            $sql .= ' INNER JOIN opportunity_naics on ON o.id = on.opportunity_id';
            $conditions[] = 'on.naics_code = ?';
            $params[] = $filters['naics'];
        }

        // Set-aside filter
        if (!empty($filters['set_aside'])) {
            $conditions[] = 'o.set_aside LIKE ?';
            $params[] = '%' . $filters['set_aside'] . '%';
        }

        // Score range
        if (!empty($filters['score_min'])) {
            $conditions[] = 'o.score >= ?';
            $params[] = (int)$filters['score_min'];
        }
        if (!empty($filters['score_max'])) {
            $conditions[] = 'o.score <= ?';
            $params[] = (int)$filters['score_max'];
        }

        // Due date range
        if (!empty($filters['due_from'])) {
            $conditions[] = 'o.due_at >= ?';
            $params[] = $filters['due_from'] . ' 00:00:00';
        }
        if (!empty($filters['due_to'])) {
            $conditions[] = 'o.due_at <= ?';
            $params[] = $filters['due_to'] . ' 23:59:59';
        }

        // Active filter
        if ($filters['active'] !== '') {
            $conditions[] = 'o.active = ?';
            $params[] = (int)$filters['active'];
        }

        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        // Sorting
        $allowedSorts = ['title', 'posted_at', 'due_at', 'score', 'status', 'created_at'];
        $sort = in_array($filters['sort'], $allowedSorts) ? $filters['sort'] : 'created_at';
        $order = strtoupper($filters['order']) === 'ASC' ? 'ASC' : 'DESC';
        $sql .= " ORDER BY o.{$sort} {$order}";

        return ['sql' => $sql, 'params' => $params];
    }

    private function buildOpportunityCountQuery(array $filters): array
    {
        $query = $this->buildOpportunityQuery($filters);
        
        // Replace SELECT clause with COUNT
        $sql = preg_replace('/^SELECT .+ FROM/', 'SELECT COUNT(DISTINCT o.id) as count FROM', $query['sql']);
        
        // Remove ORDER BY clause
        $sql = preg_replace('/ORDER BY .+$/', '', $sql);

        return ['sql' => $sql, 'params' => $query['params']];
    }

    private function getFilterOptions(): array
    {
        return [
            'agencies' => Database::fetchAll(
                'SELECT DISTINCT a.id, a.name 
                 FROM agencies a 
                 INNER JOIN opportunities o ON a.id = o.agency_id 
                 ORDER BY a.name'
            ),
            'naics_codes' => Database::fetchAll(
                'SELECT DISTINCT on.naics_code 
                 FROM opportunity_naics on 
                 ORDER BY on.naics_code'
            ),
            'statuses' => [
                'New', 'Review', 'Pursue', 'No-Bid', 'Awarded', 'Lost'
            ]
        ];
    }

    private function getOpportunityWithDetails(int $id): ?array
    {
        return Database::fetchOne(
            'SELECT o.*, a.name as agency_name, a.type as agency_type 
             FROM opportunities o 
             LEFT JOIN agencies a ON o.agency_id = a.id 
             WHERE o.id = ?',
            [$id]
        );
    }

    private function getOpportunityDocuments(int $id): array
    {
        return Database::fetchAll(
            'SELECT d.*, f.original_name, f.size, f.mime 
             FROM documents d 
             LEFT JOIN files f ON d.file_id = f.id 
             WHERE d.opportunity_id = ? 
             ORDER BY d.created_at DESC',
            [$id]
        );
    }

    private function getOpportunityContacts(int $id): array
    {
        return Database::fetchAll(
            'SELECT * FROM contacts WHERE opportunity_id = ? ORDER BY name',
            [$id]
        );
    }

    private function getOpportunityNaics(int $id): array
    {
        return Database::fetchAll(
            'SELECT naics_code FROM opportunity_naics WHERE opportunity_id = ?',
            [$id]
        );
    }

    private function getOpportunityProposals(int $id): array
    {
        return Database::fetchAll(
            'SELECT * FROM proposals WHERE opportunity_id = ? ORDER BY created_at DESC',
            [$id]
        );
    }

    private function getOpportunityBoms(int $id): array
    {
        return Database::fetchAll(
            'SELECT * FROM boms WHERE opportunity_id = ? ORDER BY created_at DESC',
            [$id]
        );
    }

    private function getOpportunitySubmissions(int $id): array
    {
        return Database::fetchAll(
            'SELECT * FROM submissions WHERE opportunity_id = ? ORDER BY created_at DESC',
            [$id]
        );
    }

    private function getOpportunityAwards(int $id): array
    {
        return Database::fetchAll(
            'SELECT * FROM awards WHERE opportunity_id = ? ORDER BY created_at DESC',
            [$id]
        );
    }

    private function getOpportunityChanges(int $id): array
    {
        return Database::fetchAll(
            'SELECT oc.*, u.email as user_email 
             FROM opportunity_changes oc 
             LEFT JOIN users u ON JSON_EXTRACT(oc.payload, "$.user_id") = u.id 
             WHERE oc.opportunity_id = ? 
             ORDER BY oc.changed_at DESC 
             LIMIT 20',
            [$id]
        );
    }
}