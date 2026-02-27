<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel - University Voting')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    @yield('styles')
</head>
<body class="admin-body">
    <div id="pageNavLoader" class="page-nav-loader" aria-hidden="true">
        <div class="page-nav-loader__bar"></div>
        <div class="page-nav-loader__label">
            <i class="fas fa-circle-notch fa-spin"></i>
            <span>Preparing dashboard...</span>
        </div>
    </div>

    <div class="admin-container">
        <!-- Sidebar -->
        <nav class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-header">
                <div class="brand-logo">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h5>Election Control</h5>
            </div>
            
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                       href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-home"></i>
                        <span>Overview</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.positions.*') ? 'active' : '' }}" 
                       href="{{ route('admin.positions.index') }}">
                        <i class="fas fa-bullseye"></i>
                        <span>Positions</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.election-periods.*') ? 'active' : '' }}" 
                       href="{{ route('admin.election-periods.index') }}">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Election Periods</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.candidates.*') ? 'active' : '' }}" 
                       href="{{ route('admin.candidates.index') }}">
                        <i class="fas fa-user-graduate"></i>
                        <span>Candidates</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.results.*') ? 'active' : '' }}"
                       href="{{ route('admin.results.index') }}">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Results</span>
                    </a>
                </li>
                <li class="nav-divider"></li>
                <li class="nav-item">
                    <form method="POST" action="{{ route('admin.logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="nav-link text-danger">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Top Bar -->
            <header class="admin-topbar">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-12 col-lg">
                            <button class="mobile-menu-toggle me-3" id="mobileMenuToggle" type="button" aria-label="Toggle navigation menu">
                                <i class="fas fa-bars"></i>
                            </button>
                            <h4 class="page-title mb-0 d-inline-block">
                                <i class="fas @yield('page-icon', 'fa-tachometer-alt') me-2"></i>
                                @yield('page-title', 'Dashboard')
                            </h4>
                        </div>
                        <div class="col-12 col-lg-auto">
                            <div class="topbar-info">
                                <i class="fas fa-clock me-1"></i>
                                <span id="current-time"></span>
                                <small class="text-muted ms-1">(Malawi Time)</small>
                                <span class="mx-2">|</span>
                                <i class="fas fa-id-card me-1"></i>
                                <span>{{ Auth::guard('admin')->user()->name }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <div class="admin-content">
                <div class="container-fluid py-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-double me-1"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </main>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="{{ asset('js/app.js') }}"></script>
    
    <script>
        // Update current time
        function updateTime() {
            const now = new Date();
            const options = { 
                timeZone: 'Africa/Blantyre',
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            document.getElementById('current-time').textContent = 
                now.toLocaleString('en-US', options);
        }
        setInterval(updateTime, 1000);
        updateTime();

        // Mobile menu toggle
        document.getElementById('mobileMenuToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('adminSidebar');
            sidebar.classList.toggle('show');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('adminSidebar');
            const toggleBtn = document.getElementById('mobileMenuToggle');
            
            if (window.innerWidth <= 991 && 
                !sidebar.contains(event.target) && 
                !toggleBtn.contains(event.target) &&
                sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
            }
        });

        // Auto-dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    if (alert && alert.parentNode) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                }, 5000);
            });
        });

        (function () {
            const loader = document.getElementById('pageNavLoader');
            if (!loader) return;

            let hideFallbackTimer = null;
            const showLoader = () => {
                loader.classList.add('is-visible');
                if (hideFallbackTimer) clearTimeout(hideFallbackTimer);
                hideFallbackTimer = setTimeout(() => loader.classList.remove('is-visible'), 4000);
            };
            const hideLoader = () => loader.classList.remove('is-visible');

            window.addEventListener('beforeunload', showLoader);
            window.addEventListener('pageshow', hideLoader);

            document.addEventListener('click', (event) => {
                const anchor = event.target.closest('a');
                if (!anchor) return;
                const href = anchor.getAttribute('href');
                if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
                if (anchor.target && anchor.target !== '_self') return;
                if (anchor.hasAttribute('download')) return;
                showLoader();
            });

            document.addEventListener('submit', (event) => {
                const form = event.target;
                if (!(form instanceof HTMLFormElement)) return;
                if (form.dataset.ajax === 'true' || form.classList.contains('js-no-nav-loader')) return;
                if (form.target && form.target !== '_self') return;
                setTimeout(() => {
                    if (!event.defaultPrevented) {
                        showLoader();
                    }
                }, 0);
            });
        })();
    </script>
    
    @yield('scripts')
</body>
</html>
