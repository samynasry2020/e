<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/config.php';

function bootstrap_session_and_csrf(): void {
  static $booted = false;
  if ($booted) { return; }
  ini_set('session.use_strict_mode', '1');
  session_name((string)(config('security.session_name') ?? 'app_session'));
  if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
  }
  if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }
  $booted = true;
}

function csrf_token(): string { bootstrap_session_and_csrf(); return (string)($_SESSION['csrf_token'] ?? ''); }
function csrf_key(): string { return (string)(config('security.csrf_token_key') ?? '_csrf'); }
function verify_csrf_or_abort(): void {
  $key = csrf_key();
  $sent = $_POST[$key] ?? '';
  if (!hash_equals((string)csrf_token(), (string)$sent)) {
    http_response_code(400);
    echo 'Bad CSRF token';
    exit;
  }
}

function e(?string $s): string { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

function redirect(string $url): void {
  header('Location: ' . $url);
  exit;
}

function audit(?int $userId, string $action, ?string $entity, ?int $entityId, array $meta = []): void {
  try {
    $stmt = pdo()->prepare('INSERT INTO audit_log (user_id, action, entity, entity_id, meta, ip, created_at) VALUES (:uid, :action, :entity, :eid, :meta, :ip, UTC_TIMESTAMP())');
    $stmt->execute([
      ':uid' => $userId,
      ':action' => $action,
      ':entity' => $entity,
      ':eid' => $entityId,
      ':meta' => json_encode($meta, JSON_UNESCAPED_SLASHES),
      ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
    ]);
  } catch (Throwable $e) {
    logger('error', 'audit_insert_failed', ['err' => $e->getMessage()]);
  }
}

function current_user(): ?array {
  bootstrap_session_and_csrf();
  static $cached = null;
  if ($cached !== null) { return $cached; }
  $userId = $_SESSION['user_id'] ?? null;
  if (!$userId) { return $cached = null; }
  $stmt = pdo()->prepare('SELECT id, email, role, status FROM users WHERE id = :id LIMIT 1');
  $stmt->execute([':id' => $userId]);
  $user = $stmt->fetch();
  if (!$user) { return $cached = null; }
  // Touch session last_seen
  try {
    $sessId = session_id();
    $update = pdo()->prepare('INSERT INTO sessions (id, user_id, ip, ua, last_seen, created_at) VALUES (:id,:uid,:ip,:ua,UTC_TIMESTAMP(),UTC_TIMESTAMP()) ON DUPLICATE KEY UPDATE user_id=:uid, ip=:ip, ua=:ua, last_seen=UTC_TIMESTAMP()');
    $update->execute([
      ':id' => $sessId,
      ':uid' => (int)$user['id'],
      ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
      ':ua' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 512),
    ]);
  } catch (Throwable $e) {
    logger('error', 'session_touch_failed', ['err' => $e->getMessage()]);
  }
  return $cached = $user;
}

function is_logged_in(): bool { return current_user() !== null; }
function has_role(string $role): bool { $u = current_user(); return $u && $u['role'] === $role; }
function require_login(): void { if (!is_logged_in()) { redirect('/?r=login'); } }
function require_role(array $roles): void {
  require_login();
  $u = current_user();
  if ($u === null || !in_array($u['role'], $roles, true)) {
    http_response_code(403);
    echo 'Forbidden';
    exit;
  }
}

function logout_user(): void {
  bootstrap_session_and_csrf();
  $sessId = session_id();
  try {
    $stmt = pdo()->prepare('DELETE FROM sessions WHERE id = :id');
    $stmt->execute([':id' => $sessId]);
  } catch (Throwable $e) {
    logger('error', 'session_delete_failed', ['err' => $e->getMessage()]);
  }
  $_SESSION = [];
  if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
  }
  session_destroy();
}

