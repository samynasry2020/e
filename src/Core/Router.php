<?php

declare(strict_types=1);

namespace GovTribe\Core;

class Router
{
    private array $routes = [];
    private array $middlewares = [];

    public function get(string $path, callable|array|string $handler): self
    {
        return $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable|array|string $handler): self
    {
        return $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, callable|array|string $handler): self
    {
        return $this->addRoute('PUT', $path, $handler);
    }

    public function delete(string $path, callable|array|string $handler): self
    {
        return $this->addRoute('DELETE', $path, $handler);
    }

    public function patch(string $path, callable|array|string $handler): self
    {
        return $this->addRoute('PATCH', $path, $handler);
    }

    private function addRoute(string $method, string $path, callable|array|string $handler): self
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middlewares' => []
        ];

        return $this;
    }

    public function middleware(string|array $middleware): self
    {
        $lastIndex = count($this->routes) - 1;
        if ($lastIndex >= 0) {
            $middlewares = is_array($middleware) ? $middleware : [$middleware];
            $this->routes[$lastIndex]['middlewares'] = array_merge(
                $this->routes[$lastIndex]['middlewares'],
                $middlewares
            );
        }

        return $this;
    }

    public function group(array $attributes, callable $callback): void
    {
        $prefix = $attributes['prefix'] ?? '';
        $middleware = $attributes['middleware'] ?? [];

        $originalRoutes = $this->routes;
        $this->routes = [];

        $callback($this);

        foreach ($this->routes as &$route) {
            $route['path'] = $prefix . $route['path'];
            $route['middlewares'] = array_merge($middleware, $route['middlewares']);
        }

        $this->routes = array_merge($originalRoutes, $this->routes);
    }

    public function dispatch(string $method, string $uri): mixed
    {
        // Remove query string
        $uri = parse_url($uri, PHP_URL_PATH) ?? '/';
        
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->matchRoute($route['path'], $uri);
            if ($params !== false) {
                return $this->executeRoute($route, $params);
            }
        }

        throw new \Exception('Route not found', 404);
    }

    private function matchRoute(string $pattern, string $uri): array|false
    {
        // Convert route pattern to regex
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $pattern);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $uri, $matches)) {
            array_shift($matches); // Remove full match
            return $matches;
        }

        return false;
    }

    private function executeRoute(array $route, array $params): mixed
    {
        // Execute middlewares
        foreach ($route['middlewares'] as $middleware) {
            if (is_string($middleware) && class_exists($middleware)) {
                $middlewareInstance = new $middleware();
                if (method_exists($middlewareInstance, 'handle')) {
                    $middlewareInstance->handle();
                }
            } elseif (is_callable($middleware)) {
                $middleware();
            }
        }

        $handler = $route['handler'];

        if (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }

        if (is_array($handler)) {
            [$controller, $method] = $handler;
            if (is_string($controller)) {
                $controller = new $controller();
            }
            return call_user_func_array([$controller, $method], $params);
        }

        if (is_string($handler)) {
            if (strpos($handler, '@') !== false) {
                [$controllerClass, $method] = explode('@', $handler);
                $controller = new $controllerClass();
                return call_user_func_array([$controller, $method], $params);
            }
        }

        throw new \Exception('Invalid route handler');
    }
}