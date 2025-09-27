<?php
$content = ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Dashboard</h1>
    <span class="text-muted">Welcome back, <?= htmlspecialchars($user->getFullName()) ?></span>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="card-body">
                <h5 class="card-title">Total Opportunities</h5>
                <h2 class="text-primary"><?= number_format($stats['total'] ?? 0) ?></h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="card-body">
                <h5 class="card-title">New Today</h5>
                <h2 class="text-success"><?= number_format($stats['new_today'] ?? 0) ?></h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="card-body">
                <h5 class="card-title">Due Soon</h5>
                <h2 class="text-warning"><?= number_format($stats['due_soon'] ?? 0) ?></h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="card-body">
                <h5 class="card-title">Pursuing</h5>
                <h2 class="text-info"><?= number_format($stats['pursue_count'] ?? 0) ?></h2>
            </div>
        </div>
    </div>
</div>

<!-- Pipeline Overview -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Opportunity Pipeline</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <h4 class="text-primary"><?= number_format($stats['new_count'] ?? 0) ?></h4>
                            <small>New</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <h4 class="text-info"><?= number_format($stats['review_count'] ?? 0) ?></h4>
                            <small>Review</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <h4 class="text-success"><?= number_format($stats['pursue_count'] ?? 0) ?></h4>
                            <small>Pursue</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <h4 class="text-warning"><?= number_format(($stats['new_count'] ?? 0) + ($stats['review_count'] ?? 0)) ?></h4>
                            <small>Pending</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="/opportunities" class="btn btn-primary">View All Opportunities</a>
                    <a href="/opportunities?status=New" class="btn btn-outline-primary">New Opportunities</a>
                    <a href="/opportunities?status=Pursue" class="btn btn-outline-success">Pursuing</a>
                    <?php if ($user->isAdmin()): ?>
                    <a href="/admin" class="btn btn-outline-secondary">Admin Panel</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/layouts/app.php';
?>