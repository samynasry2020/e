<?php

declare(strict_types=1);

namespace GovTribe\Services;

use GovTribe\Models\Opportunity;
use GovTribe\Models\Agency;
use GovTribe\Models\Contact;
use GovTribe\Models\File;
use GovTribe\Models\Document;
use GovTribe\Utils\Database;
use GovTribe\Utils\Logger;
use GovTribe\Utils\Security;
use DateTime;
use RuntimeException;

/**
 * Ingestion Service - Implements hard guards per PRD requirements
 * Never saves junk or "fake" opportunities
 */
class IngestionService
{
    private Logger $logger;
    private SamApiService $samApi;
    private array $settings;
    private array $stats;

    public function __construct(SamApiService $samApi, array $settings = [])
    {
        $this->logger = Logger::getInstance();
        $this->samApi = $samApi;
        $this->settings = $settings;
        $this->stats = [
            'fetched' => 0,
            'created' => 0,
            'updated' => 0,
            'rejected' => 0,
            'errors' => []
        ];
    }

    /**
     * Run ingestion process with hard guards
     */
    public function ingest(bool $dryRun = false): array
    {
        $this->logger->info('Starting SAM.gov ingestion', [
            'dry_run' => $dryRun,
            'settings' => $this->settings
        ]);

        try {
            // Validate configuration
            $this->validateConfiguration();

            // Build search parameters
            $searchParams = $this->buildSearchParams();

            // Fetch opportunities from API
            $response = $this->samApi->searchOpportunities($searchParams);
            $this->stats['fetched'] = $response['total_records'] ?? 0;

            $this->logger->info('Fetched opportunities from API', [
                'total_fetched' => $this->stats['fetched'],
                'errors' => $response['errors'] ?? []
            ]);

            // Process each opportunity with hard guards
            foreach ($response['opportunities'] as $opportunityData) {
                $this->processOpportunity($opportunityData, $dryRun);
            }

            // Log final statistics
            $this->logger->info('Ingestion completed', $this->stats);

            return $this->stats;

        } catch (\Exception $e) {
            $this->logger->error('Ingestion failed', [
                'error' => $e->getMessage(),
                'stats' => $this->stats
            ]);
            
            $this->stats['errors'][] = $e->getMessage();
            throw $e;
        }
    }

    /**
     * Process individual opportunity with hard guards
     */
    private function processOpportunity(array $data, bool $dryRun = false): void
    {
        try {
            // HARD GUARD 1: Validate required fields
            if (!$this->validateRequiredFields($data)) {
                $this->stats['rejected']++;
                return;
            }

            // HARD GUARD 2: Validate date window
            if (!$this->validateDateWindow($data)) {
                $this->stats['rejected']++;
                return;
            }

            // HARD GUARD 3: Validate procurement type
            if (!$this->validateProcurementType($data)) {
                $this->stats['rejected']++;
                return;
            }

            // HARD GUARD 4: Validate NAICS codes
            if (!$this->validateNaicsCodes($data)) {
                $this->stats['rejected']++;
                return;
            }

            // HARD GUARD 5: Validate set-aside codes
            if (!$this->validateSetAsideCodes($data)) {
                $this->stats['rejected']++;
                return;
            }

            // HARD GUARD 6: Validate active status
            if (!$this->validateActiveStatus($data)) {
                $this->stats['rejected']++;
                return;
            }

            if ($dryRun) {
                $this->logger->info('DRY RUN: Would process opportunity', [
                    'notice_id' => $data['noticeId'],
                    'title' => $data['title']
                ]);
                return;
            }

            // Check for existing opportunity (deduplication)
            $existing = $this->findExistingOpportunity($data['noticeId']);

            if ($existing) {
                // HARD GUARD 7: Update only if newer data
                if ($this->shouldUpdateOpportunity($existing, $data)) {
                    $this->updateOpportunity($existing, $data);
                    $this->stats['updated']++;
                }
            } else {
                // Create new opportunity
                $this->createOpportunity($data);
                $this->stats['created']++;
            }

        } catch (\Exception $e) {
            $this->stats['rejected']++;
            $this->stats['errors'][] = "Failed to process {$data['noticeId']}: " . $e->getMessage();
            
            $this->logger->error('Failed to process opportunity', [
                'notice_id' => $data['noticeId'] ?? 'unknown',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * HARD GUARD 1: Validate required fields
     */
    private function validateRequiredFields(array $data): bool
    {
        $required = ['noticeId', 'title', 'postedDate', 'type', 'baseType'];
        
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $this->logger->warning('Rejected opportunity: missing required field', [
                    'notice_id' => $data['noticeId'] ?? 'unknown',
                    'missing_field' => $field
                ]);
                return false;
            }
        }

        return true;
    }

    /**
     * HARD GUARD 2: Validate date window (within 365 days)
     */
    private function validateDateWindow(array $data): bool
    {
        try {
            $postedDate = DateTime::createFromFormat('m/d/Y', $data['postedDate']);
            if (!$postedDate) {
                $this->logger->warning('Rejected opportunity: invalid date format', [
                    'notice_id' => $data['noticeId'],
                    'posted_date' => $data['postedDate']
                ]);
                return false;
            }

            $now = new DateTime();
            $daysDiff = $now->diff($postedDate)->days;

            if ($daysDiff > 365) {
                $this->logger->warning('Rejected opportunity: posted date too old', [
                    'notice_id' => $data['noticeId'],
                    'posted_date' => $data['postedDate'],
                    'days_ago' => $daysDiff
                ]);
                return false;
            }

            return true;

        } catch (\Exception $e) {
            $this->logger->warning('Rejected opportunity: date validation error', [
                'notice_id' => $data['noticeId'],
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * HARD GUARD 3: Validate procurement type (only 'o' and 'k')
     */
    private function validateProcurementType(array $data): bool
    {
        $allowedTypes = ['o', 'k']; // Solicitation and Combined only
        
        if (!in_array($data['baseType'], $allowedTypes, true)) {
            $this->logger->warning('Rejected opportunity: invalid procurement type', [
                'notice_id' => $data['noticeId'],
                'base_type' => $data['baseType']
            ]);
            return false;
        }

        return true;
    }

    /**
     * HARD GUARD 4: Validate NAICS codes against allowlist
     */
    private function validateNaicsCodes(array $data): bool
    {
        $allowedNaics = $this->settings['allowed_naics_codes'] ?? ['334111', '541512'];
        
        if (empty($data['naicsCode'])) {
            $this->logger->warning('Rejected opportunity: no NAICS code', [
                'notice_id' => $data['noticeId']
            ]);
            return false;
        }

        $naicsCodes = is_array($data['naicsCode']) ? $data['naicsCode'] : [$data['naicsCode']];
        
        foreach ($naicsCodes as $code) {
            if (!in_array($code, $allowedNaics, true)) {
                $this->logger->warning('Rejected opportunity: NAICS code not allowed', [
                    'notice_id' => $data['noticeId'],
                    'naics_code' => $code,
                    'allowed_codes' => $allowedNaics
                ]);
                return false;
            }
        }

        return true;
    }

    /**
     * HARD GUARD 5: Validate set-aside codes (if filtering enabled)
     */
    private function validateSetAsideCodes(array $data): bool
    {
        if (!($this->settings['filter_set_aside'] ?? false)) {
            return true; // No filtering, accept all
        }

        $allowedSetAsides = $this->settings['allowed_set_aside_codes'] ?? ['WOSB', 'EDWOSB'];
        
        if (empty($data['setAsideCode'])) {
            $this->logger->warning('Rejected opportunity: no set-aside code', [
                'notice_id' => $data['noticeId']
            ]);
            return false;
        }

        if (!in_array($data['setAsideCode'], $allowedSetAsides, true)) {
            $this->logger->warning('Rejected opportunity: set-aside code not allowed', [
                'notice_id' => $data['noticeId'],
                'set_aside_code' => $data['setAsideCode'],
                'allowed_codes' => $allowedSetAsides
            ]);
            return false;
        }

        return true;
    }

    /**
     * HARD GUARD 6: Validate active status
     */
    private function validateActiveStatus(array $data): bool
    {
        // Only process active opportunities (reject archived)
        if (isset($data['archive']) && $data['archive'] === true) {
            $this->logger->warning('Rejected opportunity: archived', [
                'notice_id' => $data['noticeId']
            ]);
            return false;
        }

        return true;
    }

    /**
     * Find existing opportunity by notice ID
     */
    private function findExistingOpportunity(string $noticeId): ?array
    {
        $sql = "SELECT * FROM opportunities WHERE source = 'SAM' AND external_id = :notice_id LIMIT 1";
        return Database::fetchOne($sql, ['notice_id' => $noticeId]);
    }

    /**
     * HARD GUARD 7: Check if opportunity should be updated
     */
    private function shouldUpdateOpportunity(array $existing, array $new): bool
    {
        // Always update if external data is newer
        if (!empty($new['lastModified'])) {
            $newModified = new DateTime($new['lastModified']);
            $existingModified = new DateTime($existing['updated_at']);
            
            if ($newModified > $existingModified) {
                return true;
            }
        }

        // Update if critical fields have changed
        $criticalFields = ['title', 'reponseDeadLine', 'setAsideCode'];
        
        foreach ($criticalFields as $field) {
            $existingValue = $existing[$field] ?? '';
            $newValue = $new[$field] ?? '';
            
            if ($existingValue !== $newValue) {
                return true;
            }
        }

        return false;
    }

    /**
     * Create new opportunity
     */
    private function createOpportunity(array $data): void
    {
        Database::beginTransaction();

        try {
            // Create or find agency
            $agencyId = $this->createOrFindAgency($data);

            // Create opportunity
            $opportunityId = $this->insertOpportunity($data, $agencyId);

            // Create NAICS associations
            $this->createNaicsAssociations($opportunityId, $data['naicsCode'] ?? []);

            // Create contacts
            $this->createContacts($agencyId, $data['pointOfContact'] ?? []);

            // Fetch and store description
            $this->fetchAndStoreDescription($opportunityId, $data['description'] ?? '');

            // Fetch and store attachments
            $this->fetchAndStoreAttachments($opportunityId, $data['resourceLinks'] ?? []);

            Database::commit();

            $this->logger->info('Created new opportunity', [
                'opportunity_id' => $opportunityId,
                'notice_id' => $data['noticeId'],
                'title' => $data['title']
            ]);

        } catch (\Exception $e) {
            Database::rollback();
            throw $e;
        }
    }

    /**
     * Update existing opportunity
     */
    private function updateOpportunity(array $existing, array $data): void
    {
        Database::beginTransaction();

        try {
            // Update opportunity
            $this->updateOpportunityRecord($existing['id'], $data);

            // Update NAICS associations if changed
            $this->updateNaicsAssociations($existing['id'], $data['naicsCode'] ?? []);

            // Update contacts if changed
            if (!empty($data['pointOfContact'])) {
                $this->updateContacts($existing['agency_id'], $data['pointOfContact']);
            }

            // Re-fetch description if URL changed
            if (($data['description'] ?? '') !== ($existing['description_url'] ?? '')) {
                $this->fetchAndStoreDescription($existing['id'], $data['description'] ?? '');
            }

            // Log the change
            $this->logOpportunityChange($existing['id'], $data);

            Database::commit();

            $this->logger->info('Updated opportunity', [
                'opportunity_id' => $existing['id'],
                'notice_id' => $data['noticeId'],
                'title' => $data['title']
            ]);

        } catch (\Exception $e) {
            Database::rollback();
            throw $e;
        }
    }

    /**
     * Insert opportunity record
     */
    private function insertOpportunity(array $data, int $agencyId): int
    {
        $sql = "INSERT INTO opportunities (
            source, external_id, title, agency_id, posted_at, due_at, 
            notice_type, set_aside, ui_link, active, created_at, updated_at
        ) VALUES (
            :source, :external_id, :title, :agency_id, :posted_at, :due_at,
            :notice_type, :set_aside, :ui_link, :active, NOW(), NOW()
        )";

        $postedAt = DateTime::createFromFormat('m/d/Y', $data['postedDate'])->format('Y-m-d H:i:s');
        $dueAt = !empty($data['reponseDeadLine']) 
            ? DateTime::createFromFormat('m/d/Y', $data['reponseDeadLine'])->format('Y-m-d H:i:s')
            : null;

        Database::execute($sql, [
            'source' => 'SAM',
            'external_id' => $data['noticeId'],
            'title' => $data['title'],
            'agency_id' => $agencyId,
            'posted_at' => $postedAt,
            'due_at' => $dueAt,
            'notice_type' => $data['type'],
            'set_aside' => $data['setAsideCode'] ?? '',
            'ui_link' => $data['uiLink'] ?? '',
            'active' => 1
        ]);

        return (int) Database::lastInsertId();
    }

    /**
     * Update opportunity record
     */
    private function updateOpportunityRecord(int $id, array $data): void
    {
        $sql = "UPDATE opportunities SET 
            title = :title,
            due_at = :due_at,
            set_aside = :set_aside,
            ui_link = :ui_link,
            updated_at = NOW()
            WHERE id = :id";

        $dueAt = !empty($data['reponseDeadLine']) 
            ? DateTime::createFromFormat('m/d/Y', $data['reponseDeadLine'])->format('Y-m-d H:i:s')
            : null;

        Database::execute($sql, [
            'id' => $id,
            'title' => $data['title'],
            'due_at' => $dueAt,
            'set_aside' => $data['setAsideCode'] ?? '',
            'ui_link' => $data['uiLink'] ?? ''
        ]);
    }

    /**
     * Create or find agency
     */
    private function createOrFindAgency(array $data): int
    {
        $agencyName = $data['fullParentPathName'] ?? $data['organizationName'] ?? 'Unknown Agency';
        
        // Try to find existing agency
        $sql = "SELECT id FROM agencies WHERE name = :name LIMIT 1";
        $existing = Database::fetchOne($sql, ['name' => $agencyName]);
        
        if ($existing) {
            return (int) $existing['id'];
        }

        // Create new agency
        $sql = "INSERT INTO agencies (name, type, created_at, updated_at) VALUES (:name, :type, NOW(), NOW())";
        Database::execute($sql, [
            'name' => $agencyName,
            'type' => 'Federal'
        ]);

        return (int) Database::lastInsertId();
    }

    /**
     * Create NAICS associations
     */
    private function createNaicsAssociations(int $opportunityId, array $naicsCodes): void
    {
        if (empty($naicsCodes)) {
            return;
        }

        $codes = is_array($naicsCodes) ? $naicsCodes : [$naicsCodes];

        foreach ($codes as $code) {
            $sql = "INSERT IGNORE INTO opportunity_naics (opportunity_id, naics_code) VALUES (:opportunity_id, :naics_code)";
            Database::execute($sql, [
                'opportunity_id' => $opportunityId,
                'naics_code' => $code
            ]);
        }
    }

    /**
     * Update NAICS associations
     */
    private function updateNaicsAssociations(int $opportunityId, array $naicsCodes): void
    {
        // Remove existing associations
        $sql = "DELETE FROM opportunity_naics WHERE opportunity_id = :opportunity_id";
        Database::execute($sql, ['opportunity_id' => $opportunityId]);

        // Create new associations
        $this->createNaicsAssociations($opportunityId, $naicsCodes);
    }

    /**
     * Create contacts
     */
    private function createContacts(int $agencyId, array $contacts): void
    {
        foreach ($contacts as $contact) {
            if (empty($contact['email']) && empty($contact['phone'])) {
                continue; // Skip contacts without contact info
            }

            $sql = "INSERT IGNORE INTO contacts (agency_id, name, email, phone, title, created_at, updated_at) 
                    VALUES (:agency_id, :name, :email, :phone, :title, NOW(), NOW())";
            
            Database::execute($sql, [
                'agency_id' => $agencyId,
                'name' => $contact['fullName'] ?? '',
                'email' => $contact['email'] ?? '',
                'phone' => $contact['phone'] ?? '',
                'title' => $contact['title'] ?? ''
            ]);
        }
    }

    /**
     * Update contacts
     */
    private function updateContacts(int $agencyId, array $contacts): void
    {
        // For now, just add new contacts (don't remove existing ones)
        $this->createContacts($agencyId, $contacts);
    }

    /**
     * Fetch and store description content
     */
    private function fetchAndStoreDescription(int $opportunityId, string $descriptionUrl): void
    {
        if (empty($descriptionUrl)) {
            return;
        }

        try {
            $result = $this->samApi->fetchDescription($descriptionUrl);
            
            $sql = "UPDATE opportunities SET 
                description = :description,
                description_status = :status,
                updated_at = NOW()
                WHERE id = :id";

            Database::execute($sql, [
                'id' => $opportunityId,
                'description' => $result['content'],
                'status' => $result['status']
            ]);

            if ($result['status'] === 'missing') {
                $this->logger->warning('Description not available', [
                    'opportunity_id' => $opportunityId,
                    'url' => $descriptionUrl,
                    'error' => $result['error'] ?? 'Unknown error'
                ]);
            }

        } catch (\Exception $e) {
            $this->logger->error('Failed to fetch description', [
                'opportunity_id' => $opportunityId,
                'url' => $descriptionUrl,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Fetch and store attachments
     */
    private function fetchAndStoreAttachments(int $opportunityId, array $resourceLinks): void
    {
        if (empty($resourceLinks) || !($this->settings['fetch_attachments'] ?? false)) {
            return;
        }

        foreach ($resourceLinks as $link) {
            if (empty($link['url'])) {
                continue;
            }

            try {
                // Generate unique filename
                $filename = basename($link['url']);
                $extension = pathinfo($filename, PATHINFO_EXTENSION);
                $hash = md5($link['url'] . time());
                $uniqueFilename = $hash . '.' . $extension;
                $targetPath = $this->settings['files_root'] . '/attachments/' . $uniqueFilename;

                // Download file
                if ($this->samApi->downloadFile($link['url'], $targetPath)) {
                    // Create file record
                    $fileId = $this->createFileRecord($targetPath, $filename, $link['url']);

                    // Link to opportunity
                    $this->linkFileToOpportunity($opportunityId, $fileId, $link['description'] ?? '');
                }

            } catch (\Exception $e) {
                $this->logger->error('Failed to download attachment', [
                    'opportunity_id' => $opportunityId,
                    'url' => $link['url'],
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Create file record
     */
    private function createFileRecord(string $filePath, string $originalName, string $sourceUrl): int
    {
        $mimeType = mime_content_type($filePath);
        $size = filesize($filePath);
        $sha256 = Security::hashFile($filePath);

        $sql = "INSERT INTO files (path, original_name, mime_type, size, sha256, source_url, created_at) 
                VALUES (:path, :original_name, :mime_type, :size, :sha256, :source_url, NOW())";

        Database::execute($sql, [
            'path' => $filePath,
            'original_name' => $originalName,
            'mime_type' => $mimeType,
            'size' => $size,
            'sha256' => $sha256,
            'source_url' => $sourceUrl
        ]);

        return (int) Database::lastInsertId();
    }

    /**
     * Link file to opportunity
     */
    private function linkFileToOpportunity(int $opportunityId, int $fileId, string $label): void
    {
        $sql = "INSERT INTO documents (opportunity_id, file_id, label, created_at) 
                VALUES (:opportunity_id, :file_id, :label, NOW())";

        Database::execute($sql, [
            'opportunity_id' => $opportunityId,
            'file_id' => $fileId,
            'label' => $label
        ]);
    }

    /**
     * Log opportunity change
     */
    private function logOpportunityChange(int $opportunityId, array $newData): void
    {
        $sql = "INSERT INTO opportunity_changes (opportunity_id, changed_at, payload) 
                VALUES (:opportunity_id, NOW(), :payload)";

        Database::execute($sql, [
            'opportunity_id' => $opportunityId,
            'payload' => json_encode($newData)
        ]);
    }

    /**
     * Validate configuration
     */
    private function validateConfiguration(): void
    {
        if (empty($this->settings['allowed_naics_codes'])) {
            throw new RuntimeException('Allowed NAICS codes not configured');
        }

        if (empty($this->settings['posted_days']) || $this->settings['posted_days'] > 365) {
            throw new RuntimeException('Posted days must be between 1 and 365');
        }
    }

    /**
     * Build search parameters
     */
    private function buildSearchParams(): array
    {
        $postedDays = $this->settings['posted_days'] ?? 30;
        
        return [
            'postedFrom' => date('m/d/Y', strtotime("-{$postedDays} days")),
            'postedTo' => date('m/d/Y'),
            'ptype' => ['o', 'k'], // Solicitation and Combined only
            'ncode' => $this->settings['allowed_naics_codes'] ?? ['334111', '541512'],
            'typeOfSetAside' => $this->settings['allowed_set_aside_codes'] ?? [],
            'limit' => 100
        ];
    }

    /**
     * Get ingestion statistics
     */
    public function getStats(): array
    {
        return $this->stats;
    }

    /**
     * Reset statistics
     */
    public function resetStats(): void
    {
        $this->stats = [
            'fetched' => 0,
            'created' => 0,
            'updated' => 0,
            'rejected' => 0,
            'errors' => []
        ];
    }
}