<?php

declare(strict_types=1);

// Bootstrap the application
require_once __DIR__ . '/../vendor/autoload.php';

use GovTribe\Core\Config;
use GovTribe\Core\Router;
use GovTribe\Core\Auth;
use GovTribe\Controllers\{
    HomeController,
    AuthController,
    OpportunityController,
    AdminController,
    ProposalController,
    SupplierController,
    ReportController
};
use GovTribe\Middleware\{
    AuthMiddleware,
    CsrfMiddleware,
    RoleMiddleware
};

// Initialize configuration
Config::load();

// Set timezone
date_default_timezone_set(Config::get('app.timezone', 'UTC'));

// Start session
session_start();

// Error handling
if (!Config::get('app.debug')) {
    error_reporting(0);
    ini_set('display_errors', '0');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

// Create router
$router = new Router();

// Public routes
$router->get('/', [HomeController::class, 'dashboard']);
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);
$router->get('/health', [HomeController::class, 'health']);

// Protected routes
$router->group(['middleware' => [AuthMiddleware::class]], function($router) {
    
    // Dashboard
    $router->get('/dashboard', [HomeController::class, 'dashboard']);
    
    // Opportunities
    $router->get('/opportunities', [OpportunityController::class, 'index']);
    $router->get('/opportunities/{id}', [OpportunityController::class, 'show']);
    $router->post('/opportunities/{id}/score', [OpportunityController::class, 'updateScore'])
           ->middleware([CsrfMiddleware::class]);
    $router->post('/opportunities/{id}/status', [OpportunityController::class, 'updateStatus'])
           ->middleware([CsrfMiddleware::class]);
    $router->get('/opportunities/{id}/documents', [OpportunityController::class, 'documents']);
    $router->get('/opportunities/{id}/documents/{docId}/download', [OpportunityController::class, 'downloadDocument']);
    
    // Proposals
    $router->get('/proposals', [ProposalController::class, 'index']);
    $router->get('/proposals/{id}', [ProposalController::class, 'show']);
    $router->post('/proposals', [ProposalController::class, 'create'])
           ->middleware([CsrfMiddleware::class]);
    $router->put('/proposals/{id}', [ProposalController::class, 'update'])
           ->middleware([CsrfMiddleware::class]);
    $router->post('/proposals/{id}/package', [ProposalController::class, 'createPackage'])
           ->middleware([CsrfMiddleware::class]);
    
    // Suppliers & RFQs
    $router->get('/suppliers', [SupplierController::class, 'index']);
    $router->post('/suppliers', [SupplierController::class, 'create'])
           ->middleware([CsrfMiddleware::class]);
    $router->get('/suppliers/{id}', [SupplierController::class, 'show']);
    $router->put('/suppliers/{id}', [SupplierController::class, 'update'])
           ->middleware([CsrfMiddleware::class]);
    
    $router->get('/rfqs', [SupplierController::class, 'rfqIndex']);
    $router->post('/rfqs', [SupplierController::class, 'createRfq'])
           ->middleware([CsrfMiddleware::class]);
    $router->get('/rfqs/{id}', [SupplierController::class, 'showRfq']);
    
    // BOMs
    $router->get('/opportunities/{id}/bom', [OpportunityController::class, 'showBom']);
    $router->post('/opportunities/{id}/bom', [OpportunityController::class, 'createBom'])
           ->middleware([CsrfMiddleware::class]);
    $router->put('/boms/{id}', [OpportunityController::class, 'updateBom'])
           ->middleware([CsrfMiddleware::class]);
    
    // Submissions
    $router->get('/opportunities/{id}/submit', [OpportunityController::class, 'showSubmit']);
    $router->post('/opportunities/{id}/submit', [OpportunityController::class, 'recordSubmission'])
           ->middleware([CsrfMiddleware::class]);
    
    // Awards & Invoices
    $router->get('/awards', [ReportController::class, 'awards']);
    $router->post('/awards', [ReportController::class, 'createAward'])
           ->middleware([CsrfMiddleware::class]);
    $router->get('/awards/{id}', [ReportController::class, 'showAward']);
    $router->post('/awards/{id}/invoices', [ReportController::class, 'createInvoice'])
           ->middleware([CsrfMiddleware::class]);
    
    // Reports
    $router->get('/reports', [ReportController::class, 'index']);
    $router->get('/reports/pipeline', [ReportController::class, 'pipeline']);
    $router->get('/reports/performance', [ReportController::class, 'performance']);
    
    // Admin routes (Admin role only)
    $router->group(['prefix' => '/admin', 'middleware' => [RoleMiddleware::class . ':Admin']], function($router) {
        $router->get('/', [AdminController::class, 'index']);
        $router->get('/settings', [AdminController::class, 'settings']);
        $router->post('/settings', [AdminController::class, 'updateSettings'])
               ->middleware([CsrfMiddleware::class]);
        
        $router->get('/users', [AdminController::class, 'users']);
        $router->post('/users', [AdminController::class, 'createUser'])
               ->middleware([CsrfMiddleware::class]);
        $router->get('/users/{id}', [AdminController::class, 'showUser']);
        $router->put('/users/{id}', [AdminController::class, 'updateUser'])
               ->middleware([CsrfMiddleware::class]);
        
        $router->get('/ingestion', [AdminController::class, 'ingestion']);
        $router->post('/ingestion/sync', [AdminController::class, 'syncOpportunities'])
               ->middleware([CsrfMiddleware::class]);
        $router->post('/ingestion/test', [AdminController::class, 'testApiConnection'])
               ->middleware([CsrfMiddleware::class]);
        
        $router->get('/audit', [AdminController::class, 'auditLog']);
        $router->get('/backups', [AdminController::class, 'backups']);
        $router->post('/backups/create', [AdminController::class, 'createBackup'])
               ->middleware([CsrfMiddleware::class]);
    });
});

// Handle the request
try {
    $method = $_SERVER['REQUEST_METHOD'];
    $uri = $_SERVER['REQUEST_URI'];
    
    $result = $router->dispatch($method, $uri);
    
    if (is_string($result)) {
        echo $result;
    } elseif (is_array($result) || is_object($result)) {
        header('Content-Type: application/json');
        echo json_encode($result);
    }
    
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    
    if (Config::get('app.debug')) {
        echo "<h1>Error {$e->getCode()}</h1>";
        echo "<p>{$e->getMessage()}</p>";
        echo "<pre>{$e->getTraceAsString()}</pre>";
    } else {
        switch ($e->getCode()) {
            case 404:
                echo "Page not found";
                break;
            case 403:
                echo "Access denied";
                break;
            default:
                echo "Internal server error";
                break;
        }
    }
}