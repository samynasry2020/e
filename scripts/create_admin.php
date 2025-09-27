#!/usr/bin/env php
<?php
declare(strict_types=1);

$root = realpath(__DIR__ . '/..');
require $root . '/app/Helpers/autoload.php';
require $root . '/config/config.php';

function prompt(string $q): string { echo $q . ': '; return trim((string)fgets(STDIN)); }

function main(): int {
  $email = getenv('ADMIN_EMAIL') ?: prompt('Admin email');
  $pass = getenv('ADMIN_PASSWORD') ?: prompt('Admin password');
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { fwrite(STDERR, "Invalid email\n"); return 1; }
  if ($pass === '') { fwrite(STDERR, "Password required\n"); return 1; }
  $hash = password_hash($pass, PASSWORD_ARGON2ID);
  $pdo = pdo();
  $stmt = $pdo->prepare('INSERT INTO users (email, password_hash, role, status, created_at, updated_at) VALUES (:email, :hash, "Admin", "Active", UTC_TIMESTAMP(), UTC_TIMESTAMP()) ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), role = "Admin", status = "Active", updated_at = UTC_TIMESTAMP()');
  $stmt->execute([':email' => $email, ':hash' => $hash]);
  echo "Admin user ensured: $email\n";
  return 0;
}

exit(main());

