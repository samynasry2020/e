<?php

declare(strict_types=1);

namespace GovTribe\Services;

use GovTribe\Core\Config;
use GovTribe\Core\Database;

class SamApiClient
{
    private string $apiKey;
    private string $baseUrl;
    private array $allowedNaicsCodes;
    private array $allowedSetAsideTypes;
    private int $maxRetries = 4;
    private array $backoffDelays = [1, 2, 4, 8]; // seconds

    public function __construct()
    {
        $this->apiKey = $this->getApiKey();
        $this->baseUrl = $this->getApiEndpoint();
        $this->loadFilterSettings();
    }

    public function searchOpportunities(array $filters = []): array
    {
        $this->validateFilters($filters);
        $params = $this->buildRequestParams($filters);
        
        $allResults = [];
        $offset = 0;
        $limit = min($filters['limit'] ?? 100, 1000);
        
        do {
            $params['offset'] = $offset;
            $params['limit'] = $limit;
            
            $response = $this->makeRequest($params);
            
            if (!isset($response['opportunitiesData'])) {
                break;
            }
            
            $opportunities = $response['opportunitiesData'];
            $validOpportunities = $this->validateAndFilterOpportunities($opportunities);
            $allResults = array_merge($allResults, $validOpportunities);
            
            $totalRecords = $response['totalRecords'] ?? 0;
            $offset += $limit;
            
        } while ($offset < $totalRecords && count($opportunities) === $limit);
        
        return [
            'opportunities' => $allResults,
            'total' => count($allResults),
            'metadata' => [
                'total_api_records' => $response['totalRecords'] ?? 0,
                'valid_records' => count($allResults),
                'filters_applied' => $params
            ]
        ];
    }

    public function fetchDescription(string $descriptionUrl): array
    {
        if (empty($descriptionUrl)) {
            return ['status' => 'missing', 'content' => '', 'url' => ''];
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $descriptionUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_USERAGENT => 'GovTribe Platform/1.0',
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            error_log("Description fetch error for {$descriptionUrl}: {$error}");
            return ['status' => 'missing', 'content' => '', 'url' => $descriptionUrl];
        }

        if ($httpCode !== 200) {
            error_log("Description fetch HTTP {$httpCode} for {$descriptionUrl}");
            return ['status' => 'missing', 'content' => '', 'url' => $descriptionUrl];
        }

        $content = trim($response);
        if (empty($content) || stripos($content, 'Description Not Found') !== false) {
            return ['status' => 'missing', 'content' => '', 'url' => $descriptionUrl];
        }

        return ['status' => 'ok', 'content' => $content, 'url' => $descriptionUrl];
    }

    private function validateFilters(array $filters): void
    {
        // Validate date range is present and within limits
        if (empty($filters['postedFrom']) || empty($filters['postedTo'])) {
            throw new \InvalidArgumentException('postedFrom and postedTo are required parameters');
        }

        $postedFrom = \DateTime::createFromFormat('m/d/Y', $filters['postedFrom']);
        $postedTo = \DateTime::createFromFormat('m/d/Y', $filters['postedTo']);

        if (!$postedFrom || !$postedTo) {
            throw new \InvalidArgumentException('Date format must be MM/dd/yyyy');
        }

        $daysDiff = $postedTo->diff($postedFrom)->days;
        if ($daysDiff > 365) {
            throw new \InvalidArgumentException('Date range cannot exceed 365 days');
        }

        if ($postedFrom > $postedTo) {
            throw new \InvalidArgumentException('postedFrom cannot be after postedTo');
        }

        // Validate procurement types
        $allowedPtypes = ['o', 'k']; // Only Solicitation and Combined for MVP
        $ptypes = $filters['ptype'] ?? $allowedPtypes;
        if (!is_array($ptypes)) {
            $ptypes = [$ptypes];
        }

        foreach ($ptypes as $ptype) {
            if (!in_array($ptype, $allowedPtypes)) {
                throw new \InvalidArgumentException("Invalid procurement type: {$ptype}. Only 'o' and 'k' are allowed");
            }
        }
    }

    private function buildRequestParams(array $filters): array
    {
        $params = [
            'api_key' => $this->apiKey,
            'postedFrom' => $filters['postedFrom'],
            'postedTo' => $filters['postedTo'],
        ];

        // Add procurement types (required)
        $ptypes = $filters['ptype'] ?? ['o', 'k'];
        if (!is_array($ptypes)) {
            $ptypes = [$ptypes];
        }
        foreach ($ptypes as $ptype) {
            $params['ptype'][] = $ptype;
        }

        // Add NAICS codes (required for our filtering)
        $naicsCodes = $filters['ncode'] ?? $this->allowedNaicsCodes;
        if (!is_array($naicsCodes)) {
            $naicsCodes = [$naicsCodes];
        }
        foreach ($naicsCodes as $naics) {
            $params['ncode'][] = $naics;
        }

        // Add set-aside types if specified
        if (!empty($filters['typeOfSetAside'])) {
            $setAsides = is_array($filters['typeOfSetAside']) ? $filters['typeOfSetAside'] : [$filters['typeOfSetAside']];
            foreach ($setAsides as $setAside) {
                if (in_array($setAside, $this->allowedSetAsideTypes)) {
                    $params['typeOfSetAside'][] = $setAside;
                }
            }
        }

        return $params;
    }

    private function makeRequest(array $params): array
    {
        $url = $this->baseUrl . '?' . $this->buildQueryString($params);
        
        for ($attempt = 0; $attempt <= $this->maxRetries; $attempt++) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_USERAGENT => 'GovTribe Platform/1.0',
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_HTTPHEADER => [
                    'Accept: application/json',
                    'Content-Type: application/json'
                ]
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                error_log("SAM API request error (attempt {$attempt}): {$error}");
                if ($attempt < $this->maxRetries) {
                    sleep($this->backoffDelays[$attempt]);
                    continue;
                }
                throw new \RuntimeException("SAM API request failed: {$error}");
            }

            if ($httpCode === 200) {
                $data = json_decode($response, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $data;
                }
                throw new \RuntimeException("Invalid JSON response from SAM API");
            }

            if ($httpCode === 401 || $httpCode === 403) {
                throw new \RuntimeException("SAM API authentication error: Invalid or missing API key");
            }

            if ($httpCode === 429 || $httpCode >= 500) {
                error_log("SAM API HTTP {$httpCode} (attempt {$attempt})");
                if ($attempt < $this->maxRetries) {
                    sleep($this->backoffDelays[$attempt]);
                    continue;
                }
            }

            throw new \RuntimeException("SAM API request failed with HTTP {$httpCode}");
        }

        throw new \RuntimeException("SAM API request failed after {$this->maxRetries} attempts");
    }

    private function buildQueryString(array $params): string
    {
        $queryParts = [];
        
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    $queryParts[] = urlencode($key) . '=' . urlencode($v);
                }
            } else {
                $queryParts[] = urlencode($key) . '=' . urlencode($value);
            }
        }
        
        return implode('&', $queryParts);
    }

    private function validateAndFilterOpportunities(array $opportunities): array
    {
        $validOpportunities = [];
        
        foreach ($opportunities as $opp) {
            $validationResult = $this->validateOpportunity($opp);
            if ($validationResult['valid']) {
                $validOpportunities[] = $this->transformOpportunity($opp);
            } else {
                // Log rejection reason
                error_log("Opportunity rejected: " . json_encode([
                    'noticeId' => $opp['noticeId'] ?? 'unknown',
                    'title' => $opp['title'] ?? 'unknown',
                    'reason' => $validationResult['reason']
                ]));
            }
        }
        
        return $validOpportunities;
    }

    private function validateOpportunity(array $opp): array
    {
        // Check required fields
        if (empty($opp['title'])) {
            return ['valid' => false, 'reason' => 'Missing title'];
        }

        if (empty($opp['noticeId'])) {
            return ['valid' => false, 'reason' => 'Missing noticeId'];
        }

        // Validate procurement type
        $type = $opp['type'] ?? '';
        $baseType = $opp['baseType'] ?? '';
        if (!in_array($type, ['Solicitation', 'Combined']) && !in_array($baseType, ['Solicitation', 'Combined'])) {
            return ['valid' => false, 'reason' => 'Invalid procurement type'];
        }

        // Validate NAICS codes
        $naicsCode = $opp['naicsCode'] ?? '';
        if (!empty($naicsCode) && !in_array($naicsCode, $this->allowedNaicsCodes)) {
            return ['valid' => false, 'reason' => 'NAICS code not in allowlist'];
        }

        // Check if opportunity is active (not archived)
        $active = $opp['active'] ?? true;
        if (!$active) {
            return ['valid' => false, 'reason' => 'Opportunity is not active'];
        }

        return ['valid' => true, 'reason' => ''];
    }

    private function transformOpportunity(array $opp): array
    {
        return [
            'external_id' => $opp['noticeId'],
            'title' => $opp['title'],
            'posted_at' => $this->parseDate($opp['postedDate'] ?? ''),
            'due_at' => $this->parseDate($opp['responseDeadLine'] ?? ''),
            'notice_type' => $this->mapNoticeType($opp),
            'naics_code' => $opp['naicsCode'] ?? null,
            'set_aside' => $opp['setAside'] ?? null,
            'set_aside_code' => $opp['setAsideCode'] ?? null,
            'agency_name' => $this->extractAgencyName($opp),
            'ui_link' => $opp['uiLink'] ?? null,
            'description_url' => $opp['description'] ?? null,
            'resource_links' => $opp['resourceLinks'] ?? [],
            'point_of_contact' => $opp['pointOfContact'] ?? [],
            'active' => $opp['active'] ?? true,
            'raw_data' => $opp
        ];
    }

    private function parseDate(string $dateString): ?string
    {
        if (empty($dateString)) {
            return null;
        }

        // Try multiple date formats
        $formats = ['m/d/Y', 'Y-m-d', 'm-d-Y', 'M d, Y'];
        
        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $dateString);
            if ($date) {
                return $date->format('Y-m-d H:i:s');
            }
        }

        return null;
    }

    private function mapNoticeType(array $opp): string
    {
        $type = $opp['type'] ?? '';
        $baseType = $opp['baseType'] ?? '';
        
        if (in_array($type, ['Solicitation', 'Combined'])) {
            return $type;
        }
        
        if (in_array($baseType, ['Solicitation', 'Combined'])) {
            return $baseType;
        }
        
        return 'Solicitation'; // Default fallback
    }

    private function extractAgencyName(array $opp): string
    {
        return $opp['fullParentPathName'] ?? 
               $opp['organizationName'] ?? 
               $opp['department'] ?? 
               'Unknown Agency';
    }

    private function getApiKey(): string
    {
        // Try to get from settings table first
        $setting = Database::fetchOne('SELECT value FROM settings WHERE `key` = ?', ['sam_api_key']);
        if ($setting && !empty($setting['value'])) {
            return $setting['value'];
        }

        // Fallback to config
        $apiKey = Config::get('sam.api_key');
        if (empty($apiKey)) {
            throw new \RuntimeException('SAM API key not configured');
        }

        return $apiKey;
    }

    private function getApiEndpoint(): string
    {
        $useAlpha = Database::fetchOne('SELECT value FROM settings WHERE `key` = ?', ['sam_use_alpha']);
        $useAlpha = $useAlpha ? filter_var($useAlpha['value'], FILTER_VALIDATE_BOOLEAN) : false;

        return $useAlpha ? Config::get('sam.alpha_endpoint') : Config::get('sam.endpoint');
    }

    private function loadFilterSettings(): void
    {
        $naicsSettings = Database::fetchOne('SELECT value FROM settings WHERE `key` = ?', ['default_naics_codes']);
        $this->allowedNaicsCodes = $naicsSettings ? 
            json_decode($naicsSettings['value'], true) : 
            ['334111', '541512'];

        $setAsideSettings = Database::fetchOne('SELECT value FROM settings WHERE `key` = ?', ['default_set_aside_types']);
        $this->allowedSetAsideTypes = $setAsideSettings ? 
            json_decode($setAsideSettings['value'], true) : 
            ['WOSB', 'EDWOSB'];
    }

    public function testConnection(): array
    {
        try {
            $testFilters = [
                'postedFrom' => date('m/d/Y', strtotime('-7 days')),
                'postedTo' => date('m/d/Y'),
                'limit' => 1
            ];

            $params = $this->buildRequestParams($testFilters);
            $params['limit'] = 1;
            $params['offset'] = 0;

            $response = $this->makeRequest($params);
            
            return [
                'success' => true,
                'message' => 'API connection successful',
                'total_records' => $response['totalRecords'] ?? 0
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}