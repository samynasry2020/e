<?php

declare(strict_types=1);

namespace GovTribe\Services;

use GovTribe\Utils\Logger;
use RuntimeException;
use DateTime;
use DateTimeInterface;

/**
 * SAM.gov API v2 Integration Service
 * Implements strict validation and error handling per PRD requirements
 */
class SamApiService
{
    private Logger $logger;
    private array $config;
    private string $apiKey;
    private bool $useAlpha;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->logger = Logger::getInstance();
        $this->apiKey = '';
        $this->useAlpha = false;
    }

    /**
     * Set API key
     */
    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Set whether to use alpha API
     */
    public function setUseAlpha(bool $useAlpha): void
    {
        $this->useAlpha = $useAlpha;
    }

    /**
     * Search opportunities with strict validation
     */
    public function searchOpportunities(array $params): array
    {
        // Validate required parameters
        $this->validateSearchParams($params);

        // Build request URL
        $baseUrl = $this->useAlpha ? $this->config['alpha_url'] : $this->config['base_url'];
        $url = $baseUrl . '/search';

        // Build query parameters
        $queryParams = $this->buildQueryParams($params);

        // Make API request with retry logic
        $response = $this->makeRequest($url, $queryParams);

        // Validate and process response
        return $this->processSearchResponse($response);
    }

    /**
     * Get opportunity details by notice ID
     */
    public function getOpportunityDetails(string $noticeId): array
    {
        if (empty($noticeId)) {
            throw new RuntimeException('Notice ID is required');
        }

        $baseUrl = $this->useAlpha ? $this->config['alpha_url'] : $this->config['base_url'];
        $url = $baseUrl . '/search';

        $queryParams = [
            'api_key' => $this->apiKey,
            'noticeId' => $noticeId,
            'limit' => 1
        ];

        $response = $this->makeRequest($url, $queryParams);

        if (empty($response['opportunitiesData'])) {
            throw new RuntimeException('Opportunity not found');
        }

        return $response['opportunitiesData'][0];
    }

    /**
     * Fetch description content from URL
     */
    public function fetchDescription(string $descriptionUrl): array
    {
        if (empty($descriptionUrl)) {
            return ['content' => '', 'status' => 'missing'];
        }

        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $descriptionUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_USERAGENT => 'GovTribe Platform/1.0',
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_HTTPHEADER => [
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language: en-US,en;q=0.5',
                    'Accept-Encoding: gzip, deflate',
                    'Connection: keep-alive',
                ]
            ]);

            $content = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                $this->logger->warning('Failed to fetch description', [
                    'url' => $descriptionUrl,
                    'error' => $error
                ]);
                return ['content' => '', 'status' => 'missing', 'error' => $error];
            }

            if ($httpCode === 200 && !empty($content)) {
                // Basic HTML stripping for description content
                $content = strip_tags($content);
                $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
                $content = preg_replace('/\s+/', ' ', trim($content));
                
                return ['content' => $content, 'status' => 'ok'];
            } else {
                $this->logger->warning('Description not found or empty', [
                    'url' => $descriptionUrl,
                    'http_code' => $httpCode
                ]);
                return ['content' => '', 'status' => 'missing', 'http_code' => $httpCode];
            }

        } catch (\Exception $e) {
            $this->logger->error('Exception fetching description', [
                'url' => $descriptionUrl,
                'error' => $e->getMessage()
            ]);
            return ['content' => '', 'status' => 'missing', 'error' => $e->getMessage()];
        }
    }

    /**
     * Download file from URL
     */
    public function downloadFile(string $fileUrl, string $targetPath): bool
    {
        if (empty($fileUrl) || empty($targetPath)) {
            return false;
        }

        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $fileUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_USERAGENT => 'GovTribe Platform/1.0',
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_MAXREDIRS => 5
            ]);

            $data = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error || $httpCode !== 200 || empty($data)) {
                $this->logger->warning('Failed to download file', [
                    'url' => $fileUrl,
                    'target' => $targetPath,
                    'http_code' => $httpCode,
                    'error' => $error
                ]);
                return false;
            }

            // Ensure directory exists
            $dir = dirname($targetPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            // Write file
            $written = file_put_contents($targetPath, $data);
            if ($written === false || $written !== strlen($data)) {
                $this->logger->error('Failed to write downloaded file', [
                    'url' => $fileUrl,
                    'target' => $targetPath,
                    'expected_size' => strlen($data),
                    'written_size' => $written
                ]);
                return false;
            }

            $this->logger->info('File downloaded successfully', [
                'url' => $fileUrl,
                'target' => $targetPath,
                'size' => strlen($data)
            ]);

            return true;

        } catch (\Exception $e) {
            $this->logger->error('Exception downloading file', [
                'url' => $fileUrl,
                'target' => $targetPath,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Validate search parameters per PRD requirements
     */
    private function validateSearchParams(array $params): void
    {
        // Check API key
        if (empty($this->apiKey)) {
            throw new RuntimeException('SAM API key is required');
        }

        // Validate required date parameters
        if (empty($params['postedFrom']) || empty($params['postedTo'])) {
            throw new RuntimeException('postedFrom and postedTo are required');
        }

        // Validate date format (MM/dd/yyyy)
        $postedFrom = DateTime::createFromFormat('m/d/Y', $params['postedFrom']);
        $postedTo = DateTime::createFromFormat('m/d/Y', $params['postedTo']);

        if (!$postedFrom || !$postedTo) {
            throw new RuntimeException('postedFrom and postedTo must be in MM/dd/yyyy format');
        }

        // Validate date range (max 365 days)
        $diff = $postedTo->diff($postedFrom);
        if ($diff->days > 365) {
            throw new RuntimeException('Date range cannot exceed 365 days');
        }

        // Validate procurement types
        if (!empty($params['ptype'])) {
            $allowedTypes = ['o', 'k']; // Only Solicitation and Combined for MVP
            if (is_array($params['ptype'])) {
                foreach ($params['ptype'] as $type) {
                    if (!in_array($type, $allowedTypes, true)) {
                        throw new RuntimeException("Invalid procurement type: {$type}");
                    }
                }
            } else {
                if (!in_array($params['ptype'], $allowedTypes, true)) {
                    throw new RuntimeException("Invalid procurement type: {$params['ptype']}");
                }
            }
        }

        // Validate limit
        if (!empty($params['limit'])) {
            $limit = (int) $params['limit'];
            if ($limit < 1 || $limit > 1000) {
                throw new RuntimeException('Limit must be between 1 and 1000');
            }
        }
    }

    /**
     * Build query parameters for API request
     */
    private function buildQueryParams(array $params): array
    {
        $queryParams = [
            'api_key' => $this->apiKey,
            'postedFrom' => $params['postedFrom'],
            'postedTo' => $params['postedTo'],
            'limit' => min($params['limit'] ?? 100, 1000),
            'offset' => $params['offset'] ?? 0
        ];

        // Add procurement types (default to 'o' and 'k' if not specified)
        if (!empty($params['ptype'])) {
            if (is_array($params['ptype'])) {
                foreach ($params['ptype'] as $type) {
                    $queryParams['ptype'] = $type;
                }
            } else {
                $queryParams['ptype'] = $params['ptype'];
            }
        } else {
            // Default to Solicitation and Combined
            $queryParams['ptype'] = 'o';
            $queryParams['ptype'] = 'k';
        }

        // Add NAICS codes
        if (!empty($params['ncode'])) {
            if (is_array($params['ncode'])) {
                foreach ($params['ncode'] as $code) {
                    $queryParams['ncode'] = $code;
                }
            } else {
                $queryParams['ncode'] = $params['ncode'];
            }
        }

        // Add set-aside codes
        if (!empty($params['typeOfSetAside'])) {
            if (is_array($params['typeOfSetAside'])) {
                foreach ($params['typeOfSetAside'] as $setAside) {
                    $queryParams['typeOfSetAside'] = $setAside;
                }
            } else {
                $queryParams['typeOfSetAside'] = $params['typeOfSetAside'];
            }
        }

        return $queryParams;
    }

    /**
     * Make HTTP request with retry logic
     */
    private function makeRequest(string $url, array $params): array
    {
        $attempt = 0;
        $maxAttempts = $this->config['retry_attempts'] ?? 4;
        $baseDelay = $this->config['retry_delay'] ?? 1000;

        while ($attempt < $maxAttempts) {
            try {
                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => $url . '?' . http_build_query($params),
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_TIMEOUT => $this->config['timeout'] ?? 30,
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
                    throw new RuntimeException("cURL error: {$error}");
                }

                if ($httpCode === 200) {
                    $data = json_decode($response, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new RuntimeException('Invalid JSON response: ' . json_last_error_msg());
                    }
                    return $data;
                }

                if ($httpCode === 429 || $httpCode >= 500) {
                    // Rate limited or server error - retry with exponential backoff
                    $delay = $baseDelay * pow(2, $attempt);
                    $this->logger->warning('API request failed, retrying', [
                        'url' => $url,
                        'http_code' => $httpCode,
                        'attempt' => $attempt + 1,
                        'delay' => $delay
                    ]);
                    
                    usleep($delay * 1000); // Convert to microseconds
                    $attempt++;
                    continue;
                }

                if ($httpCode === 401) {
                    throw new RuntimeException('Invalid API key');
                }

                if ($httpCode === 403) {
                    throw new RuntimeException('API access forbidden - check API key permissions');
                }

                throw new RuntimeException("API request failed with HTTP {$httpCode}");

            } catch (\Exception $e) {
                if ($attempt === $maxAttempts - 1) {
                    $this->logger->error('API request failed after all retries', [
                        'url' => $url,
                        'error' => $e->getMessage(),
                        'attempts' => $maxAttempts
                    ]);
                    throw $e;
                }

                $delay = $baseDelay * pow(2, $attempt);
                usleep($delay * 1000);
                $attempt++;
            }
        }

        throw new RuntimeException('Maximum retry attempts exceeded');
    }

    /**
     * Process and validate search response
     */
    private function processSearchResponse(array $response): array
    {
        if (!isset($response['opportunitiesData']) || !is_array($response['opportunitiesData'])) {
            $this->logger->warning('Invalid API response structure', ['response' => $response]);
            return ['opportunities' => [], 'total' => 0, 'errors' => ['Invalid response structure']];
        }

        $opportunities = [];
        $errors = [];

        foreach ($response['opportunitiesData'] as $item) {
            try {
                $validated = $this->validateOpportunityData($item);
                if ($validated) {
                    $opportunities[] = $item;
                }
            } catch (\Exception $e) {
                $errors[] = "Validation failed for opportunity {$item['noticeId'] ?? 'unknown'}: " . $e->getMessage();
                $this->logger->warning('Opportunity validation failed', [
                    'notice_id' => $item['noticeId'] ?? 'unknown',
                    'error' => $e->getMessage()
                ]);
            }
        }

        return [
            'opportunities' => $opportunities,
            'total' => count($opportunities),
            'total_records' => $response['totalRecords'] ?? 0,
            'errors' => $errors
        ];
    }

    /**
     * Validate individual opportunity data per PRD requirements
     */
    private function validateOpportunityData(array $data): bool
    {
        // Required fields
        $required = ['noticeId', 'title', 'postedDate', 'type', 'baseType'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new RuntimeException("Missing required field: {$field}");
            }
        }

        // Validate procurement type
        $allowedTypes = ['Solicitation', 'Combined'];
        if (!in_array($data['type'], $allowedTypes, true)) {
            throw new RuntimeException("Invalid procurement type: {$data['type']}");
        }

        // Validate base type
        $allowedBaseTypes = ['o', 'k'];
        if (!in_array($data['baseType'], $allowedBaseTypes, true)) {
            throw new RuntimeException("Invalid base type: {$data['baseType']}");
        }

        // Validate date format
        $postedDate = DateTime::createFromFormat('m/d/Y', $data['postedDate']);
        if (!$postedDate) {
            throw new RuntimeException("Invalid posted date format: {$data['postedDate']}");
        }

        // Validate response deadline if present
        if (!empty($data['reponseDeadLine'])) {
            $deadline = DateTime::createFromFormat('m/d/Y', $data['reponseDeadLine']);
            if (!$deadline) {
                throw new RuntimeException("Invalid deadline format: {$data['reponseDeadLine']}");
            }
        }

        return true;
    }

    /**
     * Test API connection
     */
    public function testConnection(): array
    {
        try {
            // Make a simple request with minimal parameters
            $params = [
                'postedFrom' => date('m/d/Y', strtotime('-7 days')),
                'postedTo' => date('m/d/Y'),
                'limit' => 1
            ];

            $response = $this->searchOpportunities($params);
            
            return [
                'success' => true,
                'message' => 'API connection successful',
                'total_records' => $response['total_records'] ?? 0
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }
}