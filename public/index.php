<?php
declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';

use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\AdminController;

$router = new Router();

$router->add('GET', '/', [HomeController::class, 'index']);
$router->add('GET', '/login', [AuthController::class, 'showLogin']);
$router->add('POST', '/login', [AuthController::class, 'login']);
$router->add('GET', '/logout', [AuthController::class, 'logout']);
$router->add('GET', '/admin', [AdminController::class, 'settings']);
$router->add('POST', '/admin/settings', [AdminController::class, 'saveSettings']);
$router->add('GET', '/health', function (): void {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'ok', 'time' => (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format(DateTimeInterface::ATOM)]);
});

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

$router->dispatch($method, $path);

