<?php
declare(strict_types=1);

// Define core paths and environment
define('ROOT_PATH', dirname(__DIR__));
define('APP_START', microtime(true));

// Always operate in UTC internally
date_default_timezone_set('UTC');

// Autoloader (simple PSR-4 for App\ namespace)
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = ROOT_PATH . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

// Load configuration
$envFile = ROOT_PATH . '/.env.php';
if (!is_file($envFile)) {
    $envFile = ROOT_PATH . '/.env.sample.php';
}
$__config = require $envFile;
if (!is_array($__config)) {
    throw new RuntimeException('.env.php must return an array config');
}
$GLOBALS['__app_config'] = $__config;

// Ensure required directories exist
$dirs = [ROOT_PATH . '/storage/logs', ROOT_PATH . '/uploads'];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
}

// Error reporting and logging
ini_set('log_errors', '1');
$logPath = $__config['app']['log_path'] ?? (ROOT_PATH . '/storage/logs/app.log');
ini_set('error_log', $logPath);
if (($__config['app']['env'] ?? 'dev') === 'dev') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
}

// Small config helper
if (!function_exists('app_config')) {
    function app_config(?string $key = null, $default = null) {
        $cfg = $GLOBALS['__app_config'] ?? [];
        if ($key === null) {
            return $cfg;
        }
        $parts = explode('.', $key);
        $val = $cfg;
        foreach ($parts as $p) {
            if (is_array($val) && array_key_exists($p, $val)) {
                $val = $val[$p];
            } else {
                return $default;
            }
        }
        return $val;
    }
}

// Session setup
$sessionName = app_config('session.name', 'APPSESSID');
if (session_status() === PHP_SESSION_NONE) {
    session_name($sessionName);
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// Simple helpers
if (!function_exists('now_utc')) {
    function now_utc(): DateTimeImmutable {
        return new DateTimeImmutable('now', new DateTimeZone('UTC'));
    }
}

// Initialize database connection
use App\Core\Database;
Database::init(app_config('db'));

