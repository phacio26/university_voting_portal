<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: "Manrope", sans-serif; background: #eef3fa; }
        .auth-wrap { min-height: 100vh; display: grid; place-items: center; padding: 1rem; }
        .auth-card { width: 100%; max-width: 520px; border-radius: 16px; border: 1px solid #d9e2ef; background: #fff; box-shadow: 0 14px 30px rgba(0,0,0,0.08); }
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
<div class="auth-wrap">
    <div class="auth-card p-4">
        <h1 class="h5 fw-bold mb-2">Set New Admin Password</h1>
        <p class="text-muted">Use a secure password for your admin account.</p>

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Email Address</label>
                <input id="email" type="email" class="form-control form-control-lg" name="email" value="{{ old('email', $email) }}" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">New Password</label>
                <input id="password" type="password" class="form-control form-control-lg" name="password" required>
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                <input id="password_confirmation" type="password" class="form-control form-control-lg" name="password_confirmation" required>
            </div>

            <button class="btn btn-primary w-100">Reset Password</button>
        </form>
    </div>
</div>
</body>
</html>


