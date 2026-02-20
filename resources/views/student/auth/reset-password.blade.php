<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Student Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }

        .login-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 500px;
            padding: 2.5rem;
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-header-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 1.8rem;
            color: white;
        }

        .text-center h3 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .text-center p {
            color: #718096;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .form-label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
        }

        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: #f7fafc;
        }

        .form-control:focus {
            background: white;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-control:read-only {
            background: #edf2f8;
            cursor: not-allowed;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            border-radius: 12px;
            padding: 0.85rem 1.5rem;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 1.5rem;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(102, 126, 234, 0.3);
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .alert {
            border-radius: 12px;
            border: 2px solid;
            margin-bottom: 1.5rem;
            padding: 1rem 1.2rem;
        }

        .alert-danger {
            background: #fff5f5;
            border-color: #fc8181;
            color: #c53030;
        }

        .alert-success {
            background: #f0fff4;
            border-color: #9ae6b4;
            color: #22543d;
        }

        .text-center-bottom {
            text-align: center;
            margin-top: 1.5rem;
        }

        .text-center-bottom p {
            margin: 0;
            color: #718096;
        }

        .text-center-bottom a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .text-center-bottom a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .password-strength-meter {
            margin-top: 0.5rem;
            height: 6px;
            border-radius: 3px;
            background: #e2e8f0;
            overflow: hidden;
        }

        .password-strength-meter-fill {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 3px;
        }

        .password-strength-text {
            font-size: 0.8rem;
            margin-top: 0.3rem;
            font-weight: 500;
        }

        .hint-box {
            background: #f7fafc;
            border-left: 4px solid #667eea;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1.5rem;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
            color: #718096;
            line-height: 1.6;
        }

        .hint-box ul {
            margin: 0.5rem 0 0 1.2rem;
            padding-left: 0;
        }

        .hint-box li {
            margin-bottom: 0.3rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="card-header-icon">
                <i class="fas fa-lock-open"></i>
            </div>

            <div class="text-center mb-4">
                <h3>Reset Password</h3>
                <p>Enter your new password below</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <div><i class="fas fa-exclamation-circle me-2"></i>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('student.password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope me-2" style="color: #667eea;"></i>Email Address
                    </label>
                    <input type="email"
                           class="form-control @error('email') is-invalid @enderror"
                           id="email"
                           name="email"
                           value="{{ old('email', $email) }}"
                           required
                           readonly>
                    @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock me-2" style="color: #667eea;"></i>New Password
                    </label>
                    <input type="password"
                           class="form-control @error('password') is-invalid @enderror"
                           id="password"
                           name="password"
                           required
                           placeholder="Enter a strong password"
                           onkeyup="checkPasswordStrength()">
                    <div class="password-strength-meter">
                        <div class="password-strength-meter-fill" id="passwordStrengthMeter"></div>
                    </div>
                    <div class="password-strength-text" id="passwordStrengthText"></div>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">
                        <i class="fas fa-check-double me-2" style="color: #667eea;"></i>Confirm Password
                    </label>
                    <input type="password"
                           class="form-control @error('password_confirmation') is-invalid @enderror"
                           id="password_confirmation"
                           name="password_confirmation"
                           required
                           placeholder="Re-enter your password">
                    @error('password_confirmation')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="hint-box">
                    <strong><i class="fas fa-shield-alt me-1"></i>Password Requirements:</strong>
                    <ul>
                        <li>At least 8 characters long</li>
                        <li>Mix of uppercase and lowercase letters</li>
                        <li>Include numbers and special characters</li>
                        <li>Avoid using personal information</li>
                    </ul>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-refresh me-2"></i>Reset Password
                </button>

                <div class="text-center-bottom">
                    <p>
                        <a href="{{ route('student.login') }}">
                            <i class="fas fa-arrow-left me-1"></i>Back to Login
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const meter = document.getElementById('passwordStrengthMeter');
            const text = document.getElementById('passwordStrengthText');

            let strength = 0;
            const hasLower = /[a-z]/.test(password);
            const hasUpper = /[A-Z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(password);
            const length = password.length;

            if (length >= 8) strength += 25;
            if (hasLower) strength += 25;
            if (hasUpper) strength += 25;
            if (hasNumber || hasSpecial) strength += 25;

            meter.style.width = strength + '%';

            if (strength < 50) {
                meter.style.background = '#f56565';
                text.textContent = '❌ Weak password';
                text.style.color = '#f56565';
            } else if (strength < 75) {
                meter.style.background = '#ed8936';
                text.textContent = '⚠️ Fair password';
                text.style.color = '#ed8936';
            } else {
                meter.style.background = '#48bb78';
                text.textContent = '✅ Strong password';
                text.style.color = '#48bb78';
            }
        }
    </script>
</body>
</html>
</div>
</body>
</html>
