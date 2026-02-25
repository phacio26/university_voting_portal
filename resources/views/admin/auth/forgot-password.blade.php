<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --auth-ink: #0f172a;
            --auth-border: #bcc7d6;
            --auth-focus: #2e8bc0;
            --auth-card-bg: rgba(238, 239, 241, 0.96);
            --auth-input-bg: #dbe3ef;
            --auth-btn: #3b8ebd;
            --auth-btn-hover: #327ba4;
            --back-btn: #ee9d0f;
            --back-btn-hover: #cf8808;
        }

        * {
            font-family: "Manrope", sans-serif;
        }

        html,
        body {
            min-height: 100%;
        }

        body {
            position: relative;
            margin: 0;
            min-height: 100svh;
            overflow-x: hidden;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            color: var(--auth-ink);
            background-image:
                radial-gradient(1000px 460px at 8% -8%, rgba(14, 165, 161, 0.32), transparent 60%),
                radial-gradient(920px 420px at 92% 108%, rgba(59, 130, 246, 0.25), transparent 62%),
                repeating-linear-gradient(135deg, rgba(255, 255, 255, 0.06) 0 12px, rgba(255, 255, 255, 0) 12px 24px),
                linear-gradient(140deg, #0b2f53 0%, #0e4b74 48%, #0f766e 100%);
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-attachment: scroll;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                radial-gradient(1000px 460px at 8% -8%, rgba(14, 165, 161, 0.32), transparent 60%),
                radial-gradient(920px 420px at 92% 108%, rgba(59, 130, 246, 0.25), transparent 62%),
                repeating-linear-gradient(135deg, rgba(255, 255, 255, 0.06) 0 12px, rgba(255, 255, 255, 0) 12px 24px),
                linear-gradient(140deg, #0b2f53 0%, #0e4b74 48%, #0f766e 100%);
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            transform: translateZ(0);
            will-change: transform;
            z-index: -1;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(760px 340px at 15% 20%, rgba(255, 255, 255, 0.2), transparent 68%),
                radial-gradient(820px 380px at 85% 78%, rgba(148, 230, 224, 0.2), transparent 70%);
            opacity: 0.55;
            animation: bgFade 8s ease-in-out infinite alternate;
        }

        .auth-container {
            position: relative;
            z-index: 1;
            min-height: 100svh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(0.5rem, 2vh, 1.2rem);
        }

        @keyframes bgFade {
            0% { opacity: 0.35; }
            100% { opacity: 0.7; }
        }

        .auth-card {
            width: min(100%, 417px);
            border: 0;
            border-radius: 0;
            background: var(--auth-card-bg);
            box-shadow: 0 14px 34px rgba(2, 13, 30, 0.32);
            padding: 1rem 1.08rem 1.1rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .auth-step {
            text-align: center;
            font-size: 1.05rem;
            font-weight: 600;
            color: #34465a;
            margin-bottom: 0.72rem;
        }

        .auth-note {
            margin-bottom: 0.65rem;
            text-align: center;
            font-size: 0.82rem;
            color: #5a6879;
            line-height: 1.35;
        }

        .input-group-text {
            border-radius: 0;
            border: 1px solid var(--auth-border);
            border-left: 0;
            color: #5b6776;
            background: #edf1f7;
            width: 36px;
            justify-content: center;
        }

        .form-control {
            border: 1px solid var(--auth-border);
            border-radius: 0;
            padding: 0.62rem 0.68rem;
            font-size: 0.9rem;
            color: #0f172a;
            background: var(--auth-input-bg);
        }

        .form-control::placeholder {
            color: #7b8797;
            opacity: 1;
        }

        .form-control:focus {
            border-color: var(--auth-focus);
            background: #ecf3fb;
            box-shadow: 0 0 0 0.17rem rgba(46, 139, 192, 0.14);
        }

        .form-control:focus + .input-group-text {
            border-color: var(--auth-focus);
            background: #edf5fe;
        }

        .btn-row {
            display: flex;
            gap: 0.68rem;
            margin-top: 0.25rem;
        }

        .btn-step {
            flex: 1;
            border: 0;
            border-radius: 0;
            padding: 0.58rem 0.84rem;
            font-size: 0.9rem;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
            text-align: center;
        }

        .btn-send {
            background: var(--auth-btn);
        }

        .btn-send:hover {
            color: #fff;
            background: var(--auth-btn-hover);
        }

        .btn-back {
            background: var(--back-btn);
        }

        .btn-back:hover {
            color: #fff;
            background: var(--back-btn-hover);
        }

        .auth-card .alert {
            font-size: 0.78rem;
            border-radius: 2px;
            margin-bottom: 0.52rem;
            padding: 0.45rem 0.58rem;
            line-height: 1.28;
        }

        .reset-link {
            font-size: 0.74rem;
            word-break: break-all;
        }

        @media (max-height: 760px) {
            .auth-card {
                padding: 0.75rem 0.84rem 0.85rem;
            }

            .auth-step {
                font-size: 0.98rem;
                margin-bottom: 0.5rem;
            }

            .auth-note {
                font-size: 0.78rem;
                margin-bottom: 0.52rem;
            }

            .mb-3 {
                margin-bottom: 0.45rem !important;
            }

            .form-control {
                font-size: 0.84rem;
                padding: 0.5rem 0.55rem;
            }

            .input-group-text {
                width: 32px;
                font-size: 0.8rem;
            }

            .btn-step {
                font-size: 0.84rem;
                padding: 0.48rem 0.68rem;
            }
        }

        @media (max-height: 680px) {
            .auth-card {
                padding: 0.58rem 0.68rem 0.66rem;
            }

            .auth-step {
                font-size: 0.86rem;
                margin-bottom: 0.38rem;
            }

            .auth-note {
                font-size: 0.72rem;
                margin-bottom: 0.42rem;
                line-height: 1.22;
            }

            .mb-3 {
                margin-bottom: 0.34rem !important;
            }

            .form-control {
                font-size: 0.78rem;
                padding: 0.42rem 0.46rem;
            }

            .input-group-text {
                width: 30px;
                font-size: 0.72rem;
            }

            .btn-row {
                gap: 0.46rem;
            }

            .btn-step {
                font-size: 0.76rem;
                padding: 0.42rem 0.52rem;
            }

            .auth-card .alert {
                font-size: 0.72rem;
                padding: 0.34rem 0.4rem;
            }
        }
        @media (max-width: 768px) {
            .auth-card {
                width: min(100%, 420px);
                padding: 1.05rem 1rem 1.15rem;
            }

            .auth-step {
                font-size: 1rem;
                margin-bottom: 0.72rem;
            }

            .auth-note {
                font-size: 0.9rem;
                margin-bottom: 0.72rem;
            }

            .form-control {
                font-size: 1rem;
                padding: 0.62rem 0.68rem;
            }

            .input-group-text {
                width: 42px;
                font-size: 0.9rem;
            }

            .btn-step {
                min-height: 44px;
                font-size: 0.95rem;
                padding: 0.68rem 0.9rem;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .auth-card .alert {
                font-size: 0.86rem;
                padding: 0.55rem 0.65rem;
            }
        }

        @media (max-width: 991px) {
            body {
                background-attachment: scroll;
            }
        }

        @media (max-width: 576px) {
            .auth-container {
                align-items: flex-start;
                padding: max(0.75rem, env(safe-area-inset-top)) 0.75rem max(0.75rem, env(safe-area-inset-bottom));
            }

            .auth-card {
                width: 100%;
                margin-top: 0.4rem;
            }

            .btn-row {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-step">Reset Admin Password</div>
            <p class="auth-note">Enter your admin email to receive a reset link.</p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @if (session('reset_url'))
                <div class="alert alert-warning">
                    <div class="mb-1">Temporary reset link:</div>
                    <a href="{{ session('reset_url') }}" class="reset-link">{{ session('reset_url') }}</a>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.password.email') }}">
                @csrf

                <div class="mb-3">
                    <div class="input-group">
                        <input
                            type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            placeholder="Email address"
                        >
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    </div>
                    @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="btn-row">
                    <button type="submit" class="btn-step btn-send">Send Link</button>
                    <a href="{{ route('admin.login') }}" class="btn-step btn-back">Back</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

