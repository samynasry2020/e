<?php
declare(strict_types=1);

$root = realpath(__DIR__ . '/..');
require $root . '/app/Helpers/autoload.php';
require $root . '/config/config.php';
require $root . '/app/Helpers/util.php';
use App\Controllers\AuthController;

// Sessions and CSRF bootstrap
bootstrap_session_and_csrf();
function is_post(): bool { return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST'; }
function require_csrf(): void {
  $key = (string)(config('security.csrf_token_key') ?? '_csrf');
  $sent = $_POST[$key] ?? '';
  if (!hash_equals((string)csrf_token(), (string)$sent)) {
    http_response_code(400);
    echo 'Bad CSRF token';
    exit;
  }
}

$route = $_GET['r'] ?? 'home';

function view(string $title, string $bodyHtml): void {
  $csrf = htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8');
  echo '<!doctype html><html lang="en"><head>';
  echo '<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">';
  echo '<title>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</title>';
  echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">';
  echo '</head><body class="bg-light">';
  echo '<nav class="navbar navbar-expand-lg navbar-dark bg-dark"><div class="container-fluid">';
  echo '<a class="navbar-brand" href="/?r=home">Private GT</a>';
  echo '<div class="navbar-nav">';
  echo '<a class="nav-link" href="/?r=opportunities">Opportunities</a>';
  echo '<a class="nav-link" href="/?r=admin">Admin</a>';
  echo '</div></div></nav>';
  echo '<main class="container py-4">';
  echo $bodyHtml;
  echo '<form method="post" class="d-none"><input type="hidden" name="_csrf" value="' . $csrf . '"></form>';
  echo '</main>';
  echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>';
  echo '</body></html>';
}

switch ($route) {
  case 'health':
    $ok = true;
    $err = '';
    try {
      pdo()->query('SELECT 1');
    } catch (Throwable $e) {
      $ok = false; $err = $e->getMessage();
    }
    header('Content-Type: application/json');
    echo json_encode(['ok' => $ok, 'error' => $err], JSON_UNESCAPED_SLASHES);
    break;

  case 'home':
    view('Dashboard', '<div class="card"><div class="card-body">Welcome. Use Admin to configure SAM API key, then run ingest.</div></div>');
    break;

  case 'opportunities':
    view('Opportunities', '<div class="alert alert-info">List view coming soon.</div>');
    break;

  case 'admin':
    require_role(['Admin']);
    view('Admin', '<div class="alert alert-secondary">Admin settings UI coming soon.</div>');
    break;

  case 'login':
    if (is_post()) { AuthController::doLogin(); break; }
    view('Login', (function(){ ob_start(); \App\Controllers\AuthController::showLogin(); return ob_get_clean(); })());
    break;

  case 'logout':
    AuthController::logout();
    break;

  default:
    http_response_code(404);
    view('Not Found', '<div class="alert alert-danger">Page not found</div>');
}

