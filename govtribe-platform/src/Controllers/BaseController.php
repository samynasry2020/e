<?php

declare(strict_types=1);

namespace GovTribe\Controllers;

use GovTribe\Services\AuthService;
use GovTribe\Utils\Router;
use GovTribe\Utils\Security;

/**
 * Base controller class
 */
abstract class BaseController
{
    protected AuthService $auth;
    protected Router $router;

    public function __construct()
    {
        $this->auth = new AuthService();
        $this->router = new Router();
    }

    /**
     * Render view template
     */
    protected function view(string $template, array $data = []): void
    {
        $viewPath = __DIR__ . '/../Views/' . $template . '.php';
        
        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View not found: {$template}");
        }

        // Extract data to variables
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include the view
        include $viewPath;
        
        // Get the content
        $content = ob_get_clean();
        
        // Output the content
        echo $content;
    }

    /**
     * Render JSON response
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Redirect to URL
     */
    protected function redirect(string $url, int $statusCode = 302): void
    {
        http_response_code($statusCode);
        header("Location: {$url}");
        exit;
    }

    /**
     * Get current user
     */
    protected function getCurrentUser()
    {
        return $this->auth->getCurrentUser();
    }

    /**
     * Require authentication
     */
    protected function requireAuth()
    {
        return $this->auth->requireAuth();
    }

    /**
     * Require permission
     */
    protected function requirePermission(string $permission)
    {
        return $this->auth->requirePermission($permission);
    }

    /**
     * Require admin
     */
    protected function requireAdmin()
    {
        return $this->auth->requireAdmin();
    }

    /**
     * Get CSRF token
     */
    protected function getCsrfToken(): string
    {
        return Security::generateCsrfToken();
    }

    /**
     * Validate required fields
     */
    protected function validateRequired(array $data, array $required): array
    {
        $errors = [];
        
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }
        
        return $errors;
    }

    /**
     * Validate email
     */
    protected function validateEmail(string $email): bool
    {
        return Security::isValidEmail($email);
    }

    /**
     * Sanitize input
     */
    protected function sanitize(string $input): string
    {
        return Security::sanitizeInput($input);
    }

    /**
     * Get request data
     */
    protected function getRequestData(): array
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return $_GET;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $_POST;
        }
        
        // For PUT/DELETE, read from input stream
        $input = file_get_contents('php://input');
        if ($input) {
            parse_str($input, $data);
            return $data;
        }
        
        return [];
    }

    /**
     * Get JSON request data
     */
    protected function getJsonData(): array
    {
        $input = file_get_contents('php://input');
        if (!$input) {
            return [];
        }
        
        $data = json_decode($input, true);
        return is_array($data) ? $data : [];
    }

    /**
     * Handle validation errors
     */
    protected function handleValidationErrors(array $errors): void
    {
        if ($this->router->isAjax()) {
            $this->json(['errors' => $errors], 400);
        } else {
            // Store errors in session and redirect back
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['validation_errors'] = $errors;
            $_SESSION['old_input'] = $this->getRequestData();
            
            // Redirect back to previous page
            $referer = $_SERVER['HTTP_REFERER'] ?? '/';
            $this->redirect($referer);
        }
    }

    /**
     * Get validation errors from session
     */
    protected function getValidationErrors(): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $errors = $_SESSION['validation_errors'] ?? [];
        unset($_SESSION['validation_errors']);
        
        return $errors;
    }

    /**
     * Get old input from session
     */
    protected function getOldInput(): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $old = $_SESSION['old_input'] ?? [];
        unset($_SESSION['old_input']);
        
        return $old;
    }

    /**
     * Flash message to session
     */
    protected function flash(string $type, string $message): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['flash_messages'])) {
            $_SESSION['flash_messages'] = [];
        }
        
        $_SESSION['flash_messages'][] = [
            'type' => $type,
            'message' => $message
        ];
    }

    /**
     * Get flash messages
     */
    protected function getFlashMessages(): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $messages = $_SESSION['flash_messages'] ?? [];
        unset($_SESSION['flash_messages']);
        
        return $messages;
    }

    /**
     * Check if request is AJAX
     */
    protected function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Get pagination parameters
     */
    protected function getPaginationParams(int $defaultPerPage = 20): array
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = max(1, min(100, (int)($_GET['per_page'] ?? $defaultPerPage)));
        
        return [
            'page' => $page,
            'per_page' => $perPage,
            'offset' => ($page - 1) * $perPage
        ];
    }

    /**
     * Generate pagination data
     */
    protected function generatePagination(int $total, int $page, int $perPage, string $baseUrl): array
    {
        $totalPages = ceil($total / $perPage);
        
        return [
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => $totalPages,
            'has_next' => $page < $totalPages,
            'has_prev' => $page > 1,
            'next_page' => $page < $totalPages ? $page + 1 : null,
            'prev_page' => $page > 1 ? $page - 1 : null,
            'base_url' => $baseUrl
        ];
    }
}