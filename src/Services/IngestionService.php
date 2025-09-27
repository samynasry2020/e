<?php

declare(strict_types=1);

namespace GovTribe\Services;

use GovTribe\Core\Database;
use GovTribe\Models\Opportunity;

class IngestionService
{
    private SamApiClient $samClient;
    private array $ingestionLog = [];
    private int $created = 0;
    private int $updated = 0;
    private int $rejected = 0;
    private bool $dryRun = false;

    public function __construct(bool $dryRun = false)
    {
        $this->samClient = new SamApiClient();
        $this->dryRun = $dryRun;
    }

    public function ingestOpportunities(array $filters = []): array
    {
        $this->resetCounters();
        
        try {
            // Apply default filters if not provided
            $filters = $this->applyDefaultFilters($filters);
            
            // Fetch opportunities from SAM API
            $apiResult = $this->samClient->searchOpportunities($filters);
            
            foreach ($apiResult['opportunities'] as $opportunityData) {
                $this->processOpportunity($opportunityData);
            }
            
            $result = [
                'success' => true,
                'dry_run' => $this->dryRun,
                'summary' => [
                    'total_fetched' => count($apiResult['opportunities']),
                    'created' => $this->created,
                    'updated' => $this->updated,
                    'rejected' => $this->rejected,
                    'api_total' => $apiResult['metadata']['total_api_records'] ?? 0
                ],
                'filters_used' => $filters,
                'ingestion_log' => $this->ingestionLog
            ];
            
            if (!$this->dryRun) {
                $this->logIngestionResult($result);
            }
            
            return $result;
            
        } catch (\Exception $e) {
            $error = [
                'success' => false,
                'error' => $e->getMessage(),
                'dry_run' => $this->dryRun,
                'summary' => [
                    'total_fetched' => 0,
                    'created' => $this->created,
                    'updated' => $this->updated,
                    'rejected' => $this->rejected
                ],
                'ingestion_log' => $this->ingestionLog
            ];
            
            if (!$this->dryRun) {
                $this->logIngestionResult($error);
            }
            
            error_log("Ingestion failed: " . $e->getMessage());
            return $error;
        }
    }

    private function processOpportunity(array $opportunityData): void
    {
        try {
            // Apply strict validation guards
            $validation = $this->validateOpportunityData($opportunityData);
            if (!$validation['valid']) {
                $this->rejected++;
                $this->addToLog('rejected', $opportunityData['external_id'] ?? 'unknown', $validation['reason']);
                return;
            }

            // Check if opportunity already exists
            $existing = $this->findExistingOpportunity($opportunityData['external_id']);
            
            if ($existing) {
                $this->updateExistingOpportunity($existing, $opportunityData);
            } else {
                $this->createNewOpportunity($opportunityData);
            }
            
        } catch (\Exception $e) {
            $this->rejected++;
            $this->addToLog('error', $opportunityData['external_id'] ?? 'unknown', $e->getMessage());
        }
    }

    private function validateOpportunityData(array $data): array
    {
        // Guard 1: Required fields present
        if (empty($data['title'])) {
            return ['valid' => false, 'reason' => 'Missing title'];
        }

        if (empty($data['external_id'])) {
            return ['valid' => false, 'reason' => 'Missing external_id'];
        }

        // Guard 2: Valid notice type
        $allowedTypes = ['Solicitation', 'Combined'];
        if (!in_array($data['notice_type'], $allowedTypes)) {
            return ['valid' => false, 'reason' => "Invalid notice type: {$data['notice_type']}"];
        }

        // Guard 3: NAICS code validation
        if (!empty($data['naics_code'])) {
            $allowedNaics = $this->getAllowedNaicsCodes();
            if (!in_array($data['naics_code'], $allowedNaics)) {
                return ['valid' => false, 'reason' => "NAICS code not in allowlist: {$data['naics_code']}"];
            }
        }

        // Guard 4: Active opportunities only
        if (isset($data['active']) && !$data['active']) {
            return ['valid' => false, 'reason' => 'Opportunity is not active'];
        }

        // Guard 5: Valid dates
        if (!empty($data['posted_at']) && !$this->isValidDate($data['posted_at'])) {
            return ['valid' => false, 'reason' => 'Invalid posted date'];
        }

        if (!empty($data['due_at']) && !$this->isValidDate($data['due_at'])) {
            return ['valid' => false, 'reason' => 'Invalid due date'];
        }

        return ['valid' => true, 'reason' => ''];
    }

    private function createNewOpportunity(array $data): void
    {
        if ($this->dryRun) {
            $this->created++;
            $this->addToLog('would_create', $data['external_id'], 'New opportunity');
            return;
        }

        Database::beginTransaction();
        
        try {
            // Create or find agency
            $agencyId = $this->findOrCreateAgency($data['agency_name']);
            
            // Prepare opportunity data
            $opportunityData = [
                'source' => 'SAM',
                'external_id' => $data['external_id'],
                'title' => $data['title'],
                'agency_id' => $agencyId,
                'posted_at' => $data['posted_at'],
                'due_at' => $data['due_at'],
                'notice_type' => $data['notice_type'],
                'set_aside' => $data['set_aside'],
                'ui_link' => $data['ui_link'],
                'active' => $data['active'] ? 1 : 0,
                'status' => 'New'
            ];

            // Handle description with strict no-fake policy
            $descriptionResult = $this->processDescription($data['description_url'] ?? '');
            $opportunityData['description'] = $descriptionResult['content'];
            $opportunityData['description_status'] = $descriptionResult['status'];

            // Calculate initial score
            $scoreResult = $this->calculateScore($data);
            $opportunityData['score'] = $scoreResult['score'];
            $opportunityData['score_reasons'] = $scoreResult['reasons'];

            // Insert opportunity
            $opportunityId = Database::insert('opportunities', $opportunityData);

            // Insert NAICS codes
            if (!empty($data['naics_code'])) {
                Database::insert('opportunity_naics', [
                    'opportunity_id' => $opportunityId,
                    'naics_code' => $data['naics_code']
                ]);
            }

            // Insert contacts
            $this->insertContacts($opportunityId, $data['point_of_contact'] ?? []);

            // Process attachments with strict validation
            $this->processAttachments($opportunityId, $data['resource_links'] ?? []);

            Database::commit();
            
            $this->created++;
            $this->addToLog('created', $data['external_id'], 'Successfully created');
            
        } catch (\Exception $e) {
            Database::rollback();
            throw $e;
        }
    }

    private function updateExistingOpportunity(array $existing, array $data): void
    {
        // Check if update is needed
        $changes = $this->detectChanges($existing, $data);
        if (empty($changes)) {
            $this->addToLog('no_change', $data['external_id'], 'No updates needed');
            return;
        }

        if ($this->dryRun) {
            $this->updated++;
            $this->addToLog('would_update', $data['external_id'], 'Changes detected: ' . implode(', ', array_keys($changes)));
            return;
        }

        Database::beginTransaction();
        
        try {
            // Update opportunity
            Database::update('opportunities', $changes, ['id' => $existing['id']]);

            // Log changes
            Database::insert('opportunity_changes', [
                'opportunity_id' => $existing['id'],
                'payload' => json_encode([
                    'changes' => $changes,
                    'source' => 'ingestion',
                    'timestamp' => date('Y-m-d H:i:s')
                ])
            ]);

            Database::commit();
            
            $this->updated++;
            $this->addToLog('updated', $data['external_id'], 'Updated: ' . implode(', ', array_keys($changes)));
            
        } catch (\Exception $e) {
            Database::rollback();
            throw $e;
        }
    }

    private function processDescription(string $descriptionUrl): array
    {
        if (empty($descriptionUrl)) {
            return ['content' => '', 'status' => 'missing'];
        }

        $result = $this->samClient->fetchDescription($descriptionUrl);
        
        // Never create fake descriptions - return exactly what we got
        return [
            'content' => $result['content'],
            'status' => $result['status']
        ];
    }

    private function processAttachments(int $opportunityId, array $resourceLinks): void
    {
        if (empty($resourceLinks)) {
            // No attachments available - don't create phantom documents
            return;
        }

        $fetchAttachments = $this->shouldFetchAttachments();
        
        foreach ($resourceLinks as $link) {
            $documentData = [
                'opportunity_id' => $opportunityId,
                'label' => $link['name'] ?? $link['title'] ?? 'Attachment',
                'source_url' => $link['url'] ?? null
            ];

            if ($fetchAttachments && !empty($link['url'])) {
                try {
                    $fileId = $this->downloadAndStoreFile($link['url'], $link['name'] ?? null);
                    if ($fileId) {
                        $documentData['file_id'] = $fileId;
                    }
                } catch (\Exception $e) {
                    error_log("Failed to download attachment: " . $e->getMessage());
                    // Continue with just the URL reference
                }
            }

            Database::insert('documents', $documentData);
        }
    }

    private function downloadAndStoreFile(string $url, ?string $originalName): ?int
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_USERAGENT => 'GovTribe Platform/1.0',
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $content = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error || $httpCode !== 200 || empty($content)) {
            return null;
        }

        // Generate file path and name
        $hash = hash('sha256', $content);
        $extension = $this->getExtensionFromMime($contentType) ?: 'bin';
        $filename = $hash . '.' . $extension;
        $directory = date('Y/m');
        $fullDir = rtrim(Config::get('files.root'), '/') . '/' . $directory;
        
        if (!is_dir($fullDir)) {
            mkdir($fullDir, 0755, true);
        }
        
        $fullPath = $fullDir . '/' . $filename;
        
        if (file_put_contents($fullPath, $content) === false) {
            return null;
        }

        // Store file record
        return Database::insert('files', [
            'path' => $directory . '/' . $filename,
            'original_name' => $originalName ?: $filename,
            'mime' => $contentType ?: 'application/octet-stream',
            'size' => strlen($content),
            'sha256' => $hash,
            'source_url' => $url
        ]);
    }

    private function calculateScore(array $data): array
    {
        $score = 0;
        $reasons = [];

        // NAICS match (+30)
        if (!empty($data['naics_code']) && in_array($data['naics_code'], $this->getAllowedNaicsCodes())) {
            $score += 30;
            $reasons[] = "NAICS match ({$data['naics_code']})";
        }

        // Set-aside match (+25)
        $allowedSetAsides = $this->getAllowedSetAsideTypes();
        if (!empty($data['set_aside_code']) && in_array($data['set_aside_code'], $allowedSetAsides)) {
            $score += 25;
            $reasons[] = "Set-aside match ({$data['set_aside_code']})";
        }

        // Preferred agency (+20)
        $preferredAgencies = $this->getPreferredAgencies();
        if (in_array($data['agency_name'], $preferredAgencies)) {
            $score += 20;
            $reasons[] = "Preferred agency";
        }

        // Due date scoring
        if (!empty($data['due_at'])) {
            $dueDate = new \DateTime($data['due_at']);
            $now = new \DateTime();
            $daysDiff = $now->diff($dueDate)->days;
            
            if ($dueDate > $now) {
                if ($daysDiff >= 7) {
                    $score += 10;
                    $reasons[] = "Due date ≥7 days away";
                } elseif ($daysDiff <= 3) {
                    $score -= 20;
                    $reasons[] = "Due date ≤3 days away";
                }
            } else {
                $score -= 30;
                $reasons[] = "Past due date";
            }
        }

        return [
            'score' => max(0, min(100, $score)),
            'reasons' => implode('; ', $reasons)
        ];
    }

    private function findExistingOpportunity(string $externalId): ?array
    {
        return Database::fetchOne(
            'SELECT * FROM opportunities WHERE source = ? AND external_id = ?',
            ['SAM', $externalId]
        );
    }

    private function findOrCreateAgency(string $agencyName): int
    {
        $existing = Database::fetchOne('SELECT id FROM agencies WHERE name = ?', [$agencyName]);
        if ($existing) {
            return $existing['id'];
        }

        return Database::insert('agencies', [
            'name' => $agencyName,
            'type' => 'Federal'
        ]);
    }

    private function insertContacts(int $opportunityId, array $contacts): void
    {
        foreach ($contacts as $contact) {
            if (empty($contact['email']) && empty($contact['phone'])) {
                continue; // Skip contacts without any contact info
            }

            Database::insert('contacts', [
                'opportunity_id' => $opportunityId,
                'name' => $contact['fullName'] ?? null,
                'email' => $contact['email'] ?? null,
                'phone' => $contact['phone'] ?? null,
                'title' => $contact['title'] ?? null
            ]);
        }
    }

    private function detectChanges(array $existing, array $new): array
    {
        $changes = [];
        
        $fieldsToCheck = ['title', 'due_at', 'set_aside', 'active'];
        
        foreach ($fieldsToCheck as $field) {
            $existingValue = $existing[$field] ?? null;
            $newValue = $new[$field] ?? null;
            
            if ($existingValue != $newValue) {
                $changes[$field] = $newValue;
            }
        }

        return $changes;
    }

    private function applyDefaultFilters(array $filters): array
    {
        if (empty($filters['postedFrom']) || empty($filters['postedTo'])) {
            $defaultDays = $this->getDefaultPostedDays();
            $filters['postedFrom'] = date('m/d/Y', strtotime("-{$defaultDays} days"));
            $filters['postedTo'] = date('m/d/Y');
        }

        if (empty($filters['ptype'])) {
            $filters['ptype'] = ['o', 'k']; // Solicitation and Combined only
        }

        if (empty($filters['ncode'])) {
            $filters['ncode'] = $this->getAllowedNaicsCodes();
        }

        return $filters;
    }

    private function getAllowedNaicsCodes(): array
    {
        $setting = Database::fetchOne('SELECT value FROM settings WHERE `key` = ?', ['default_naics_codes']);
        return $setting ? json_decode($setting['value'], true) : ['334111', '541512'];
    }

    private function getAllowedSetAsideTypes(): array
    {
        $setting = Database::fetchOne('SELECT value FROM settings WHERE `key` = ?', ['default_set_aside_types']);
        return $setting ? json_decode($setting['value'], true) : ['WOSB', 'EDWOSB'];
    }

    private function getPreferredAgencies(): array
    {
        $setting = Database::fetchOne('SELECT value FROM settings WHERE `key` = ?', ['preferred_agencies']);
        return $setting ? json_decode($setting['value'], true) : [];
    }

    private function getDefaultPostedDays(): int
    {
        $setting = Database::fetchOne('SELECT value FROM settings WHERE `key` = ?', ['default_posted_days']);
        return $setting ? (int)$setting['value'] : 30;
    }

    private function shouldFetchAttachments(): bool
    {
        $setting = Database::fetchOne('SELECT value FROM settings WHERE `key` = ?', ['fetch_attachments']);
        return $setting ? filter_var($setting['value'], FILTER_VALIDATE_BOOLEAN) : true;
    }

    private function isValidDate(string $date): bool
    {
        return \DateTime::createFromFormat('Y-m-d H:i:s', $date) !== false ||
               \DateTime::createFromFormat('Y-m-d', $date) !== false;
    }

    private function getExtensionFromMime(string $mime): ?string
    {
        $mimeMap = [
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'text/plain' => 'txt'
        ];

        return $mimeMap[$mime] ?? null;
    }

    private function resetCounters(): void
    {
        $this->created = 0;
        $this->updated = 0;
        $this->rejected = 0;
        $this->ingestionLog = [];
    }

    private function addToLog(string $action, string $externalId, string $message): void
    {
        $this->ingestionLog[] = [
            'action' => $action,
            'external_id' => $externalId,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }

    private function logIngestionResult(array $result): void
    {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'success' => $result['success'],
            'summary' => $result['summary'],
            'error' => $result['error'] ?? null
        ];

        // Store in settings as last ingestion result
        Database::query(
            'INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)',
            ['last_ingestion_result', json_encode($logData)]
        );

        // Also log to file
        $logFile = __DIR__ . '/../../storage/logs/ingestion.log';
        $logDir = dirname($logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        file_put_contents($logFile, json_encode($logData) . "\n", FILE_APPEND | LOCK_EX);
    }

    public function getLastIngestionResult(): ?array
    {
        $setting = Database::fetchOne('SELECT value FROM settings WHERE `key` = ?', ['last_ingestion_result']);
        return $setting ? json_decode($setting['value'], true) : null;
    }
}