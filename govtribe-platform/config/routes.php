<?php

declare(strict_types=1);

/**
 * GovTribe Platform Routes
 */

use GovTribe\Controllers\AuthController;
use GovTribe\Controllers\DashboardController;
use GovTribe\Controllers\OpportunityController;
use GovTribe\Controllers\AdminController;
use GovTribe\Middleware\AuthMiddleware;
use GovTribe\Middleware\CsrfMiddleware;

// Global middleware
$router->middleware(function() {
    // CSRF protection for all POST/PUT/DELETE requests
    if (in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT', 'DELETE'])) {
        $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!Security::verifyCsrfToken($token)) {
            http_response_code(403);
            echo '<h1>403 - Forbidden</h1><p>Invalid CSRF token.</p>';
            return false;
        }
    }
    return true;
});

// Public routes
$router->get('/', [DashboardController::class, 'index']);
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);
$router->get('/health', function() {
    $router->json([
        'status' => 'ok',
        'timestamp' => date('Y-m-d H:i:s'),
        'version' => '1.0.0'
    ]);
});

// Protected routes (require authentication)
$authMiddleware = function() {
    if (!AuthService::getInstance()->isAuthenticated()) {
        header('Location: /login');
        return false;
    }
    return true;
};

// Dashboard
$router->get('/dashboard', [DashboardController::class, 'index'], [$authMiddleware]);

// Opportunities
$router->get('/opportunities', [OpportunityController::class, 'index'], [$authMiddleware]);
$router->get('/opportunities/create', [OpportunityController::class, 'create'], [$authMiddleware]);
$router->post('/opportunities', [OpportunityController::class, 'store'], [$authMiddleware]);
$router->get('/opportunities/{id}', [OpportunityController::class, 'show'], [$authMiddleware]);
$router->get('/opportunities/{id}/edit', [OpportunityController::class, 'edit'], [$authMiddleware]);
$router->put('/opportunities/{id}', [OpportunityController::class, 'update'], [$authMiddleware]);
$router->delete('/opportunities/{id}', [OpportunityController::class, 'delete'], [$authMiddleware]);
$router->post('/opportunities/{id}/score', [OpportunityController::class, 'score'], [$authMiddleware]);
$router->post('/opportunities/{id}/status', [OpportunityController::class, 'updateStatus'], [$authMiddleware]);

// Admin routes (require admin role)
$adminMiddleware = function() {
    $authService = AuthService::getInstance();
    if (!$authService->isAuthenticated()) {
        header('Location: /login');
        return false;
    }
    if (!$authService->isAdmin()) {
        http_response_code(403);
        echo '<h1>403 - Forbidden</h1><p>Administrator access required.</p>';
        return false;
    }
    return true;
};

$router->get('/admin', [AdminController::class, 'index'], [$adminMiddleware]);
$router->get('/admin/users', [AdminController::class, 'users'], [$adminMiddleware]);
$router->get('/admin/users/create', [AdminController::class, 'createUser'], [$adminMiddleware]);
$router->post('/admin/users', [AdminController::class, 'storeUser'], [$adminMiddleware]);
$router->get('/admin/users/{id}/edit', [AdminController::class, 'editUser'], [$adminMiddleware]);
$router->put('/admin/users/{id}', [AdminController::class, 'updateUser'], [$adminMiddleware]);
$router->delete('/admin/users/{id}', [AdminController::class, 'deleteUser'], [$adminMiddleware]);
$router->get('/admin/settings', [AdminController::class, 'settings'], [$adminMiddleware]);
$router->post('/admin/settings', [AdminController::class, 'updateSettings'], [$adminMiddleware]);
$router->get('/admin/ingestion', [AdminController::class, 'ingestion'], [$adminMiddleware]);
$router->post('/admin/ingestion/sync', [AdminController::class, 'syncNow'], [$adminMiddleware]);
$router->get('/admin/audit', [AdminController::class, 'audit'], [$adminMiddleware]);

// API routes
$router->get('/api/opportunities', [OpportunityController::class, 'apiIndex'], [$authMiddleware]);
$router->get('/api/opportunities/{id}', [OpportunityController::class, 'apiShow'], [$authMiddleware]);
$router->get('/api/stats', [DashboardController::class, 'apiStats'], [$authMiddleware]);

// Catch-all for 404
$router->any('*', function() {
    http_response_code(404);
    echo '<h1>404 - Not Found</h1>';
});