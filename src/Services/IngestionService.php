<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Agency;
use App\Models\Contact;
use App\Models\Document;
use App\Models\File;
use App\Models\Opportunity;
use App\Utils\Config;
use App\Utils\Database;
use App\Utils\Logger;
use App\Utils\Security;

class IngestionService
{
    private SamApiService $samApi;
    private array $allowedNaicsCodes;
    private array $allowedProcurementTypes;
    private array $preferredAgencies;

    public function __construct()
    {
        $this->samApi = new SamApiService();
        $this->allowedNaicsCodes = json_decode(Config::get('default_naics_codes', '["334111", "541512"]'), true);
        $this->allowedProcurementTypes = ['o', 'k']; // Solicitation, Combined
        $this->preferredAgencies = []; // Will be loaded from settings
    }

    /**
     * Ingest opportunities from SAM.gov API
     */
    public function ingestOpportunities(array $params = []): array
    {
        $results = [
            'total_fetched' => 0,
            'created' => 0,
            'updated' => 0,
            'rejected' => 0,
            'errors' => [],
            'start_time' => date('Y-m-d H:i:s'),
        ];

        try {
            Logger::info('Starting opportunity ingestion', $params);

            // Build search parameters
            $searchParams = $this->buildSearchParams($params);
            
            // Fetch all pages of results
            $offset = 0;
            $limit = 100;
            $totalRecords = null;

            do {
                $searchParams['offset'] = $offset;
                $searchParams['limit'] = $limit;

                $response = $this->samApi->searchOpportunities($searchParams);
                
                if ($totalRecords === null) {
                    $totalRecords = $response['totalRecords'] ?? 0;
                    $results['total_fetched'] = $totalRecords;
                }

                $opportunities = $response['opportunitiesData'] ?? [];
                
                foreach ($opportunities as $opportunityData) {
                    try {
                        $result = $this->processOpportunity($opportunityData);
                        
                        if ($result['action'] === 'created') {
                            $results['created']++;
                        } elseif ($result['action'] === 'updated') {
                            $results['updated']++;
                        } else {
                            $results['rejected']++;
                            $results['errors'][] = $result['reason'];
                        }
                    } catch (\Exception $e) {
                        $results['rejected']++;
                        $results['errors'][] = "Error processing opportunity: " . $e->getMessage();
                        Logger::error('Error processing opportunity', [
                            'data' => $opportunityData,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                $offset += $limit;
                
                // Small delay to be respectful to the API
                usleep(100000); // 100ms
                
            } while ($offset < $totalRecords);

            $results['end_time'] = date('Y-m-d H:i:s');
            $results['success'] = true;

            Logger::info('Opportunity ingestion completed', $results);

        } catch (\Exception $e) {
            $results['success'] = false;
            $results['error'] = $e->getMessage();
            $results['end_time'] = date('Y-m-d H:i:s');

            Logger::error('Opportunity ingestion failed', $results);
        }

        return $results;
    }

    /**
     * Build search parameters with defaults
     */
    private function buildSearchParams(array $params): array
    {
        $defaultDays = Config::get('default_posted_days', 30);
        
        return [
            'postedFrom' => $params['postedFrom'] ?? date('m/d/Y', strtotime("-{$defaultDays} days")),
            'postedTo' => $params['postedTo'] ?? date('m/d/Y'),
            'ptype' => $params['ptype'] ?? $this->allowedProcurementTypes,
            'ncode' => $params['ncode'] ?? $this->allowedNaicsCodes,
            'typeOfSetAside' => $params['typeOfSetAside'] ?? [],
            'limit' => $params['limit'] ?? 100,
        ];
    }

    /**
     * Process individual opportunity with strict validation
     */
    private function processOpportunity(array $data): array
    {
        // Apply hard validation guards from PRD §5
        $validation = $this->validateOpportunity($data);
        if (!$validation['valid']) {
            return ['action' => 'rejected', 'reason' => $validation['reason']];
        }

        // Extract and transform data
        $opportunityData = $this->transformOpportunityData($data);
        
        // Check if opportunity already exists
        $existing = Database::queryOne(
            'SELECT id FROM opportunities WHERE source = ? AND external_id = ?',
            ['SAM', $opportunityData['external_id']]
        );

        if ($existing) {
            return $this->updateExistingOpportunity($existing['id'], $opportunityData, $data);
        } else {
            return $this->createNewOpportunity($opportunityData, $data);
        }
    }

    /**
     * Apply strict validation guards per PRD §5
     */
    private function validateOpportunity(array $data): array
    {
        // 1. Check for required fields
        if (empty($data['title'])) {
            return ['valid' => false, 'reason' => 'Missing title'];
        }

        // 2. Check procurement type
        if (empty($data['type']) || empty($data['baseType'])) {
            return ['valid' => false, 'reason' => 'Missing procurement type'];
        }

        $procurementType = strtolower($data['baseType'] ?? '');
        if (!in_array($procurementType, $this->allowedProcurementTypes)) {
            return ['valid' => false, 'reason' => "Invalid procurement type: {$procurementType}"];
        }

        // 3. Check NAICS codes
        if (empty($data['naicsCode'])) {
            return ['valid' => false, 'reason' => 'Missing NAICS code'];
        }

        $naicsCodes = is_array($data['naicsCode']) ? $data['naicsCode'] : [$data['naicsCode']];
        $hasValidNaics = false;
        
        foreach ($naicsCodes as $naics) {
            if (in_array($naics, $this->allowedNaicsCodes)) {
                $hasValidNaics = true;
                break;
            }
        }

        if (!$hasValidNaics) {
            return ['valid' => false, 'reason' => 'No allowed NAICS codes found'];
        }

        // 4. Check for valid notice ID
        if (empty($data['noticeId'])) {
            return ['valid' => false, 'reason' => 'Missing notice ID'];
        }

        // 5. Check posted date
        if (empty($data['postedDate'])) {
            return ['valid' => false, 'reason' => 'Missing posted date'];
        }

        // 6. Check if opportunity is active (reject archived)
        if (isset($data['archive']) && $data['archive'] === true) {
            return ['valid' => false, 'reason' => 'Opportunity is archived'];
        }

        return ['valid' => true];
    }

    /**
     * Transform API data to database format
     */
    private function transformOpportunityData(array $data): array
    {
        return [
            'source' => 'SAM',
            'external_id' => $data['noticeId'],
            'title' => trim($data['title']),
            'posted_at' => $this->parseDate($data['postedDate']),
            'due_at' => $this->parseDate($data['reponseDeadLine'] ?? null),
            'notice_type' => $this->mapNoticeType($data['type'], $data['baseType']),
            'set_aside' => $data['setAside'] ?? $data['setAsideCode'] ?? null,
            'ui_link' => $data['uiLink'] ?? null,
            'agency_name' => $data['fullParentPathName'] ?? $data['organizationName'] ?? 'Unknown Agency',
            'naics_codes' => is_array($data['naicsCode']) ? $data['naicsCode'] : [$data['naicsCode']],
            'description_url' => $data['description'] ?? null,
            'resource_links' => $data['resourceLinks'] ?? [],
            'point_of_contact' => $data['pointOfContact'] ?? [],
        ];
    }

    /**
     * Create new opportunity
     */
    private function createNewOpportunity(array $opportunityData, array $originalData): array
    {
        Database::beginTransaction();

        try {
            // Create or find agency
            $agency = $this->findOrCreateAgency($opportunityData['agency_name']);

            // Fetch description
            $description = $this->fetchDescription($opportunityData['description_url']);

            // Insert opportunity
            $opportunityId = Database::execute(
                'INSERT INTO opportunities (
                    source, external_id, title, description, description_status,
                    agency_id, posted_at, due_at, notice_type, set_aside, ui_link,
                    score, score_reasons, status, active
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $opportunityData['source'],
                    $opportunityData['external_id'],
                    $opportunityData['title'],
                    $description['content'],
                    $description['status'],
                    $agency->id,
                    $opportunityData['posted_at'],
                    $opportunityData['due_at'],
                    $opportunityData['notice_type'],
                    $opportunityData['set_aside'],
                    $opportunityData['ui_link'],
                    0, // Initial score
                    null, // Score reasons
                    'New',
                    1, // Active
                ]
            );

            $opportunityId = (int) Database::lastInsertId();

            // Insert NAICS codes
            foreach ($opportunityData['naics_codes'] as $naicsCode) {
                Database::execute(
                    'INSERT INTO opportunity_naics (opportunity_id, naics_code) VALUES (?, ?)',
                    [$opportunityId, $naicsCode]
                );
            }

            // Create contacts
            foreach ($opportunityData['point_of_contact'] as $contactData) {
                $this->createContact($agency->id, $contactData);
            }

            // Handle attachments if enabled
            if (Config::get('attachment_fetching_enabled', false) && !empty($opportunityData['resource_links'])) {
                $this->processAttachments($opportunityId, $opportunityData['resource_links']);
            }

            // Calculate initial score
            $this->calculateOpportunityScore($opportunityId);

            Database::commit();

            Logger::info('Created new opportunity', [
                'opportunity_id' => $opportunityId,
                'external_id' => $opportunityData['external_id'],
                'title' => $opportunityData['title']
            ]);

            return ['action' => 'created', 'opportunity_id' => $opportunityId];

        } catch (\Exception $e) {
            Database::rollback();
            throw $e;
        }
    }

    /**
     * Update existing opportunity
     */
    private function updateExistingOpportunity(int $opportunityId, array $opportunityData, array $originalData): array
    {
        Database::beginTransaction();

        try {
            // Check if update is needed
            $existing = Database::queryOne(
                'SELECT * FROM opportunities WHERE id = ?',
                [$opportunityId]
            );

            $needsUpdate = false;
            $changes = [];

            // Check for changes in key fields
            if ($existing['title'] !== $opportunityData['title']) {
                $changes['title'] = ['old' => $existing['title'], 'new' => $opportunityData['title']];
                $needsUpdate = true;
            }

            if ($existing['due_at'] !== $opportunityData['due_at']) {
                $changes['due_at'] = ['old' => $existing['due_at'], 'new' => $opportunityData['due_at']];
                $needsUpdate = true;
            }

            // Fetch description if URL has changed
            $description = ['status' => 'ok', 'content' => $existing['description']];
            if (!empty($opportunityData['description_url'])) {
                $description = $this->fetchDescription($opportunityData['description_url']);
            }

            if ($needsUpdate || $description['status'] !== $existing['description_status']) {
                Database::execute(
                    'UPDATE opportunities SET 
                        title = ?, description = ?, description_status = ?,
                        due_at = ?, updated_at = NOW()
                     WHERE id = ?',
                    [
                        $opportunityData['title'],
                        $description['content'],
                        $description['status'],
                        $opportunityData['due_at'],
                        $opportunityId
                    ]
                );

                // Log changes
                if (!empty($changes)) {
                    Database::execute(
                        'INSERT INTO opportunity_changes (opportunity_id, payload) VALUES (?, ?)',
                        [$opportunityId, json_encode($changes)]
                    );
                }

                // Recalculate score
                $this->calculateOpportunityScore($opportunityId);

                Database::commit();

                Logger::info('Updated existing opportunity', [
                    'opportunity_id' => $opportunityId,
                    'external_id' => $opportunityData['external_id'],
                    'changes' => $changes
                ]);

                return ['action' => 'updated', 'opportunity_id' => $opportunityId];
            }

            Database::rollback();
            return ['action' => 'no_change', 'opportunity_id' => $opportunityId];

        } catch (\Exception $e) {
            Database::rollback();
            throw $e;
        }
    }

    /**
     * Find or create agency
     */
    private function findOrCreateAgency(string $agencyName): Agency
    {
        $agency = Database::queryOne(
            'SELECT * FROM agencies WHERE name = ?',
            [$agencyName]
        );

        if ($agency) {
            return Agency::fromArray($agency);
        }

        // Create new agency
        Database::execute(
            'INSERT INTO agencies (name, type) VALUES (?, ?)',
            [$agencyName, 'Federal']
        );

        $agencyId = (int) Database::lastInsertId();
        return Agency::findById($agencyId);
    }

    /**
     * Create contact
     */
    private function createContact(int $agencyId, array $contactData): void
    {
        $name = trim($contactData['fullName'] ?? '');
        $email = trim($contactData['email'] ?? '');
        $phone = trim($contactData['phone'] ?? '');
        $title = trim($contactData['title'] ?? '');

        if (empty($name) && empty($email)) {
            return; // Skip invalid contacts
        }

        // Check if contact already exists
        $existing = Database::queryOne(
            'SELECT id FROM contacts WHERE agency_id = ? AND email = ?',
            [$agencyId, $email]
        );

        if (!$existing) {
            Database::execute(
                'INSERT INTO contacts (agency_id, name, email, phone, title) VALUES (?, ?, ?, ?, ?)',
                [$agencyId, $name, $email, $phone, $title]
            );
        }
    }

    /**
     * Fetch description from URL
     */
    private function fetchDescription(?string $url): array
    {
        if (empty($url)) {
            return ['status' => 'missing', 'content' => null];
        }

        return $this->samApi->fetchDescription($url);
    }

    /**
     * Process attachments
     */
    private function processAttachments(int $opportunityId, array $resourceLinks): void
    {
        foreach ($resourceLinks as $link) {
            try {
                $url = $link['link'] ?? '';
                $label = $link['description'] ?? 'Attachment';

                if (empty($url)) {
                    continue;
                }

                // Generate file path
                $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'pdf';
                $filename = Security::generateRandomString(32) . '.' . $extension;
                $uploadPath = Config::get('upload.path', '/workspace/uploads');
                $filePath = $uploadPath . '/attachments/' . $filename;

                // Download file
                $result = $this->samApi->downloadAttachment($url, $filePath);
                
                if ($result['success']) {
                    // Create file record
                    Database::execute(
                        'INSERT INTO files (path, original_name, mime_type, size, sha256, source_url) VALUES (?, ?, ?, ?, ?, ?)',
                        [
                            $filePath,
                            $label,
                            mime_content_type($filePath),
                            $result['size'],
                            Security::generateFileHash($filePath),
                            $url
                        ]
                    );

                    $fileId = (int) Database::lastInsertId();

                    // Link to opportunity
                    Database::execute(
                        'INSERT INTO documents (opportunity_id, file_id, label) VALUES (?, ?, ?)',
                        [$opportunityId, $fileId, $label]
                    );
                }
            } catch (\Exception $e) {
                Logger::warning('Failed to process attachment', [
                    'opportunity_id' => $opportunityId,
                    'url' => $url ?? 'unknown',
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Calculate opportunity score
     */
    private function calculateOpportunityScore(int $opportunityId): void
    {
        $opportunity = Database::queryOne(
            'SELECT o.*, a.is_preferred FROM opportunities o 
             LEFT JOIN agencies a ON o.agency_id = a.id 
             WHERE o.id = ?',
            [$opportunityId]
        );

        $score = 0;
        $reasons = [];

        // +30 for NAICS match (already validated in ingestion)
        $score += 30;
        $reasons[] = 'NAICS code matches allowlist (+30)';

        // +25 for set-aside match (if applicable)
        if (!empty($opportunity['set_aside']) && in_array($opportunity['set_aside'], ['WOSB', 'EDWOSB'])) {
            $score += 25;
            $reasons[] = 'Set-aside match (+25)';
        }

        // +20 for preferred agency
        if ($opportunity['is_preferred']) {
            $score += 20;
            $reasons[] = 'Preferred agency (+20)';
        }

        // +10 if due date is >= 7 days away, -20 if <= 3 days
        if ($opportunity['due_at']) {
            $daysUntilDue = (strtotime($opportunity['due_at']) - time()) / (24 * 60 * 60);
            
            if ($daysUntilDue >= 7) {
                $score += 10;
                $reasons[] = 'Adequate response time (+10)';
            } elseif ($daysUntilDue <= 3) {
                $score -= 20;
                $reasons[] = 'Limited response time (-20)';
            }
        }

        // Clamp score between 0-100
        $score = max(0, min(100, $score));

        // Update score
        Database::execute(
            'UPDATE opportunities SET score = ?, score_reasons = ? WHERE id = ?',
            [$score, json_encode($reasons), $opportunityId]
        );
    }

    /**
     * Parse date string to MySQL format
     */
    private function parseDate(?string $dateString): ?string
    {
        if (empty($dateString)) {
            return null;
        }

        try {
            $date = new \DateTime($dateString);
            return $date->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            Logger::warning('Failed to parse date', ['date' => $dateString]);
            return null;
        }
    }

    /**
     * Map API notice type to database enum
     */
    private function mapNoticeType(string $type, string $baseType): string
    {
        $mapping = [
            'Solicitation' => 'Solicitation',
            'Combined Synopsis/Solicitation' => 'Combined',
            'Combined' => 'Combined',
        ];

        return $mapping[$type] ?? $mapping[$baseType] ?? 'Solicitation';
    }
}