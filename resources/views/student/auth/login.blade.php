<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --auth-bg-a: #0a4d91;
            --auth-bg-b: #0f766e;
            --auth-ink: #0f172a;
            --auth-muted: #475569;
            --auth-border: #dbe3ee;
            --auth-focus: #0a4d91;
        }

        * {
            font-family: "Manrope", sans-serif;
        }

        body {
            margin: 0;
            min-height: 100vh;
            color: var(--auth-ink);
            background:
                radial-gradient(700px 260px at 10% -20%, rgba(255, 255, 255, 0.22), transparent 65%),
                radial-gradient(900px 340px at 100% 120%, rgba(255, 255, 255, 0.16), transparent 70%),
                linear-gradient(135deg, var(--auth-bg-a), var(--auth-bg-b));
        }

        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.2rem;
        }

        .auth-card {
            width: 100%;
            max-width: 460px;
            border: 1px solid var(--auth-border);
            border-radius: 18px;
            background: #ffffff;
            box-shadow: 0 20px 42px rgba(15, 23, 42, 0.16);
            padding: 1.8rem;
        }

        .auth-title {
            font-size: 1.55rem;
            font-weight: 800;
            margin-bottom: 0.35rem;
            color: #0a1528;
        }

        .auth-subtitle {
            color: var(--auth-muted);
            font-size: 0.92rem;
            margin-bottom: 0;
        }

        .form-label {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-control {
            border: 1px solid var(--auth-border);
            border-radius: 11px;
            padding: 0.7rem 0.85rem;
            font-size: 0.95rem;
            color: #0f172a;
            background: #f8fafc;
        }

        .form-control:focus {
            border-color: var(--auth-focus);
            background: #fff;
            box-shadow: 0 0 0 0.2rem rgba(10, 77, 145, 0.14);
        }

        .form-check-label {
            color: #334155;
            font-size: 0.9rem;
        }

        .forgot-password-link {
            color: #0a4d91;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.88rem;
        }

        .forgot-password-link:hover {
            color: #0f766e;
            text-decoration: underline;
        }

        .btn-auth {
            border: 0;
            border-radius: 11px;
            padding: 0.78rem 1rem;
            font-size: 0.95rem;
            font-weight: 700;
            background: linear-gradient(135deg, #0a4d91, #0f766e);
            color: #fff;
        }

        .btn-auth:hover {
            color: #fff;
            filter: brightness(1.03);
        }

        .auth-footer,
        .auth-footer a {
            font-size: 0.9rem;
        }

        .auth-footer a {
            font-weight: 700;
            color: #0a4d91;
            text-decoration: none;
        }

        .auth-footer a:hover {
            color: #0f766e;
            text-decoration: underline;
        }

        @media (max-width: 576px) {
            .auth-container {
                align-items: flex-start;
                padding-top: 1rem;
            }

            .auth-card {
                border-radius: 14px;
                padding: 1rem;
            }

            .auth-title {
                font-size: 1.28rem;
            }

            .auth-subtitle {
                font-size: 0.85rem;
            }

            .auth-meta {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 0.6rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="text-center mb-4">
                <h1 class="auth-title"><i class="fas fa-vote-yea me-2" style="color:#0a4d91;"></i>Student Login</h1>
                <p class="auth-subtitle">Sign in to continue to your dashboard</p>
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
                    <label for="registration_number" class="form-label">Registration Number</label>
                    <input
                        type="text"
                        class="form-control @error('registration_number') is-invalid @enderror"
                        id="registration_number"
                        name="registration_number"
                        value="{{ old('registration_number') }}"
                        required
                        autofocus
                        placeholder="Enter your registration number"
                    >
                    @error('registration_number')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input
                        type="password"
                        class="form-control @error('password') is-invalid @enderror"
                        id="password"
                        name="password"
                        required
                        placeholder="Enter your password"
                    >
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between align-items-center auth-meta mb-3">
                    <div class="form-check m-0">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    <a href="{{ route('student.password.request') }}" class="forgot-password-link">Forgot password?</a>
                </div>

                <button type="submit" class="btn btn-auth w-100 mb-3">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </button>

                <div class="text-center auth-footer">
                    <span>Don't have an account?</span>
                    <a href="{{ route('student.register') }}">Register here</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
