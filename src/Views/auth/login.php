<?php
$content = ob_start();
?>

<div class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="card shadow-lg" style="width: 100%; max-width: 400px;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <i class="bi bi-shield-check text-primary" style="font-size: 3rem;"></i>
                <h1 class="h3 mt-3 mb-0">GovTribe Platform</h1>
                <p class="text-muted">Sign in to your account</p>
            </div>

            <form method="POST" action="/login">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" 
                           class="form-control form-control-lg" 
                           id="email" 
                           name="email" 
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                           required 
                           autofocus>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" 
                           class="form-control form-control-lg" 
                           id="password" 
                           name="password" 
                           required>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">
                        Remember me for 30 days
                    </label>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                    </button>
                </div>
            </form>

            <div class="text-center mt-4">
                <small class="text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    Contact your administrator for account access
                </small>
            </div>
        </div>
    </div>
</div>

<div class="position-fixed bottom-0 start-0 p-3">
    <small class="text-muted">
        <i class="bi bi-shield-lock me-1"></i>
        Secure Government Contract Platform
    </small>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>