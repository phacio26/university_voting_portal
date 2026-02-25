<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Student Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
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

        .auth-container {
            min-height: 100svh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(0.5rem, 2vh, 1.2rem);
        }

        .auth-card {
            width: min(100%, 320px);
            border: 0;
            border-radius: 0;
            background: var(--auth-card-bg);
            box-shadow: 0 14px 34px rgba(2, 13, 30, 0.32);
            padding: 0.82rem 0.78rem 0.86rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .auth-logo-wrap {
            text-align: center;
            margin-bottom: 0.6rem;
        }

        .auth-logo {
            width: 82px;
            max-width: 100%;
            height: auto;
            display: inline-block;
        }

        .auth-step {
            text-align: center;
            font-size: 0.86rem;
            color: #334155;
            margin-bottom: 0.65rem;
            font-weight: 700;
        }

        .input-group-text {
            border-radius: 0;
            border: 1px solid var(--auth-border);
            border-left: 0;
            color: #4b5a6c;
            background: #edf1f7;
            width: 36px;
            justify-content: center;
        }

        .form-control {
            border: 1px solid var(--auth-border);
            border-radius: 0;
            padding: 0.5rem 0.54rem;
            font-size: 0.88rem;
            color: #0f172a;
            background: var(--auth-input-bg);
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

        .form-control[readonly] {
            opacity: 0.92;
            cursor: not-allowed;
        }

        .btn-auth {
            border: 0;
            border-radius: 2px;
            padding: 0.54rem 0.84rem;
            font-size: 0.88rem;
            font-weight: 700;
            background: var(--auth-btn);
            color: #fff;
            line-height: 1;
        }

        .btn-auth:hover {
            color: #fff;
            background: var(--auth-btn-hover);
        }

        .btn-auth .btn-text {
            font-size: 0.88rem;
            vertical-align: middle;
        }

        .btn-back {
            display: block;
            width: 100%;
            border: 0;
            border-radius: 2px;
            padding: 0.54rem 0.84rem;
            font-size: 0.88rem;
            font-weight: 700;
            background: var(--back-btn);
            color: #fff;
            line-height: 1;
            text-decoration: none;
            text-align: center;
            margin-bottom: 0.62rem;
        }

        .btn-back:hover {
            color: #fff;
            background: var(--back-btn-hover);
        }

        .auth-card .alert {
            font-size: 0.8rem;
            border-radius: 2px;
            margin-bottom: 0.65rem;
        }

        .form-note {
            font-size: 0.78rem;
            color: #475569;
            margin-top: 0.2rem;
            margin-bottom: 0.62rem;
        }

        @media (max-width: 991px) {
            body {
                background-attachment: scroll;
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

            .form-control {
                font-size: 1rem;
                padding: 0.62rem 0.68rem;
            }

            .input-group-text {
                width: 42px;
                font-size: 0.9rem;
            }

            .btn-auth,
            .btn-back {
                min-height: 44px;
                font-size: 0.95rem;
                padding: 0.68rem 0.9rem;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .form-note {
                font-size: 0.9rem;
                margin-bottom: 0.72rem;
            }

            .auth-card .alert {
                font-size: 0.86rem;
                padding: 0.55rem 0.65rem;
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

            .auth-logo {
                width: 96px;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-logo-wrap">
                <img class="auth-logo" src="{{ asset('storage/images/favicon.png') }}" alt="University logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
                <i class="fas fa-vote-yea" style="display:none;font-size:3.2rem;color:#163e65;"></i>
            </div>

            <div class="auth-step">Reset your account password</div>

            @if ($errors->any())
                <div class="alert alert-danger mb-3">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('student.password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-3">
                    <div class="input-group">
                        <input
                            type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            id="email"
                            name="email"
                            value="{{ old('email', $email) }}"
                            required
                            readonly
                            placeholder="Email address"
                        >
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    </div>
                    @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <div class="input-group">
                        <input
                            type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            id="password"
                            name="password"
                            required
                            placeholder="New password"
                            minlength="8"
                            maxlength="8"
                            pattern="[A-Za-z\d]{8}"
                            title="Password must be exactly 8 characters long with letters and numbers only"
                        >
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <div class="password-strength mt-2">
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar" id="password-strength-bar" role="progressbar" style="width: 0%;"></div>
                        </div>
                        <small class="text-muted" id="password-strength-text">Password strength:</small>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="input-group">
                        <input
                            type="password"
                            class="form-control @error('password_confirmation') is-invalid @enderror"
                            id="password_confirmation"
                            name="password_confirmation"
                            required
                            placeholder="Confirm password"
                        >
                        <span class="input-group-text"><i class="fas fa-check"></i></span>
                    </div>
                    @error('password_confirmation')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-note">Use at least 8 characters with letters and numbers.</div>

                <button type="submit" class="btn btn-auth w-100 mb-3" aria-label="Reset Password">
                    <span class="btn-text"><i class="fas fa-rotate-right me-1"></i>Reset Password</span>
                </button>

                <a href="{{ route('student.login') }}" class="btn-back">
                    <i class="fas fa-arrow-left me-1"></i>Back to Login
                </a>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const strengthBar = document.getElementById('password-strength-bar');
            const strengthText = document.getElementById('password-strength-text');

            function checkPasswordStrength(password) {
                let strength = 0;

                if (password.length === 8) strength++;
                if (/[A-Za-z]/.test(password)) strength++;
                if (/\d/.test(password)) strength++;

                const strengthLevels = {
                    0: { width: '0%', text: 'Invalid', class: 'bg-danger' },
                    1: { width: '33%', text: 'Weak', class: 'bg-warning' },
                    2: { width: '67%', text: 'Good', class: 'bg-info' },
                    3: { width: '100%', text: 'Strong', class: 'bg-success' }
                };

                const level = strengthLevels[strength] || strengthLevels[0];
                
                strengthBar.style.width = level.width;
                strengthBar.className = 'progress-bar ' + level.class;
                strengthText.textContent = 'Password strength: ' + level.text;
                strengthText.className = level.class === 'bg-danger' || level.class === 'bg-warning' ? 'text-danger' : 
                                       level.class === 'bg-info' ? 'text-info' : 'text-success';
            }

            passwordInput.addEventListener('input', function() {
                checkPasswordStrength(this.value);
            });
        });
    </script>
</body>
</html>
