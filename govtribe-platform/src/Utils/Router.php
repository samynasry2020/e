<?php

declare(strict_types=1);

namespace GovTribe\Utils;

use RuntimeException;

/**
 * Simple router for handling HTTP requests
 */
class Router
{
    private array $routes = [];
    private array $middleware = [];
    private string $basePath = '';

    public function __construct(string $basePath = '')
    {
        $this->basePath = rtrim($basePath, '/');
    }

    /**
     * Add GET route
     */
    public function get(string $path, callable $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    /**
     * Add POST route
     */
    public function post(string $path, callable $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    /**
     * Add PUT route
     */
    public function put(string $path, callable $handler, array $middleware = []): void
    {
        $this->addRoute('PUT', $path, $handler, $middleware);
    }

    /**
     * Add DELETE route
     */
    public function delete(string $path, callable $handler, array $middleware = []): void
    {
        $this->addRoute('DELETE', $path, $handler, $middleware);
    }

    /**
     * Add route with any HTTP method
     */
    public function any(string $path, callable $handler, array $middleware = []): void
    {
        $this->addRoute('*', $path, $handler, $middleware);
    }

    /**
     * Add global middleware
     */
    public function middleware(callable $middleware): void
    {
        $this->middleware[] = $middleware;
    }

    /**
     * Add route
     */
    private function addRoute(string $method, string $path, callable $handler, array $middleware = []): void
    {
        $fullPath = $this->basePath . '/' . ltrim($path, '/');
        
        $this->routes[] = [
            'method' => $method,
            'path' => $fullPath,
            'handler' => $handler,
            'middleware' => $middleware,
            'pattern' => $this->pathToRegex($fullPath),
        ];
    }

    /**
     * Convert path pattern to regex
     */
    private function pathToRegex(string $path): string
    {
        // Escape forward slashes
        $pattern = str_replace('/', '\/', $path);
        
        // Convert route parameters {param} to regex groups
        $pattern = preg_replace('/\{([^}]+)\}/', '(?P<$1>[^\/]+)', $pattern);
        
        return '/^' . $pattern . '$/';
    }

    /**
     * Dispatch request to appropriate handler
     */
    public function dispatch(string $method, string $path): void
    {
        $path = parse_url($path, PHP_URL_PATH) ?: '/';
        
        foreach ($this->routes as $route) {
            if ($route['method'] !== '*' && $route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $path, $matches)) {
                // Extract route parameters
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                // Execute global middleware
                foreach ($this->middleware as $middleware) {
                    $result = $middleware();
                    if ($result === false) {
                        return; // Middleware blocked the request
                    }
                }

                // Execute route-specific middleware
                foreach ($route['middleware'] as $middleware) {
                    $result = $middleware();
                    if ($result === false) {
                        return; // Middleware blocked the request
                    }
                }

                // Execute route handler
                call_user_func($route['handler'], $params);
                return;
            }
        }

        // No route found
        $this->handle404();
    }

    /**
     * Handle 404 Not Found
     */
    private function handle404(): void
    {
        http_response_code(404);
        
        $content = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Not Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <h1 class="display-1">404</h1>
                <h2>Page Not Found</h2>
                <p class="text-muted">The page you are looking for does not exist.</p>
                <a href="/" class="btn btn-primary">Go Home</a>
            </div>
        </div>
    </div>
</body>
</html>';
        
        echo $content;
    }

    /**
     * Handle 405 Method Not Allowed
     */
    public function handle405(): void
    {
        http_response_code(405);
        
        $content = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>405 - Method Not Allowed</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <h1 class="display-1">405</h1>
                <h2>Method Not Allowed</h2>
                <p class="text-muted">The HTTP method used is not allowed for this resource.</p>
                <a href="/" class="btn btn-primary">Go Home</a>
            </div>
        </div>
    </div>
</body>
</html>';
        
        echo $content;
    }

    /**
     * Redirect to URL
     */
    public function redirect(string $url, int $statusCode = 302): void
    {
        http_response_code($statusCode);
        header("Location: {$url}");
        exit;
    }

    /**
     * Get current request method
     */
    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * Get current request path
     */
    public function getPath(): string
    {
        return parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    }

    /**
     * Check if request is AJAX
     */
    public function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Get request data
     */
    public function getRequestData(): array
    {
        $data = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $data = $_GET;
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
        } else {
            // For PUT/DELETE, read from input stream
            $input = file_get_contents('php://input');
            if ($input) {
                parse_str($input, $data);
            }
        }
        
        return $data;
    }

    /**
     * Get JSON request data
     */
    public function getJsonData(): array
    {
        $input = file_get_contents('php://input');
        if (!$input) {
            return [];
        }
        
        $data = json_decode($input, true);
        return is_array($data) ? $data : [];
    }

    /**
     * Send JSON response
     */
    public function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Send error response
     */
    public function error(string $message, int $statusCode = 400): void
    {
        $this->json(['error' => $message], $statusCode);
    }
}