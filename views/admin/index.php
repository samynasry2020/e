<?php
$content = ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Admin Dashboard</h1>
    <button class="btn btn-primary" onclick="triggerSync()">Sync Opportunities</button>
</div>

<!-- System Status -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title">Database</h5>
                <span class="badge bg-<?= $stats['database_status'] === 'Connected' ? 'success' : 'danger' ?> fs-6">
                    <?= htmlspecialchars($stats['database_status']) ?>
                </span>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title">SAM API</h5>
                <span class="badge bg-<?= strpos($stats['sam_api_status'], 'Connected') !== false ? 'success' : 'danger' ?> fs-6">
                    <?= htmlspecialchars($stats['sam_api_status']) ?>
                </span>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title">Total Users</h5>
                <h3 class="text-primary"><?= number_format($stats['users']) ?></h3>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title">Total Files</h5>
                <h3 class="text-info"><?= number_format($stats['files']) ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Opportunities</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-primary"><?= number_format($stats['opportunities']) ?></h4>
                        <small>Total</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success"><?= number_format($stats['recent_opportunities']) ?></h4>
                        <small>Last 7 Days</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Agencies</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-12">
                        <h4 class="text-info"><?= number_format($stats['agencies']) ?></h4>
                        <small>Total Agencies</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="/admin/users" class="btn btn-outline-primary">Manage Users</a>
                    <a href="/admin/settings" class="btn btn-outline-secondary">System Settings</a>
                    <button class="btn btn-outline-success" onclick="triggerSync()">Sync SAM.gov</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">System Information</h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-6">PHP Version:</dt>
                    <dd class="col-sm-6"><?= PHP_VERSION ?></dd>
                    
                    <dt class="col-sm-6">Server:</dt>
                    <dd class="col-sm-6"><?= $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' ?></dd>
                    
                    <dt class="col-sm-6">Environment:</dt>
                    <dd class="col-sm-6"><?= \App\Utils\Config::get('app.env', 'unknown') ?></dd>
                    
                    <dt class="col-sm-6">Debug Mode:</dt>
                    <dd class="col-sm-6">
                        <span class="badge bg-<?= \App\Utils\Config::get('app.debug', false) ? 'warning' : 'success' ?>">
                            <?= \App\Utils\Config::get('app.debug', false) ? 'ON' : 'OFF' ?>
                        </span>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<!-- Sync Results -->
<?php if (isset($_GET['sync']) && $_GET['sync'] === 'completed' && isset($_SESSION['sync_results'])): ?>
<div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
    <h6>Sync Completed Successfully!</h6>
    <ul class="mb-0">
        <li>Total Fetched: <?= number_format($_SESSION['sync_results']['total_fetched'] ?? 0) ?></li>
        <li>Created: <?= number_format($_SESSION['sync_results']['created'] ?? 0) ?></li>
        <li>Updated: <?= number_format($_SESSION['sync_results']['updated'] ?? 0) ?></li>
        <li>Rejected: <?= number_format($_SESSION['sync_results']['rejected'] ?? 0) ?></li>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['sync_results']); ?>
<?php endif; ?>

<?php if (isset($_GET['sync']) && $_GET['sync'] === 'error' && isset($_SESSION['sync_error'])): ?>
<div class="alert alert-danger alert-dismissible fade show mt-4" role="alert">
    <h6>Sync Failed!</h6>
    <p class="mb-0"><?= htmlspecialchars($_SESSION['sync_error']) ?></p>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['sync_error']); ?>
<?php endif; ?>

<script>
function triggerSync() {
    if (confirm('This will fetch new opportunities from SAM.gov. Continue?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/sync';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = 'csrf_token';
        csrfInput.value = '<?= \App\Utils\Security::generateCsrfToken() ?>';
        
        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/layouts/app.php';
?>