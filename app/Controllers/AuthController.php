<?php
declare(strict_types=1);

namespace App\Controllers;

require_once __DIR__ . '/../Helpers/util.php';

class AuthController
{
  public static function showLogin(): void
  {
    bootstrap_session_and_csrf();
    $csrf = e(csrf_token());
    echo '<div class="row justify-content-center"><div class="col-md-4">';
    echo '<div class="card"><div class="card-body">';
    echo '<h5 class="card-title">Sign in</h5>';
    if (!empty($_GET['error'])) {
      echo '<div class="alert alert-danger">' . e($_GET['error']) . '</div>';
    }
    echo '<form method="post" action="/?r=login">';
    echo '<input type="hidden" name="' . e(csrf_key()) . '" value="' . $csrf . '">';
    echo '<div class="mb-3"><label class="form-label">Email</label><input required type="email" name="email" class="form-control"></div>';
    echo '<div class="mb-3"><label class="form-label">Password</label><input required type="password" name="password" class="form-control"></div>';
    echo '<button class="btn btn-primary w-100" type="submit">Sign in</button>';
    echo '</form>';
    echo '</div></div></div></div>';
  }

  public static function doLogin(): void
  {
    verify_csrf_or_abort();
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    if ($email === '' || $password === '') { header('Location: /?r=login&error=Missing+credentials'); return; }

    // Throttling key
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $windowMinutes = (int)(config('security.login_window_minutes') ?? 15);
    $maxAttempts = (int)(config('security.login_max_attempts') ?? 5);
    $since = gmdate('Y-m-d H:i:s', time() - ($windowMinutes * 60));
    $stmt = pdo()->prepare('SELECT COUNT(*) AS cnt FROM audit_log WHERE action = :action AND ip = :ip AND created_at >= :since');
    $stmt->execute([':action' => 'login_failed', ':ip' => $ip, ':since' => $since]);
    $row = $stmt->fetch();
    if ($row && (int)$row['cnt'] >= $maxAttempts) {
      header('Location: /?r=login&error=Too+many+attempts.+Try+later');
      return;
    }

    $stmt = pdo()->prepare('SELECT id, email, password_hash, role, status FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();
    if (!$user || $user['status'] !== 'Active' || !password_verify($password, $user['password_hash'])) {
      audit(null, 'login_failed', 'User', null, ['email' => $email]);
      header('Location: /?r=login&error=Invalid+credentials');
      return;
    }

    bootstrap_session_and_csrf();
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int)$user['id'];
    audit((int)$user['id'], 'login_success', 'User', (int)$user['id']);
    header('Location: /?r=home');
  }

  public static function logout(): void
  {
    $u = current_user();
    if ($u) { audit((int)$u['id'], 'logout', 'User', (int)$u['id']); }
    logout_user();
    header('Location: /?r=login');
  }
}

