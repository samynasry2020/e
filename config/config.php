<?php

declare(strict_types=1);

// Configuration loader and helpers

function app_root(): string {
  static $root = null;
  if ($root === null) {
    $root = realpath(__DIR__ . '/..');
  }
  return $root ?: __DIR__ . '/..';
}

function load_env_config(): array {
  // Resolution order: ENV var -> /var/www/app/.env.php -> project root .env.php -> config/env.php -> config/env.example.php
  $paths = [];
  $envHint = getenv('APP_ENV_FILE');
  if ($envHint && file_exists($envHint)) {
    $paths[] = $envHint;
  }
  $paths[] = '/var/www/app/.env.php';
  $paths[] = app_root() . '/.env.php';
  $paths[] = app_root() . '/config/env.php';
  $paths[] = app_root() . '/config/env.example.php';

  foreach ($paths as $p) {
    if (is_file($p)) {
      /** @var array $cfg */
      $cfg = require $p;
      if (!is_array($cfg)) {
        throw new RuntimeException('Environment file did not return array: ' . $p);
      }
      return $cfg;
    }
  }
  throw new RuntimeException('No environment configuration found. Create /var/www/app/.env.php or ./.env.php');
}

function config(?string $key = null, $default = null) {
  static $cfg = null;
  if ($cfg === null) {
    $cfg = load_env_config();
    // Set PHP timezone for display; DB will store UTC
    if (isset($cfg['app']['tz'])) {
      date_default_timezone_set((string)$cfg['app']['tz']);
    }
  }
  if ($key === null) {
    return $cfg;
  }
  $segments = explode('.', $key);
  $value = $cfg;
  foreach ($segments as $seg) {
    if (!is_array($value) || !array_key_exists($seg, $value)) {
      return $default;
    }
    $value = $value[$seg];
  }
  return $value;
}

function pdo(): PDO {
  static $pdo = null;
  if ($pdo instanceof PDO) {
    return $pdo;
  }
  $host = (string)config('db.host');
  $port = (int)config('db.port');
  $db   = (string)config('db.database');
  $user = (string)config('db.user');
  $pass = (string)config('db.pass');
  $charset = (string)(config('db.charset') ?? 'utf8mb4');

  $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $host, $port, $db, $charset);
  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ]);
  // Force UTC at DB connection level
  $pdo->exec("SET time_zone = '+00:00'");
  return $pdo;
}

function logger(string $level, string $message, array $context = []): void {
  $file = (string)(config('app.log_file') ?? (app_root() . '/storage/logs/app.log'));
  $dir = dirname($file);
  if (!is_dir($dir)) {
    @mkdir($dir, 0775, true);
  }
  $entry = [
    'ts' => gmdate('c'),
    'level' => $level,
    'msg' => $message,
    'ctx' => $context,
  ];
  @file_put_contents($file, json_encode($entry, JSON_UNESCAPED_SLASHES) . PHP_EOL, FILE_APPEND);
}

