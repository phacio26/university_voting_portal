<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
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
            background-attachment: scroll;
        }

        .auth-container {
            min-height: 100svh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(0.5rem, 2vh, 1.2rem);
        }

        .auth-card {
            width: min(100%, 342px);
            border: 0;
            border-radius: 0;
            background: var(--auth-card-bg);
            box-shadow: 0 14px 34px rgba(2, 13, 30, 0.32);
            padding: 0.95rem 0.9rem 0.98rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .auth-logo-wrap {
            text-align: center;
            margin-bottom: 0.6rem;
        }

        .auth-logo {
            width: 92px;
            max-width: 100%;
            height: auto;
            display: inline-block;
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
            padding: 0.58rem 0.62rem;
            font-size: 0.92rem;
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

        .forgot-password-link {
            color: #2d79a7;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.92rem;
        }

        .forgot-password-link:hover {
            color: #205f86;
            text-decoration: underline;
        }

        .btn-auth {
            border: 0;
            border-radius: 2px;
            padding: 0.62rem 0.92rem;
            font-size: 0.92rem;
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
            font-size: 0.92rem;
            vertical-align: middle;
        }

        .btn-register {
            display: block;
            width: 100%;
            border: 0;
            border-radius: 2px;
            padding: 0.62rem 0.92rem;
            font-size: 0.92rem;
            font-weight: 700;
            background: #ee9d0f;
            color: #fff;
            line-height: 1;
            text-decoration: none;
            text-align: center;
            margin-bottom: 0.62rem;
        }

        .btn-register:hover {
            color: #fff;
            background: #cf8808;
        }

        .auth-card .alert {
            font-size: 0.8rem;
            border-radius: 2px;
            margin-bottom: 0.65rem;
        }

        .remember-wrap {
            margin-bottom: 0.72rem;
        }

        .form-check-label {
            font-size: 0.82rem;
            color: #334155;
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

            .form-control {
                font-size: 1rem;
                padding: 0.62rem 0.68rem;
            }

            .input-group-text {
                width: 42px;
                font-size: 0.9rem;
            }

            .btn-auth,
            .btn-register {
                min-height: 44px;
                font-size: 0.95rem;
                padding: 0.68rem 0.9rem;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .forgot-password-link,
            .form-check-label {
                font-size: 0.9rem;
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

            @if(session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger mb-3">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('student.login') }}">
                @csrf
                <div class="mb-3">
                    <div class="input-group">
                        <input
                            type="text"
                            class="form-control @error('registration_number') is-invalid @enderror"
                            id="registration_number"
                            name="registration_number"
                            value="{{ old('registration_number') }}"
                            required
                            autofocus
                            placeholder="Registration number"
                        >
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                    </div>
                    @error('registration_number')
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
                            placeholder="Password"
                        >
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="remember-wrap">
                    <div class="form-check m-0">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-auth w-100 mb-3" aria-label="Login">
                    <span class="btn-text"><i class="fas fa-circle-check me-1"></i>Login</span>
                </button>

                <a href="{{ route('student.register') }}" class="btn-register">
                    <i class="fas fa-user-plus me-1"></i>Registration
                </a>

                <div>
                    <a href="{{ route('student.password.request') }}" class="forgot-password-link">Forgot Password?</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
