<?php

declare(strict_types=1);

namespace App\Services;

use App\Utils\Config;
use App\Utils\Logger;

class SamApiService
{
    private string $apiKey;
    private string $baseUrl;
    private int $timeout = 30;
    private int $maxRetries = 4;
    private array $retryDelays = [1, 2, 4, 8]; // seconds

    public function __construct()
    {
        $this->apiKey = Config::get('sam.api_key', '');
        $this->baseUrl = Config::get('sam.environment', 'prod') === 'alpha' 
            ? Config::get('sam.alpha_url', 'https://api-alpha.sam.gov/opportunities/v2/search')
            : Config::get('sam.base_url', 'https://api.sam.gov/opportunities/v2/search');

        if (empty($this->apiKey)) {
            throw new \RuntimeException('SAM API key is not configured');
        }
    }

    /**
     * Search for opportunities with strict validation
     */
    public function searchOpportunities(array $params): array
    {
        // Validate required parameters
        $this->validateSearchParams($params);

        $queryParams = $this->buildQueryParams($params);
        $url = $this->baseUrl . '?' . http_build_query($queryParams);

        Logger::info('SAM API search request', [
            'url' => $this->baseUrl,
            'params' => $queryParams
        ]);

        return $this->makeRequest($url, 'GET');
    }

    /**
     * Validate search parameters according to PRD requirements
     */
    private function validateSearchParams(array $params): void
    {
        // Check required date parameters
        if (empty($params['postedFrom']) || empty($params['postedTo'])) {
            throw new \InvalidArgumentException('postedFrom and postedTo are required');
        }

        // Validate date format (MM/dd/yyyy)
        $postedFrom = \DateTime::createFromFormat('m/d/Y', $params['postedFrom']);
        $postedTo = \DateTime::createFromFormat('m/d/Y', $params['postedTo']);

        if (!$postedFrom || !$postedTo) {
            throw new \InvalidArgumentException('Invalid date format. Use MM/dd/yyyy');
        }

        // Check date range (max 1 year)
        $dateDiff = $postedTo->diff($postedFrom);
        if ($dateDiff->days > 365) {
            throw new \InvalidArgumentException('Date range cannot exceed 365 days');
        }

        // Validate procurement types (only 'o' and 'k' allowed)
        if (!empty($params['ptype'])) {
            $allowedTypes = ['o', 'k'];
            $types = is_array($params['ptype']) ? $params['ptype'] : [$params['ptype']];
            
            foreach ($types as $type) {
                if (!in_array($type, $allowedTypes)) {
                    throw new \InvalidArgumentException("Invalid procurement type: {$type}. Allowed: " . implode(', ', $allowedTypes));
                }
            }
        }

        // Validate NAICS codes
        if (!empty($params['ncode'])) {
            $allowedNaics = ['334111', '541512']; // Default from PRD, should be configurable
            $codes = is_array($params['ncode']) ? $params['ncode'] : [$params['ncode']];
            
            foreach ($codes as $code) {
                if (!in_array($code, $allowedNaics)) {
                    throw new \InvalidArgumentException("NAICS code not allowed: {$code}");
                }
            }
        }

        // Validate limit
        if (!empty($params['limit']) && ($params['limit'] < 1 || $params['limit'] > 1000)) {
            throw new \InvalidArgumentException('Limit must be between 1 and 1000');
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
            'limit' => $params['limit'] ?? 100,
        ];

        // Add procurement types (default to both allowed types)
        $queryParams['ptype'] = $params['ptype'] ?? ['o', 'k'];

        // Add NAICS codes if provided
        if (!empty($params['ncode'])) {
            $queryParams['ncode'] = $params['ncode'];
        }

        // Add set-aside codes if provided
        if (!empty($params['typeOfSetAside'])) {
            $queryParams['typeOfSetAside'] = $params['typeOfSetAside'];
        }

        // Add offset for pagination
        if (!empty($params['offset'])) {
            $queryParams['offset'] = $params['offset'];
        }

        return $queryParams;
    }

    /**
     * Make HTTP request with retry logic
     */
    private function makeRequest(string $url, string $method = 'GET', array $data = null): array
    {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT => 'GovTribe-Platform/1.0',
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
            ],
        ]);

        if ($method === 'POST' && $data) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        // Retry logic with exponential backoff
        for ($attempt = 0; $attempt < $this->maxRetries; $attempt++) {
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);

            if ($error) {
                Logger::error('CURL error in SAM API request', [
                    'attempt' => $attempt + 1,
                    'error' => $error,
                    'url' => $url
                ]);
                
                if ($attempt < $this->maxRetries - 1) {
                    sleep($this->retryDelays[$attempt] ?? 1);
                    continue;
                }
                
                curl_close($ch);
                throw new \RuntimeException("CURL error: {$error}");
            }

            // Handle HTTP errors
            if ($httpCode >= 400) {
                Logger::warning('HTTP error in SAM API request', [
                    'attempt' => $attempt + 1,
                    'http_code' => $httpCode,
                    'response' => $response,
                    'url' => $url
                ]);

                // Handle specific error cases
                if ($httpCode === 401) {
                    curl_close($ch);
                    throw new \RuntimeException('SAM API authentication failed. Check API key.');
                }

                if ($httpCode === 429) {
                    // Rate limited - wait and retry
                    if ($attempt < $this->maxRetries - 1) {
                        sleep($this->retryDelays[$attempt] ?? 1);
                        continue;
                    }
                }

                if ($httpCode >= 500) {
                    // Server error - retry with backoff
                    if ($attempt < $this->maxRetries - 1) {
                        sleep($this->retryDelays[$attempt] ?? 1);
                        continue;
                    }
                }

                // For other errors, don't retry
                curl_close($ch);
                throw new \RuntimeException("HTTP error {$httpCode}: " . substr($response, 0, 500));
            }

            // Success
            break;
        }

        curl_close($ch);

        if ($response === false) {
            throw new \RuntimeException('Failed to get response from SAM API');
        }

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Logger::error('Invalid JSON response from SAM API', [
                'response' => substr($response, 0, 1000),
                'json_error' => json_last_error_msg()
            ]);
            throw new \RuntimeException('Invalid JSON response from SAM API');
        }

        return $data;
    }

    /**
     * Fetch opportunity description from URL
     */
    public function fetchDescription(string $descriptionUrl): array
    {
        if (empty($descriptionUrl)) {
            return ['status' => 'missing', 'content' => null];
        }

        Logger::info('Fetching opportunity description', ['url' => $descriptionUrl]);

        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $descriptionUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT => 'GovTribe-Platform/1.0',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);

        if ($error) {
            Logger::warning('Error fetching description', [
                'url' => $descriptionUrl,
                'error' => $error
            ]);
            return ['status' => 'missing', 'content' => null];
        }

        if ($httpCode === 404) {
            Logger::info('Description not found', ['url' => $descriptionUrl]);
            return ['status' => 'missing', 'content' => null];
        }

        if ($httpCode !== 200) {
            Logger::warning('Unexpected HTTP code when fetching description', [
                'url' => $descriptionUrl,
                'http_code' => $httpCode
            ]);
            return ['status' => 'missing', 'content' => null];
        }

        // Clean up the content
        $content = trim($response);
        if (empty($content)) {
            return ['status' => 'missing', 'content' => null];
        }

        // Check if it's actually HTML or plain text
        if (strpos($content, '<!DOCTYPE') === 0 || strpos($content, '<html') === 0) {
            // It's HTML, extract text content
            $content = strip_tags($content);
            $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
            $content = preg_replace('/\s+/', ' ', $content);
            $content = trim($content);
        }

        return ['status' => 'ok', 'content' => $content];
    }

    /**
     * Download attachment file
     */
    public function downloadAttachment(string $url, string $destinationPath): array
    {
        Logger::info('Downloading attachment', [
            'url' => $url,
            'destination' => $destinationPath
        ]);

        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT => 'GovTribe-Platform/1.0',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);

        if ($error) {
            Logger::warning('Error downloading attachment', [
                'url' => $url,
                'error' => $error
            ]);
            return ['success' => false, 'error' => $error];
        }

        if ($httpCode !== 200) {
            Logger::warning('Unexpected HTTP code when downloading attachment', [
                'url' => $url,
                'http_code' => $httpCode
            ]);
            return ['success' => false, 'error' => "HTTP {$httpCode}"];
        }

        // Create directory if it doesn't exist
        $dir = dirname($destinationPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Save file
        if (file_put_contents($destinationPath, $response) === false) {
            Logger::error('Failed to save attachment file', [
                'url' => $url,
                'destination' => $destinationPath
            ]);
            return ['success' => false, 'error' => 'Failed to save file'];
        }

        return [
            'success' => true,
            'size' => strlen($response),
            'path' => $destinationPath
        ];
    }

    /**
     * Test API connection
     */
    public function testConnection(): array
    {
        try {
            $params = [
                'postedFrom' => date('m/d/Y', strtotime('-1 day')),
                'postedTo' => date('m/d/Y'),
                'limit' => 1,
            ];

            $result = $this->searchOpportunities($params);
            
            return [
                'success' => true,
                'message' => 'API connection successful',
                'total_records' => $result['totalRecords'] ?? 0
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}