<?php
$content = ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-speedometer2 me-2"></i>Dashboard
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-download me-1"></i>Export
            </button>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-sm btn-primary" onclick="refreshDashboard()">
                <i class="bi bi-arrow-clockwise me-1"></i>Refresh
            </button>
        </div>
    </div>
</div>

<!-- Key Metrics Row -->
<div class="row mb-4">
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="text-primary mb-2">
                    <i class="bi bi-plus-circle-fill" style="font-size: 2rem;"></i>
                </div>
                <h3 class="mb-0"><?= number_format($stats['new_24h']) ?></h3>
                <small class="text-muted">New (24h)</small>
            </div>
        </div>
    </div>
    
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="text-warning mb-2">
                    <i class="bi bi-clock-fill" style="font-size: 2rem;"></i>
                </div>
                <h3 class="mb-0"><?= number_format($stats['due_7d']) ?></h3>
                <small class="text-muted">Due ≤7 days</small>
            </div>
        </div>
    </div>
    
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="text-info mb-2">
                    <i class="bi bi-activity" style="font-size: 2rem;"></i>
                </div>
                <h3 class="mb-0"><?= number_format($stats['total_active']) ?></h3>
                <small class="text-muted">Active Total</small>
            </div>
        </div>
    </div>
    
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="text-success mb-2">
                    <i class="bi bi-funnel-fill" style="font-size: 2rem;"></i>
                </div>
                <h3 class="mb-0"><?= number_format($stats['in_pipeline']) ?></h3>
                <small class="text-muted">In Pipeline</small>
            </div>
        </div>
    </div>
    
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="text-success mb-2">
                    <i class="bi bi-trophy-fill" style="font-size: 2rem;"></i>
                </div>
                <h3 class="mb-0"><?= number_format($stats['awards_year']) ?></h3>
                <small class="text-muted">Awards (YTD)</small>
            </div>
        </div>
    </div>
    
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="text-success mb-2">
                    <i class="bi bi-currency-dollar" style="font-size: 2rem;"></i>
                </div>
                <h3 class="mb-0"><?= number_format($stats['award_value_year'] / 1000000, 1) ?>M</h3>
                <small class="text-muted">Value (YTD)</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Pipeline Chart -->
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-bar-chart me-2"></i>Opportunity Pipeline
                </h5>
            </div>
            <div class="card-body">
                <canvas id="pipelineChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- Last Ingestion Status -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0">
                <h5 class="card-title mb-0">
                    <i class="bi bi-arrow-down-circle me-2"></i>Data Ingestion
                </h5>
            </div>
            <div class="card-body">
                <?php if ($last_ingestion): ?>
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <i class="bi bi-<?= $last_ingestion['success'] ? 'check-circle-fill text-success' : 'x-circle-fill text-danger' ?>" style="font-size: 1.5rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">
                                <?= $last_ingestion['success'] ? 'Last Sync Successful' : 'Last Sync Failed' ?>
                            </h6>
                            <small class="text-muted">
                                <?= date('M j, Y g:i A', strtotime($last_ingestion['timestamp'])) ?>
                            </small>
                        </div>
                    </div>
                    
                    <?php if ($last_ingestion['success'] && isset($last_ingestion['summary'])): ?>
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="text-success">
                                    <strong><?= $last_ingestion['summary']['created'] ?></strong>
                                </div>
                                <small class="text-muted">Created</small>
                            </div>
                            <div class="col-4">
                                <div class="text-primary">
                                    <strong><?= $last_ingestion['summary']['updated'] ?></strong>
                                </div>
                                <small class="text-muted">Updated</small>
                            </div>
                            <div class="col-4">
                                <div class="text-warning">
                                    <strong><?= $last_ingestion['summary']['rejected'] ?></strong>
                                </div>
                                <small class="text-muted">Rejected</small>
                            </div>
                        </div>
                    <?php elseif (!$last_ingestion['success']): ?>
                        <div class="alert alert-danger py-2">
                            <small><?= htmlspecialchars($last_ingestion['error'] ?? 'Unknown error') ?></small>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-info-circle text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2">No ingestion data available</p>
                    </div>
                <?php endif; ?>
                
                <?php if ($user && $user->getRole() === 'Admin'): ?>
                    <div class="d-grid mt-3">
                        <a href="/admin/ingestion" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-gear me-1"></i>Manage Ingestion
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Recent Opportunities -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>Recent Opportunities
                </h5>
                <a href="/opportunities" class="btn btn-outline-primary btn-sm">
                    View All <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($recent_opportunities)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Agency</th>
                                    <th>Due Date</th>
                                    <th>Score</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_opportunities as $opp): ?>
                                    <tr>
                                        <td>
                                            <a href="/opportunities/<?= $opp['id'] ?>" class="text-decoration-none">
                                                <?= htmlspecialchars($this->truncate($opp['title'], 60)) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?= htmlspecialchars($opp['agency_name'] ?? 'Unknown') ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php if ($opp['due_at']): ?>
                                                <small class="<?= strtotime($opp['due_at']) < time() + (7 * 24 * 60 * 60) ? 'text-danger' : 'text-muted' ?>">
                                                    <?= $this->formatDate($opp['due_at']) ?>
                                                </small>
                                            <?php else: ?>
                                                <small class="text-muted">TBD</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge score-badge <?= $opp['score'] >= 70 ? 'score-high' : ($opp['score'] >= 40 ? 'score-medium' : 'score-low') ?>">
                                                <?= $opp['score'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge status-<?= strtolower(str_replace(' ', '-', $opp['status'])) ?>">
                                                <?= htmlspecialchars($opp['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="/opportunities/<?= $opp['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                        <h5 class="text-muted mt-3">No opportunities found</h5>
                        <p class="text-muted">Run data ingestion to fetch opportunities from SAM.gov</p>
                        <?php if ($user && $user->getRole() === 'Admin'): ?>
                            <a href="/admin/ingestion" class="btn btn-primary">
                                <i class="bi bi-arrow-down-circle me-2"></i>Start Ingestion
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

// Additional JavaScript for charts
$additional_js = '
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Pipeline Chart
const ctx = document.getElementById("pipelineChart").getContext("2d");
const pipelineData = ' . json_encode($pipeline_data) . ';

new Chart(ctx, {
    type: "bar",
    data: {
        labels: Object.keys(pipelineData),
        datasets: [{
            label: "Opportunities",
            data: Object.values(pipelineData),
            backgroundColor: [
                "#cce5ff", // New
                "#fff3cd", // Review  
                "#d4edda", // Pursue
                "#f8d7da", // No-Bid
                "#d1ecf1", // Awarded
                "#e2e3e5"  // Lost
            ],
            borderColor: [
                "#004085",
                "#856404", 
                "#155724",
                "#721c24",
                "#0c5460",
                "#383d41"
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

function refreshDashboard() {
    location.reload();
}
</script>';

include __DIR__ . '/layout.php';
?>