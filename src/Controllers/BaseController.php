<?php

declare(strict_types=1);

namespace GovTribe\Controllers;

use GovTribe\Core\Auth;
use GovTribe\Core\Config;

abstract class BaseController
{
    protected function render(string $view, array $data = []): string
    {
        $data['user'] = Auth::user();
        $data['csrf_token'] = Auth::generateCsrfToken();
        $data['app_url'] = Config::get('app.url');
        
        $viewFile = __DIR__ . '/../Views/' . $view . '.php';
        
        if (!file_exists($viewFile)) {
            throw new \Exception("View not found: {$view}");
        }
        
        // Extract data to variables
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include the view
        include $viewFile;
        
        // Return the buffered content
        return ob_get_clean();
    }

    protected function json(array $data, int $status = 200): string
    {
        http_response_code($status);
        header('Content-Type: application/json');
        return json_encode($data);
    }

    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    protected function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        $this->redirect($referer);
    }

    protected function validate(array $rules, array $data): array
    {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            $rulesList = is_string($rule) ? explode('|', $rule) : $rule;
            
            foreach ($rulesList as $r) {
                if ($r === 'required' && empty($value)) {
                    $errors[$field][] = ucfirst($field) . ' is required';
                } elseif (str_starts_with($r, 'min:')) {
                    $min = (int)substr($r, 4);
                    if (strlen($value) < $min) {
                        $errors[$field][] = ucfirst($field) . " must be at least {$min} characters";
                    }
                } elseif (str_starts_with($r, 'max:')) {
                    $max = (int)substr($r, 4);
                    if (strlen($value) > $max) {
                        $errors[$field][] = ucfirst($field) . " must not exceed {$max} characters";
                    }
                } elseif ($r === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = ucfirst($field) . ' must be a valid email address';
                } elseif ($r === 'numeric' && !is_numeric($value)) {
                    $errors[$field][] = ucfirst($field) . ' must be a number';
                }
            }
        }
        
        return $errors;
    }

    protected function flashMessage(string $type, string $message): void
    {
        if (!isset($_SESSION['flash'])) {
            $_SESSION['flash'] = [];
        }
        $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
    }

    protected function getFlashMessages(): array
    {
        $messages = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $messages;
    }

    protected function formatCurrency(float $amount): string
    {
        return '$' . number_format($amount, 2);
    }

    protected function formatDate(string $date, string $format = 'M j, Y'): string
    {
        return date($format, strtotime($date));
    }

    protected function formatDateTime(string $datetime, string $format = 'M j, Y g:i A'): string
    {
        return date($format, strtotime($datetime));
    }

    protected function timeAgo(string $datetime): string
    {
        $time = time() - strtotime($datetime);
        
        if ($time < 60) return 'just now';
        if ($time < 3600) return floor($time/60) . ' minutes ago';
        if ($time < 86400) return floor($time/3600) . ' hours ago';
        if ($time < 2592000) return floor($time/86400) . ' days ago';
        if ($time < 31536000) return floor($time/2592000) . ' months ago';
        
        return floor($time/31536000) . ' years ago';
    }

    protected function truncate(string $text, int $length = 100): string
    {
        return strlen($text) > $length ? substr($text, 0, $length) . '...' : $text;
    }

    protected function sanitizeInput(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    protected function requirePermission(string $permission): void
    {
        if (!Auth::can($permission)) {
            http_response_code(403);
            throw new \Exception('Access denied', 403);
        }
    }

    protected function requireRole(string $role): void
    {
        if (!Auth::hasRole($role)) {
            http_response_code(403);
            throw new \Exception('Access denied', 403);
        }
    }

    protected function logAction(string $action, string $entity, ?int $entityId = null, array $meta = []): void
    {
        $user = Auth::user();
        if (!$user) return;

        \GovTribe\Core\Database::insert('audit_log', [
            'user_id' => $user->getId(),
            'action' => $action,
            'entity' => $entity,
            'entity_id' => $entityId,
            'meta' => json_encode($meta),
            'ip_address' => $this->getClientIp()
        ]);
    }

    private function getClientIp(): string
    {
        $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = trim(explode(',', $_SERVER[$key])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
}