<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars($csrf_token) ?>">
    <title><?= htmlspecialchars($title ?? 'GovTribe Platform') ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            border-radius: 0.375rem;
            margin: 0.125rem 0;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .opportunity-card {
            transition: transform 0.2s ease-in-out;
        }
        .opportunity-card:hover {
            transform: translateY(-2px);
        }
        .score-badge {
            font-weight: 600;
        }
        .score-high { background-color: #d4edda; color: #155724; }
        .score-medium { background-color: #fff3cd; color: #856404; }
        .score-low { background-color: #f8d7da; color: #721c24; }
        .status-new { background-color: #cce5ff; color: #004085; }
        .status-review { background-color: #fff3cd; color: #856404; }
        .status-pursue { background-color: #d4edda; color: #155724; }
        .status-no-bid { background-color: #f8d7da; color: #721c24; }
        .status-awarded { background-color: #d1ecf1; color: #0c5460; }
        .status-lost { background-color: #e2e3e5; color: #383d41; }
        .navbar-brand img {
            height: 32px;
        }
        .main-content {
            min-height: calc(100vh - 76px);
        }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="/dashboard">
                <i class="bi bi-shield-check me-2"></i>
                <strong>GovTribe Platform</strong>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($user) && $user): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i>
                                <?= htmlspecialchars($user->getFullName()) ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/profile">
                                    <i class="bi bi-person me-2"></i>Profile
                                </a></li>
                                <?php if ($user->getRole() === 'Admin'): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="/admin">
                                        <i class="bi bi-gear me-2"></i>Administration
                                    </a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="/logout" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <?php if (isset($user) && $user): ?>
                <!-- Sidebar Navigation -->
                <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                    <div class="position-sticky pt-3">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="/dashboard">
                                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/opportunities">
                                    <i class="bi bi-search me-2"></i>Opportunities
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/proposals">
                                    <i class="bi bi-file-text me-2"></i>Proposals
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/suppliers">
                                    <i class="bi bi-building me-2"></i>Suppliers
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/rfqs">
                                    <i class="bi bi-clipboard-check me-2"></i>RFQs & Quotes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/awards">
                                    <i class="bi bi-trophy me-2"></i>Awards & Invoices
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/reports">
                                    <i class="bi bi-graph-up me-2"></i>Reports
                                </a>
                            </li>
                            <?php if ($user->getRole() === 'Admin'): ?>
                                <li class="nav-item mt-3">
                                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-light">
                                        <span>Administration</span>
                                    </h6>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/admin">
                                        <i class="bi bi-gear me-2"></i>Settings
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/admin/users">
                                        <i class="bi bi-people me-2"></i>Users
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/admin/ingestion">
                                        <i class="bi bi-arrow-down-circle me-2"></i>Data Ingestion
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/admin/audit">
                                        <i class="bi bi-journal-text me-2"></i>Audit Log
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </nav>

                <!-- Main Content -->
                <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
            <?php else: ?>
                <!-- Full Width Content for Login -->
                <main class="col-12 main-content">
            <?php endif; ?>
            
            <!-- Flash Messages -->
            <?php if (isset($flash_messages) && !empty($flash_messages)): ?>
                <div class="mt-3">
                    <?php foreach ($flash_messages as $message): ?>
                        <div class="alert alert-<?= $message['type'] === 'error' ? 'danger' : $message['type'] ?> alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($message['message']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Page Content -->
            <?= $content ?? '' ?>
            
            </main>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Set CSRF token for all AJAX requests
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Add CSRF token to all forms
            document.querySelectorAll('form').forEach(form => {
                if (!form.querySelector('input[name="csrf_token"]')) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'csrf_token';
                    input.value = csrfToken;
                    form.appendChild(input);
                }
            });

            // Set up AJAX defaults
            if (typeof jQuery !== 'undefined') {
                jQuery.ajaxSetup({
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                    }
                });
            }

            // Active nav link highlighting
            const currentPath = window.location.pathname;
            document.querySelectorAll('.sidebar .nav-link').forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });
        });

        // Utility functions
        function formatCurrency(amount) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD'
            }).format(amount);
        }

        function formatDate(dateString) {
            return new Intl.DateTimeFormat('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            }).format(new Date(dateString));
        }

        function showToast(message, type = 'success') {
            // Create toast element
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-bg-${type} border-0`;
            toast.setAttribute('role', 'alert');
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;

            // Add to toast container (create if doesn't exist)
            let container = document.querySelector('.toast-container');
            if (!container) {
                container = document.createElement('div');
                container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
                document.body.appendChild(container);
            }
            container.appendChild(toast);

            // Show toast
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();

            // Remove from DOM after hidden
            toast.addEventListener('hidden.bs.toast', () => {
                toast.remove();
            });
        }
    </script>
    
    <?= $additional_js ?? '' ?>
</body>
</html>