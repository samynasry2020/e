<?php

// Simple autoloader for GovTribe Platform
// This replaces Composer's autoloader for our minimal dependency setup

spl_autoload_register(function ($class) {
    // Convert namespace to file path
    $file = str_replace('App\\', __DIR__ . '/../src/', $class);
    $file = str_replace('\\', '/', $file) . '.php';
    
    if (file_exists($file)) {
        require_once $file;
    }
});

// Load utility classes directly since they're always needed
require_once __DIR__ . '/../src/Utils/Config.php';
require_once __DIR__ . '/../src/Utils/Database.php';
require_once __DIR__ . '/../src/Utils/Logger.php';
require_once __DIR__ . '/../src/Utils/Security.php';
require_once __DIR__ . '/../src/Utils/Router.php';