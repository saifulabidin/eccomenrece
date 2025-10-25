<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login - {{ config('app.name') }}</title>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            width: 100%;
            max-width: 420px;
            backdrop-filter: blur(10px);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h1 {
            color: #333;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: #666;
            margin: 0;
        }

        .google-btn {
            background: #4285f4;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            margin-bottom: 1rem;
        }

        .google-btn:hover {
            background: #3367d6;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(66, 133, 244, 0.3);
        }

        .alert {
            border-radius: 10px;
            border: none;
            margin-bottom: 1.5rem;
        }

        .admin-info {
            background: rgba(66, 133, 244, 0.1);
            border-left: 4px solid #4285f4;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .admin-info h6 {
            color: #4285f4;
            margin-bottom: 0.5rem;
        }

        .admin-info p {
            color: #666;
            margin: 0;
            font-size: 0.9rem;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-section i {
            font-size: 3rem;
            color: #4285f4;
            margin-bottom: 1rem;
        }

        @media (max-width: 576px) {
            .login-container {
                margin: 1rem;
                padding: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-section">
            <i class="bi bi-shield-lock"></i>
            <h1>Admin Panel</h1>
        </div>

        <div class="login-header">
            <h2>Login Admin</h2>
            <p>Gunakan akun Google Anda untuk mengakses panel admin</p>
        </div>

        <!-- Success/Alert Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Admin Info -->
        <div class="admin-info">
            <h6><i class="bi bi-info-circle me-2"></i>Informasi Akses</h6>
            <p>Hanya pengguna dengan email terdaftar yang dapat mengakses panel admin ini. Pastikan Anda menggunakan akun Google yang telah diotorisasi.</p>
        </div>

        <!-- Google Login Button -->
        <a href="{{ route('admin.auth.google') }}" class="google-btn">
            <i class="bi bi-google"></i>
            Login dengan Google
        </a>

        <!-- Security Note -->
        <div class="text-center mt-3">
            <small class="text-muted">
                <i class="bi bi-shield-check me-1"></i>
                Koneksi aman dengan enkripsi SSL/TLS
            </small>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>