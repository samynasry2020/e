<?php

declare(strict_types=1);

// Define application constants
define('APP_ROOT', dirname(__DIR__));
define('APP_SRC', APP_ROOT . '/src');
define('APP_VIEWS', APP_ROOT . '/views');
define('APP_PUBLIC', APP_ROOT . '/public');
define('APP_STORAGE', APP_ROOT . '/storage');
define('APP_UPLOADS', APP_ROOT . '/uploads');

// Simple autoloader
spl_autoload_register(function ($class) {
    // Convert namespace to file path
    $file = str_replace('App\\', APP_SRC . '/', $class);
    $file = str_replace('\\', '/', $file) . '.php';
    
    if (file_exists($file)) {
        require_once $file;
    }
});

// Include utility classes
require_once APP_SRC . '/Utils/Config.php';
require_once APP_SRC . '/Utils/Database.php';
require_once APP_SRC . '/Utils/Logger.php';
require_once APP_SRC . '/Utils/Security.php';
require_once APP_SRC . '/Utils/Router.php';