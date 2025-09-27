<?php
$content = ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Opportunities</h1>
    <div>
        <span class="badge bg-primary"><?= number_format($pagination['total_count']) ?> total</span>
        <?php if ($user->isAdmin()): ?>
        <button class="btn btn-sm btn-success ms-2" onclick="triggerSync()">Sync Now</button>
        <?php endif; ?>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-header">
        <h6 class="mb-0">Filters</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="/opportunities">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="New" <?= ($filters['status'] ?? '') === 'New' ? 'selected' : '' ?>>New</option>
                        <option value="Review" <?= ($filters['status'] ?? '') === 'Review' ? 'selected' : '' ?>>Review</option>
                        <option value="Pursue" <?= ($filters['status'] ?? '') === 'Pursue' ? 'selected' : '' ?>>Pursue</option>
                        <option value="No-Bid" <?= ($filters['status'] ?? '') === 'No-Bid' ? 'selected' : '' ?>>No-Bid</option>
                        <option value="Awarded" <?= ($filters['status'] ?? '') === 'Awarded' ? 'selected' : '' ?>>Awarded</option>
                        <option value="Lost" <?= ($filters['status'] ?? '') === 'Lost' ? 'selected' : '' ?>>Lost</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Agency</label>
                    <select name="agency_id" class="form-select">
                        <option value="">All Agencies</option>
                        <?php foreach ($agencies as $agency): ?>
                        <option value="<?= $agency->id ?>" <?= ($filters['agency_id'] ?? '') == $agency->id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($agency->name) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">NAICS Code</label>
                    <select name="naics_code" class="form-select">
                        <option value="">All NAICS</option>
                        <?php foreach ($naics_codes as $code => $description): ?>
                        <option value="<?= $code ?>" <?= ($filters['naics_code'] ?? '') === $code ? 'selected' : '' ?>>
                            <?= htmlspecialchars($description) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Set-Aside</label>
                    <select name="set_aside" class="form-select">
                        <option value="">All Set-Asides</option>
                        <?php foreach ($set_aside_options as $code => $description): ?>
                        <option value="<?= $code ?>" <?= ($filters['set_aside'] ?? '') === $code ? 'selected' : '' ?>>
                            <?= htmlspecialchars($description) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Search opportunities..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Score Range</label>
                    <div class="input-group">
                        <input type="number" name="score_min" class="form-control" placeholder="Min" value="<?= htmlspecialchars($filters['score_min'] ?? '') ?>">
                        <span class="input-group-text">-</span>
                        <input type="number" name="score_max" class="form-control" placeholder="Max" value="<?= htmlspecialchars($filters['score_max'] ?? '') ?>">
                    </div>
                </div>
                
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Apply Filters</button>
                    <a href="/opportunities" class="btn btn-outline-secondary">Clear</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Opportunities Table -->
<div class="card">
    <div class="card-header">
        <h6 class="mb-0">Opportunities (<?= number_format($pagination['total_count']) ?> total)</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Agency</th>
                        <th>Due Date</th>
                        <th>Score</th>
                        <th>Status</th>
                        <th>Set-Aside</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($opportunities as $opportunity): ?>
                    <tr>
                        <td>
                            <div>
                                <strong><?= htmlspecialchars($opportunity->title) ?></strong>
                                <br>
                                <small class="text-muted">
                                    <?= htmlspecialchars($opportunity->notice_type) ?>
                                    <?php if ($opportunity->description_status === 'missing'): ?>
                                    <span class="badge bg-warning text-dark">No Description</span>
                                    <?php endif; ?>
                                </small>
                            </div>
                        </td>
                        <td>
                            <?= htmlspecialchars($opportunity->agency->name ?? 'Unknown') ?>
                        </td>
                        <td>
                            <?php if ($opportunity->due_at): ?>
                                <?php
                                $dueDate = new DateTime($opportunity->due_at);
                                $now = new DateTime();
                                $daysUntilDue = $now->diff($dueDate)->days;
                                $isOverdue = $dueDate < $now;
                                $isDueSoon = $daysUntilDue <= 7 && !$isOverdue;
                                ?>
                                <span class="<?= $isOverdue ? 'text-danger' : ($isDueSoon ? 'text-warning' : '') ?>">
                                    <?= $dueDate->format('M j, Y') ?>
                                </span>
                                <?php if ($isDueSoon || $isOverdue): ?>
                                <br><small class="text-muted">
                                    <?= $isOverdue ? 'Overdue' : $daysUntilDue . ' days' ?>
                                </small>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">No due date</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $scoreClass = 'score-low';
                            if ($opportunity->score >= 70) $scoreClass = 'score-high';
                            elseif ($opportunity->score >= 40) $scoreClass = 'score-medium';
                            ?>
                            <span class="badge badge-score <?= $scoreClass ?>">
                                <?= $opportunity->score ?>
                            </span>
                        </td>
                        <td>
                            <?php
                            $statusClass = 'secondary';
                            switch ($opportunity->status) {
                                case 'New': $statusClass = 'primary'; break;
                                case 'Review': $statusClass = 'info'; break;
                                case 'Pursue': $statusClass = 'success'; break;
                                case 'No-Bid': $statusClass = 'danger'; break;
                                case 'Awarded': $statusClass = 'success'; break;
                                case 'Lost': $statusClass = 'warning'; break;
                            }
                            ?>
                            <span class="badge bg-<?= $statusClass ?>"><?= $opportunity->status ?></span>
                        </td>
                        <td>
                            <?php if ($opportunity->set_aside): ?>
                                <span class="badge bg-light text-dark"><?= htmlspecialchars($opportunity->set_aside) ?></span>
                            <?php else: ?>
                                <span class="text-muted">None</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="/opportunities/<?= $opportunity->id ?>" class="btn btn-sm btn-outline-primary">View</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (empty($opportunities)): ?>
        <div class="text-center py-5">
            <p class="text-muted">No opportunities found matching your criteria.</p>
            <a href="/opportunities" class="btn btn-primary">View All Opportunities</a>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Pagination -->
<?php if ($pagination['total_pages'] > 1): ?>
<nav class="mt-4">
    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
        <li class="page-item <?= $i === $pagination['page'] ? 'active' : '' ?>">
            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
        </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<script>
function triggerSync() {
    if (confirm('This will fetch new opportunities from SAM.gov. Continue?')) {
        fetch('/api/opportunities/sync', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: 'csrf_token=' + encodeURIComponent(document.querySelector('input[name="csrf_token"]')?.value || '')
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Sync completed successfully!');
                location.reload();
            } else {
                alert('Sync failed: ' + data.error);
            }
        })
        .catch(error => {
            alert('Sync failed: ' + error.message);
        });
    }
}
</script>

<?php
$content = ob_get_clean();
include dirname(__DIR__) . '/layouts/app.php';
?>