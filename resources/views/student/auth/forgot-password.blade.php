<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Student Portal</title>
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
            max-width: 450px;
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

        .info-box {
            background: #f7fafc;
            border-left: 4px solid #667eea;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            color: #718096;
            line-height: 1.6;
        }

        .info-box i {
            color: #667eea;
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="card-header-icon">
                <i class="fas fa-key"></i>
            </div>

            <div class="text-center mb-4">
                <h3>Forgot Password?</h3>
                <p>Enter your email address and we'll send you a link to reset your password</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <div><i class="fas fa-exclamation-circle me-2"></i>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if (session('status'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
                </div>
            @endif

            @if (session('reset_url'))
                <div class="alert alert-warning">
                    <div class="mb-2"><i class="fas fa-link me-2"></i>Temporary reset link:</div>
                    <a href="{{ session('reset_url') }}" class="small" style="word-break: break-all;">
                        {{ session('reset_url') }}
                    </a>
                </div>
            @endif

            <form method="POST" action="{{ route('student.password.email') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope me-2" style="color: #667eea;"></i>Email Address
                    </label>
                    <input type="email"
                           class="form-control @error('email') is-invalid @enderror"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           placeholder="Enter your registered email">
                    @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="info-box">
                    <i class="fas fa-info-circle"></i>
                    <strong>Note:</strong> A password reset link will be sent to your email address. Please check your inbox and follow the instructions to reset your password.
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-2"></i>Send Reset Link
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
