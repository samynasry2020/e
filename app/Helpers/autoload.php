<?php

spl_autoload_register(function (string $class): void {
  // PSR-4 light: map App\ to app/ directory
  $prefix = 'App\\';
  $baseDir = dirname(__DIR__) . DIRECTORY_SEPARATOR;
  if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
    return; // not our namespace
  }
  $relative = substr($class, strlen($prefix));
  $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';
  if (is_file($file)) {
    require $file;
  }
});

