<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: "Manrope", sans-serif; background: #eef3fa; }
        .login-container { min-height: 100vh; display: grid; place-items: center; padding: 1rem; }
        .login-card { background: white; border-radius: 16px; border: 1px solid #d9e2ef; box-shadow: 0 14px 30px rgba(0,0,0,0.08); width: 100%; max-width: 420px; }
                    /* fixed-sidebar */
        @media (min-width: 768px) {
            .sidebar {
                position: fixed;
                top: 0;
                bottom: 0;
                overflow-y: auto;
                height: 100vh;
                z-index: 1020;
            }
            .main-content {
                margin-left: 25%;
                position: relative;
                height: auto;
                overflow: visible;
            }
        }
        @media (min-width: 992px) {
            .sidebar {
                width: 16.6666667%;
            }
            .main-content {
                margin-left: 16.6666667%;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card p-4">
            <div class="text-center mb-4">
                <h3 class="fw-bold mb-1">Admin Login</h3>
                <p class="text-muted mb-0">Access administration panel</p>
            </div>

            @if(session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email Address</label>
                    <input type="email" class="form-control form-control-lg" id="email" name="email" value="{{ old('email') }}" required autofocus>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    <a href="{{ route('admin.password.request') }}" class="small text-decoration-none">Forgot password?</a>
                </div>

                <button type="submit" class="btn btn-primary w-100 btn-lg">Login</button>
            </form>
        </div>
    </div>
</body>
</html>


