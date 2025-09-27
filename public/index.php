<?php

declare(strict_types=1);

// Start output buffering
ob_start();

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Set timezone
date_default_timezone_set('America/Los_Angeles');

// Include autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Include application bootstrap
require_once dirname(__DIR__) . '/src/bootstrap.php';

use App\Utils\Config;
use App\Utils\Database;
use App\Utils\Logger;
use App\Utils\Router;

try {
    // Initialize configuration
    Config::load();
    
    // Initialize database
    Database::init();
    
    // Start session
    session_start();
    
    // Initialize router
    $router = new Router();
    
    // Define routes
    $router->get('/', 'HomeController@index');
    $router->get('/login', 'AuthController@showLogin');
    $router->post('/login', 'AuthController@login');
    $router->post('/logout', 'AuthController@logout');
    $router->get('/dashboard', 'DashboardController@index');
    
    // Opportunities routes
    $router->get('/opportunities', 'OpportunityController@index');
    $router->get('/opportunities/{id}', 'OpportunityController@show');
    $router->post('/opportunities/{id}/score', 'OpportunityController@updateScore');
    $router->post('/opportunities/{id}/status', 'OpportunityController@updateStatus');
    
    // Admin routes
    $router->get('/admin', 'AdminController@index');
    $router->get('/admin/users', 'AdminController@users');
    $router->get('/admin/settings', 'AdminController@settings');
    $router->post('/admin/settings', 'AdminController@updateSettings');
    $router->post('/admin/sync', 'AdminController@syncNow');
    
    // API routes
    $router->get('/api/opportunities', 'ApiController@opportunities');
    $router->post('/api/opportunities/sync', 'ApiController@sync');
    
    // Handle the request
    $router->dispatch();
    
} catch (Throwable $e) {
    Logger::critical('Unhandled exception: ' . $e->getMessage(), [
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
    
    // Show error page
    http_response_code(500);
    
    if (Config::get('app.debug', false)) {
        echo '<h1>Application Error</h1>';
        echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    } else {
        echo '<h1>Internal Server Error</h1>';
        echo '<p>An unexpected error occurred. Please try again later.</p>';
    }
}

// End output buffering and send response
ob_end_flush();