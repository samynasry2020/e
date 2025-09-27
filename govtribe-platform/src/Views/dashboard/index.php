<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - GovTribe Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .stat-card.success {
            background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
        }
        .stat-card.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .stat-card.info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .opportunity-card {
            border-left: 4px solid #667eea;
            transition: all 0.3s ease;
        }
        .opportunity-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .score-badge {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }
        .due-soon {
            border-left-color: #f5576c !important;
        }
        .high-score {
            border-left-color: #56ab2f !important;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/dashboard">
                <i class="bi bi-shield-check me-2"></i>
                GovTribe Platform
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="/dashboard">
                            <i class="bi bi-speedometer2 me-1"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/opportunities">
                            <i class="bi bi-briefcase me-1"></i>
                            Opportunities
                        </a>
                    </li>
                    <?php if ($user->canAccessAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin">
                            <i class="bi bi-gear me-1"></i>
                            Admin
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>
                            <?= htmlspecialchars($user->email) ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="/logout" class="d-inline">
                                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <!-- Welcome Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h3 mb-1">Welcome back, <?= htmlspecialchars($user->email) ?>!</h1>
                <p class="text-muted">Here's what's happening with your federal contract opportunities.</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number"><?= $stats['new_24h'] ?></div>
                    <div class="stat-label">
                        <i class="bi bi-plus-circle me-1"></i>
                        New (24h)
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card warning">
                    <div class="stat-number"><?= $stats['due_7d'] ?></div>
                    <div class="stat-label">
                        <i class="bi bi-clock me-1"></i>
                        Due ≤7d
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card info">
                    <div class="stat-number"><?= $stats['total_active'] ?></div>
                    <div class="stat-label">
                        <i class="bi bi-briefcase me-1"></i>
                        Active
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card success">
                    <div class="stat-number"><?= $stats['high_score'] ?></div>
                    <div class="stat-label">
                        <i class="bi bi-star me-1"></i>
                        High Score
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Pipeline Chart -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-diagram-3 me-2"></i>
                            Pipeline Status
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="pipelineChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-lightning me-2"></i>
                            Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="/opportunities" class="btn btn-outline-primary">
                                <i class="bi bi-search me-2"></i>
                                Browse Opportunities
                            </a>
                            <?php if ($user->can('manage_ingestion')): ?>
                            <button class="btn btn-outline-success" onclick="syncNow()">
                                <i class="bi bi-arrow-clockwise me-2"></i>
                                Sync Now
                            </button>
                            <?php endif; ?>
                            <a href="/opportunities?filter=high_score" class="btn btn-outline-warning">
                                <i class="bi bi-star me-2"></i>
                                High Score Opportunities
                            </a>
                            <a href="/opportunities?filter=due_soon" class="btn btn-outline-danger">
                                <i class="bi bi-clock me-2"></i>
                                Due Soon
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Opportunities -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-clock-history me-2"></i>
                            Recent Opportunities
                        </h5>
                        <a href="/opportunities" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recent_opportunities)): ?>
                            <div class="text-center py-4">
                                <i class="bi bi-inbox display-1 text-muted"></i>
                                <p class="text-muted mt-3">No opportunities found. Run a sync to get started!</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($recent_opportunities as $opp): ?>
                                <div class="card opportunity-card mb-3 <?= $opp['score'] >= 70 ? 'high-score' : '' ?> <?= $opp['days_until_due'] !== null && $opp['days_until_due'] <= 3 ? 'due-soon' : '' ?>">
                                    <div class="card-body py-3">
                                        <div class="row align-items-center">
                                            <div class="col-md-8">
                                                <h6 class="mb-1">
                                                    <a href="/opportunities/<?= $opp['id'] ?>" class="text-decoration-none">
                                                        <?= htmlspecialchars($opp['title']) ?>
                                                    </a>
                                                </h6>
                                                <p class="text-muted small mb-1">
                                                    <i class="bi bi-building me-1"></i>
                                                    <?= htmlspecialchars($opp['agency_name'] ?? 'Unknown Agency') ?>
                                                </p>
                                                <div class="d-flex gap-3 small text-muted">
                                                    <span>
                                                        <i class="bi bi-calendar me-1"></i>
                                                        Due: <?= $opp['due_at'] ? date('M j, Y', strtotime($opp['due_at'])) : 'No deadline' ?>
                                                    </span>
                                                    <span>
                                                        <i class="bi bi-tag me-1"></i>
                                                        <?= htmlspecialchars($opp['set_aside'] ?? 'N/A') ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-4 text-end">
                                                <div class="mb-2">
                                                    <span class="badge score-badge <?= $opp['score'] >= 70 ? 'bg-success' : ($opp['score'] >= 50 ? 'bg-warning' : 'bg-secondary') ?>">
                                                        Score: <?= $opp['score'] ?>
                                                    </span>
                                                </div>
                                                <div class="mb-2">
                                                    <span class="badge bg-primary">
                                                        <?= htmlspecialchars($opp['status']) ?>
                                                    </span>
                                                </div>
                                                <?php if ($opp['days_until_due'] !== null && $opp['days_until_due'] <= 7): ?>
                                                    <small class="text-warning">
                                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                                        <?= $opp['days_until_due'] <= 0 ? 'Overdue' : $opp['days_until_due'] . ' days left' ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Due Soon -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-exclamation-triangle me-2 text-warning"></i>
                            Due Soon
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($due_soon_opportunities)): ?>
                            <div class="text-center py-3">
                                <i class="bi bi-check-circle text-success"></i>
                                <p class="text-muted mt-2 small">No urgent deadlines!</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($due_soon_opportunities as $opp): ?>
                                <div class="d-flex justify-content-between align-items-center mb-3 p-2 border-start border-warning border-3">
                                    <div>
                                        <h6 class="mb-1 small">
                                            <a href="/opportunities/<?= $opp['id'] ?>" class="text-decoration-none">
                                                <?= htmlspecialchars(substr($opp['title'], 0, 50)) ?>...
                                            </a>
                                        </h6>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($opp['agency_name'] ?? 'Unknown') ?>
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-warning small">
                                            <?= $opp['days_until_due'] <= 0 ? 'Overdue' : $opp['days_until_due'] . 'd' ?>
                                        </span>
                                        <div class="badge bg-primary small mt-1">
                                            <?= $opp['score'] ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Pipeline Chart
        const pipelineData = <?= json_encode($pipeline_data['status'] ?? []) ?>;
        const ctx = document.getElementById('pipelineChart').getContext('2d');
        
        const pipelineChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: pipelineData.map(item => item.status),
                datasets: [{
                    data: pipelineData.map(item => item.count),
                    backgroundColor: [
                        '#667eea',
                        '#f093fb',
                        '#56ab2f',
                        '#f5576c',
                        '#4facfe',
                        '#6c757d'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Sync Now function
        function syncNow() {
            if (confirm('This will trigger a manual sync with SAM.gov. Continue?')) {
                fetch('/admin/ingestion/sync', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': '<?= htmlspecialchars($csrf_token) ?>'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Sync initiated successfully!');
                        location.reload();
                    } else {
                        alert('Sync failed: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    alert('Sync failed: ' + error.message);
                });
            }
        }

        // Auto-refresh dashboard every 5 minutes
        setInterval(() => {
            location.reload();
        }, 300000);
    </script>
</body>
</html>