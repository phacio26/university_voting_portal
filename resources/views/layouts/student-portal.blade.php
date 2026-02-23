<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'University Voting Portal')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --portal-bg: #f3f6fb;
            --portal-ink: #0b1220;
            --portal-muted: #334155;
            --portal-brand: #0a4d91;
            --portal-brand-2: #0f766e;
            --portal-card: #ffffff;
            --portal-border: #d9e2ef;
            --portal-success: #198754;
        }

        body {
            font-family: "Manrope", sans-serif;
            color: var(--portal-ink);
            background:
                radial-gradient(1200px 400px at 100% -10%, rgba(15, 118, 110, 0.12), transparent 60%),
                radial-gradient(1000px 360px at 0% -10%, rgba(10, 77, 145, 0.12), transparent 60%),
                var(--portal-bg);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
        }

        .portal-navbar {
            background: linear-gradient(92deg, #0a4d91, #0f766e);
            box-shadow: 0 12px 30px rgba(10, 77, 145, 0.25);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
        }

        .portal-shell {
            max-width: 1140px;
            margin: 0 auto;
            padding: 5rem 0.75rem 2rem;
        }

        .portal-card {
            border: 1px solid var(--portal-border);
            border-radius: 16px;
            background: var(--portal-card);
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            transition: transform 0.24s ease, box-shadow 0.24s ease, border-color 0.24s ease;
        }

        .portal-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 28px rgba(15, 23, 42, 0.1);
            border-color: #c9d8ec;
        }

        .portal-heading {
            font-weight: 800;
            letter-spacing: -0.01em;
            color: #0a1528;
        }

        .portal-muted {
            color: var(--portal-muted);
            font-weight: 600;
        }

        .portal-navbar .navbar-brand {
            letter-spacing: -0.01em;
            transition: opacity 0.2s ease;
            color: #ffffff !important;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.25);
        }

        .portal-navbar .navbar-brand:hover {
            opacity: 0.92;
        }

        .portal-navbar .nav-link {
            border-radius: 10px;
            padding: 0.45rem 0.75rem !important;
            transition: background-color 0.2s ease, color 0.2s ease, transform 0.2s ease;
            color: rgba(255, 255, 255, 0.97) !important;
            font-weight: 700;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.22);
        }

        .portal-navbar .nav-link:hover {
            background: rgba(255, 255, 255, 0.16);
            transform: translateY(-1px);
        }

        .portal-navbar .nav-link.active {
            background: rgba(255, 255, 255, 0.22);
            color: #fff !important;
            font-weight: 700;
        }

        .portal-navbar .portal-logout-link {
            border-radius: 10px;
            padding: 0.45rem 0.75rem !important;
            color: rgba(255, 255, 255, 0.97) !important;
            font-weight: 700;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.22);
            border: 1px solid rgba(255, 255, 255, 0.35);
            background: rgba(255, 255, 255, 0.12);
            transition: background-color 0.2s ease, color 0.2s ease, transform 0.2s ease;
        }

        .portal-navbar .portal-logout-link:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-1px);
        }

        .portal-shell,
        .portal-shell p,
        .portal-shell div,
        .portal-shell span,
        .portal-shell li {
            color: #0f172a;
        }

        .portal-shell h1,
        .portal-shell h2,
        .portal-shell h3,
        .portal-shell h4,
        .portal-shell h5,
        .portal-shell h6 {
            color: #0a1528;
            font-weight: 800;
        }

        .portal-shell td,
        .portal-shell th,
        .portal-shell label,
        .portal-shell .form-label {
            color: #0f172a;
        }

        .portal-shell small,
        .portal-shell .small,
        .portal-shell .form-text {
            color: #152b42 !important;
            opacity: 1;
            font-weight: 700;
            letter-spacing: 0.01em;
        }

        .portal-shell .alert {
            color: #0f172a;
            font-weight: 600;
        }

        .portal-shell .btn-outline-secondary {
            color: #243447;
            border-color: #8fa2ba;
            font-weight: 700;
            background: #ffffff;
        }

        .portal-shell .btn-outline-secondary:hover {
            color: #ffffff;
            background: #334155;
            border-color: #334155;
        }

        .portal-shell .btn-outline-primary {
            color: #0a3f78;
            border-color: #0a4d91;
            background: #ffffff;
            font-weight: 700;
        }

        .portal-shell .btn-outline-primary:hover {
            color: #ffffff;
            background: #0a4d91;
            border-color: #0a4d91;
        }

        .portal-shell .form-control,
        .portal-shell .form-select {
            color: #0f172a;
            font-weight: 600;
        }

        .portal-shell .form-control::placeholder,
        .portal-shell textarea::placeholder {
            color: #5b6780;
            opacity: 1;
        }

        .portal-shell .text-muted,
        .portal-shell small.text-muted,
        .portal-shell .small.text-muted,
        .portal-shell .card-title.text-muted {
            color: #152b42 !important;
            opacity: 1;
            font-weight: 700;
            letter-spacing: 0.01em;
        }

        .portal-shell .btn {
            transition: transform 0.2s ease, box-shadow 0.22s ease, filter 0.2s ease;
            font-weight: 700;
        }

        .portal-shell .btn-sm {
            font-size: 0.92rem;
            min-height: 2.3rem;
            padding: 0.4rem 0.72rem;
        }

        .portal-shell .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.12);
            filter: saturate(1.03);
        }

        .portal-shell .btn:active {
            transform: translateY(0);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.1);
        }

        .fx-fade-up {
            opacity: 0;
            transform: translateY(14px);
            transition: opacity 0.45s ease, transform 0.45s ease;
            will-change: opacity, transform;
        }

        .fx-fade-up.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        .avatar-img {
            width: 56px;
            height: 56px;
            border-radius: 999px;
            object-fit: cover;
            border: 2px solid #dbe7f5;
            filter: saturate(1.12) contrast(1.08) brightness(1.03);
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.18);
        }

        .avatar-fallback {
            width: 56px;
            height: 56px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #e6eef8;
            color: #2f4f73;
            font-weight: 700;
            font-size: 0.92rem;
        }

        .portal-shell .results-avatar,
        .portal-shell .candidate-photo,
        .portal-shell .photo-preview {
            filter: saturate(1.14) contrast(1.08) brightness(1.03);
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.16);
            transition: transform 0.25s ease, filter 0.25s ease;
        }

        .portal-shell .results-avatar:hover,
        .portal-shell .candidate-photo:hover {
            transform: scale(1.02);
            filter: saturate(1.2) contrast(1.12) brightness(1.05);
        }

        .page-nav-loader {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 2000;
            opacity: 0;
            transition: opacity 0.15s ease-in-out;
        }

        .page-nav-loader.is-visible {
            opacity: 1;
        }

        .page-nav-loader__bar {
            position: absolute;
            top: 0;
            left: 0;
            width: 55%;
            height: 4px;
            background: linear-gradient(90deg, var(--portal-brand), var(--portal-brand-2));
            animation: navLoaderSlide 1s ease-in-out infinite;
            box-shadow: 0 2px 10px rgba(10, 77, 145, 0.3);
        }

        .page-nav-loader__label {
            position: absolute;
            top: 12px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(15, 23, 42, 0.92);
            color: #fff;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            padding: 0.4rem 0.8rem;
            font-size: 0.78rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            box-shadow: 0 10px 26px rgba(15, 23, 42, 0.28);
        }

        .portal-footer {
            margin-top: 2rem;
            border-top: 1px solid var(--portal-border);
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(4px);
        }

        .portal-footer .footer-inner {
            max-width: 1140px;
            margin: 0 auto;
            padding: 0.9rem 0.75rem;
            color: var(--portal-muted);
            font-size: 0.9rem;
            text-align: center;
        }

        @keyframes navLoaderSlide {
            0% { transform: translateX(-120%); }
            100% { transform: translateX(260%); }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation: none !important;
                transition: none !important;
                scroll-behavior: auto !important;
            }
        }

        @media (max-width: 991.98px) {
            .portal-navbar .navbar-collapse .navbar-nav {
                align-items: stretch;
            }

            .portal-navbar .navbar-collapse .nav-item {
                width: 100%;
            }

            .portal-navbar .navbar-collapse .nav-link,
            .portal-navbar .navbar-collapse .portal-logout-link {
                display: block;
                width: 100%;
                text-align: left;
            }

            .portal-navbar .navbar-collapse .portal-logout-link {
                margin-top: 0.15rem;
            }
        }

        @media (max-width: 576px) {
            body {
                font-size: 0.94rem;
                font-weight: 500;
            }

            .portal-shell {
                padding: 4.6rem 0.65rem 1.2rem;
            }

            .portal-navbar .navbar-brand {
                font-size: 0.98rem;
                font-weight: 700;
            }

            .portal-navbar .nav-link,
            .portal-navbar .portal-logout-link {
                font-size: 0.9rem;
                font-weight: 700;
            }

            .portal-shell h1 { font-size: 1.28rem; font-weight: 800; }
            .portal-shell h2 { font-size: 1.16rem; font-weight: 800; }
            .portal-shell h3 { font-size: 1.04rem; font-weight: 700; }
            .portal-shell h4 { font-size: 0.98rem; font-weight: 700; }
            .portal-shell h5,
            .portal-shell h6 { font-size: 0.93rem; font-weight: 700; }

            .portal-shell p,
            .portal-shell li,
            .portal-shell span,
            .portal-muted {
                font-size: 0.9rem;
                font-weight: 600;
            }

            .portal-shell small,
            .portal-shell .small,
            .portal-shell .form-text {
                font-size: 0.8rem;
                font-weight: 600;
            }

            .portal-shell .btn {
                font-size: 0.88rem;
                font-weight: 700;
                min-height: 2.15rem;
                padding: 0.38rem 0.62rem;
            }

            .portal-shell .btn-sm {
                font-size: 0.84rem;
                min-height: 2rem;
                padding: 0.34rem 0.56rem;
            }

            .portal-shell .form-control,
            .portal-shell .form-select {
                font-size: 0.9rem;
                font-weight: 600;
            }
        }

    </style>
    @stack('styles')
</head>
<body>
    <div id="pageNavLoader" class="page-nav-loader" aria-hidden="true">
        <div class="page-nav-loader__bar"></div>
        <div class="page-nav-loader__label">
            <i class="fas fa-circle-notch fa-spin"></i>
            <span>Loading page...</span>
        </div>
    </div>

    @php($studentUser = auth('student')->user())
    <nav class="navbar navbar-expand-lg navbar-dark portal-navbar">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('student.dashboard') }}">University Voting Portal</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#portalNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="portalNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" href="{{ route('student.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('student.results.index') || request()->routeIs('student.results.show') || request()->routeIs('student.results.position') ? 'active' : '' }}" href="{{ route('student.results.index') }}">Results</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('student.results.history') ? 'active' : '' }}" href="{{ route('student.results.history') }}">History</a>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <form method="POST" action="{{ route('student.logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="nav-link portal-logout-link border-0">
                                Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="portal-shell">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm js-auto-dismiss">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm js-auto-dismiss">{{ session('error') }}</div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning border-0 shadow-sm js-auto-dismiss">{{ session('warning') }}</div>
        @endif
        @if(session('info'))
            <div class="alert alert-info border-0 shadow-sm js-auto-dismiss">{{ session('info') }}</div>
        @endif

        @yield('content')
    </main>

    <footer class="portal-footer">
        <div class="footer-inner">
            &copy; <span id="portalYear">{{ date('Y') }}</span> University Voting Portal. All rights reserved.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            const yearEl = document.getElementById('portalYear');
            if (yearEl) {
                yearEl.textContent = String(new Date().getFullYear());
            }
        })();

        document.addEventListener('DOMContentLoaded', () => {
            const revealNodes = document.querySelectorAll('main.portal-shell > *');
            revealNodes.forEach((node, index) => {
                node.classList.add('fx-fade-up');
                node.style.transitionDelay = `${Math.min(index * 45, 260)}ms`;
            });

            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries, obs) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                            obs.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.05, rootMargin: '0px 0px -8% 0px' });

                revealNodes.forEach((node) => observer.observe(node));
            } else {
                revealNodes.forEach((node) => node.classList.add('is-visible'));
            }
        });

        setTimeout(() => {
            document.querySelectorAll('.js-auto-dismiss').forEach((alert) => {
                alert.classList.add('fade');
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 4000);

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
    @stack('scripts')
</body>
</html>

