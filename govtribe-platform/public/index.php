<?php

declare(strict_types=1);

/**
 * GovTribe Platform - Main Entry Point
 * Bootstrap the application and handle all HTTP requests
 */

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Set timezone
date_default_timezone_set('America/Los_Angeles');

// Simple autoloader
spl_autoload_register(function ($class) {
    $prefix = 'GovTribe\\';
    $base_dir = __DIR__ . '/../src/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            [$key, $value] = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Load configuration
$config = require __DIR__ . '/../config/app.php';

// Initialize application
use GovTribe\App;
use GovTribe\Utils\Router;

$app = App::initialize($config);
$router = new Router();

// Load routes
require __DIR__ . '/../config/routes.php';

// Dispatch request
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

$router->dispatch($method, $path);