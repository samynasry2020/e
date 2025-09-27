<div class="row justify-content-center">
  <div class="col-md-5">
    <div class="card shadow-sm">
      <div class="card-body">
        <h1 class="h4 mb-3">Sign in</h1>
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <form method="post" action="/login">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(App\Core\Csrf::token(), ENT_QUOTES, 'UTF-8'); ?>">
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input class="form-control" type="email" name="email" required autofocus>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input class="form-control" type="password" name="password" required>
          </div>
          <div class="d-grid">
            <button class="btn btn-primary" type="submit">Login</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
