<?php

declare(strict_types=1);

namespace App\Utils;

class Router
{
    private array $routes = [];
    private array $middleware = [];

    /**
     * Add GET route
     */
    public function get(string $path, string $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    /**
     * Add POST route
     */
    public function post(string $path, string $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    /**
     * Add PUT route
     */
    public function put(string $path, string $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }

    /**
     * Add DELETE route
     */
    public function delete(string $path, string $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    /**
     * Add route with middleware
     */
    public function addRoute(string $method, string $path, string $handler, array $middleware = []): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    /**
     * Dispatch request to appropriate handler
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $path = rtrim($path, '/') ?: '/';

        // Find matching route
        $route = $this->findRoute($method, $path);
        
        if (!$route) {
            $this->handleNotFound();
            return;
        }

        // Extract parameters from path
        $params = $this->extractParams($route['path'], $path);

        // Run middleware
        foreach ($route['middleware'] as $middlewareClass) {
            $middleware = new $middlewareClass();
            if (!$middleware->handle()) {
                return;
            }
        }

        // Execute handler
        $this->executeHandler($route['handler'], $params);
    }

    /**
     * Find matching route
     */
    private function findRoute(string $method, string $path): ?array
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if ($this->pathMatches($route['path'], $path)) {
                return $route;
            }
        }

        return null;
    }

    /**
     * Check if path matches route pattern
     */
    private function pathMatches(string $pattern, string $path): bool
    {
        // Convert route pattern to regex
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $pattern);
        $pattern = '#^' . $pattern . '$#';

        return preg_match($pattern, $path) === 1;
    }

    /**
     * Extract parameters from path
     */
    private function extractParams(string $pattern, string $path): array
    {
        $params = [];

        // Find parameter names in pattern
        preg_match_all('/\{([^}]+)\}/', $pattern, $paramNames);
        
        // Convert pattern to regex and extract values
        $regex = preg_replace('/\{([^}]+)\}/', '([^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';
        
        if (preg_match($regex, $path, $matches)) {
            array_shift($matches); // Remove full match
            $params = array_combine($paramNames[1], $matches);
        }

        return $params;
    }

    /**
     * Execute route handler
     */
    private function executeHandler(string $handler, array $params): void
    {
        [$controllerName, $methodName] = explode('@', $handler);
        $controllerClass = "App\\Controllers\\{$controllerName}";

        if (!class_exists($controllerClass)) {
            Logger::error("Controller not found: {$controllerClass}");
            $this->handleNotFound();
            return;
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $methodName)) {
            Logger::error("Method not found: {$controllerClass}::{$methodName}");
            $this->handleNotFound();
            return;
        }

        try {
            // Set route parameters
            $_REQUEST['_route_params'] = $params;
            
            // Execute controller method
            $controller->$methodName($params);
        } catch (Throwable $e) {
            Logger::error("Controller error: " . $e->getMessage(), [
                'controller' => $controllerClass,
                'method' => $methodName,
                'trace' => $e->getTraceAsString()
            ]);
            $this->handleServerError();
        }
    }

    /**
     * Handle 404 Not Found
     */
    private function handleNotFound(): void
    {
        http_response_code(404);
        
        if (file_exists(APP_VIEWS . '/errors/404.php')) {
            include APP_VIEWS . '/errors/404.php';
        } else {
            echo '<h1>404 - Page Not Found</h1>';
            echo '<p>The requested page could not be found.</p>';
        }
    }

    /**
     * Handle 500 Server Error
     */
    private function handleServerError(): void
    {
        http_response_code(500);
        
        if (file_exists(APP_VIEWS . '/errors/500.php')) {
            include APP_VIEWS . '/errors/500.php';
        } else {
            echo '<h1>500 - Internal Server Error</h1>';
            echo '<p>An unexpected error occurred.</p>';
        }
    }

    /**
     * Get current route parameters
     */
    public static function getRouteParams(): array
    {
        return $_REQUEST['_route_params'] ?? [];
    }

    /**
     * Generate URL for route
     */
    public function url(string $path, array $params = []): string
    {
        foreach ($params as $key => $value) {
            $path = str_replace("{{$key}}", $value, $path);
        }
        
        return $path;
    }

    /**
     * Redirect to URL
     */
    public static function redirect(string $url, int $code = 302): void
    {
        header("Location: {$url}", true, $code);
        exit;
    }

    /**
     * Get current request method
     */
    public static function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * Check if request is AJAX
     */
    public static function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Get request body as JSON
     */
    public static function getJsonBody(): ?array
    {
        $input = file_get_contents('php://input');
        if (empty($input)) {
            return null;
        }

        $data = json_decode($input, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        return $data;
    }
}