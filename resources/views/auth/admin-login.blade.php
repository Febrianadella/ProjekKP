<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login Admin - SIFORA</title>

    @vite(['resources/js/app.js'])

    <!-- Bootstrap Icons untuk bi bi-* -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        body {
            margin: 0;
            min-height: 100vh;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background-color: #f3f4f6;
        }

        .login-layout {
            display: flex;
            min-height: 100vh;
        }

        .login-left {
            flex: 1;
            padding: 40px 64px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background-color: #f8fafc;
        }

        .login-right {
            flex: 1;
            position: relative;
            color: #fff;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .login-right::before {
            content: "";
            position: absolute;
            inset: 0;
            background: url('/images/admin-bg.jpg') center/cover no-repeat;
            opacity: 0.2;
        }

        .right-content {
            position: relative;
            z-index: 1;
            max-width: 420px;
            text-align: left;
        }

        .brand-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 32px;
        }

        /* logo kiri: kotak biru dengan ikon shield */
        .brand-logo-wrap {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            background: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
        }

        .brand-logo-wrap i {
            font-size: 1.6rem;
        }

        .brand-text-title {
            font-weight: 600;
            font-size: 1rem;
        }

        .brand-text-sub {
            font-size: 0.78rem;
            color: #64748b;
        }

        .welcome-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .welcome-sub {
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 28px;
        }

        .admin-badge {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .form-label-small {
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 4px;
        }

        .form-control-custom {
            border-radius: 10px;
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
            font-size: 0.9rem;
        }

        .form-control-custom:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 1px rgba(37, 99, 235, 0.15);
        }

        .btn-admin-custom {
            border-radius: 999px;
            background-color: #2563eb;
            border-color: #2563eb;
            font-weight: 600;
            color: white;
        }

        .btn-admin-custom:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
            color: white;
        }

        .login-meta {
            font-size: 0.8rem;
            color: #6b7280;
        }

        .link-muted {
            font-size: 0.85rem;
        }

        @media (max-width: 992px) {
            .login-layout {
                flex-direction: column;
            }

            .login-right {
                display: none;
            }

            .login-left {
                padding: 32px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="login-layout">

        {{-- LEFT: FORM --}}
        <div class="login-left">
            {{-- BRAND --}}
            <div class="brand-row mb-4">
                <div class="brand-logo-wrap">
                    <i class="bi bi-shield-fill-check"></i>
                </div>
                <div>
                    <div class="brand-text-title">SIFORA Admin</div>
                    <div class="brand-text-sub">Panel Administrator</div>
                </div>
            </div>

            <div class="mb-4">
                <span class="admin-badge">
                    <i class="bi bi-shield-lock-fill me-1"></i> ADMIN ACCESS
                </span>
                <h1 class="welcome-title">Login Administrator</h1>
                <p class="welcome-sub">
                    Masuk dengan akun administrator untuk mengakses panel admin.
                </p>
            </div>

            <form method="POST" action="{{ route('admin.login.store') }}" class="mb-3">
                @csrf

                {{-- Email --}}
                <div class="mb-3">
                    <label for="email" class="form-label-small">Email Admin</label>
                    <input id="email" type="email"
                        class="form-control form-control-custom @error('email') is-invalid @enderror" name="email"
                        value="{{ old('email') }}" required autofocus autocomplete="email">
                    @error('email')
                        <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-3">
                    <label for="password" class="form-label-small">Password</label>
                    <input id="password" type="password"
                        class="form-control form-control-custom @error('password') is-invalid @enderror" name="password"
                        required autocomplete="current-password">
                    @error('password')
                        <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Remember --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label login-meta" for="remember">
                            Ingat saya
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-admin-custom w-100 py-2">
                    <i class="bi bi-shield-lock-fill me-2"></i> Masuk sebagai Admin
                </button>
            </form>

            <div class="mt-4">
                <a href="{{ route('login') }}" class="login-meta text-decoration-none">
                    ← Login sebagai Pimpinan
                </a>
            </div>

            <div class="mt-2">
                <a href="{{ route('home') }}" class="login-meta text-decoration-none">
                    ← Kembali ke beranda
                </a>
            </div>
        </div>

        {{-- RIGHT: HERO INFO --}}
        <div class="login-right">
            <div class="right-content">
                <div class="mb-4">
                    <h2 class="fw-semibold mb-2">
                        <i class="bi bi-gear-fill me-2"></i> Panel Administrator
                    </h2>
                    <p class="mb-0 small">
                        Area khusus untuk administrator sistem.
                        Kelola pengguna, surat, dan konfigurasi sistem dengan akses penuh.
                    </p>
                </div>
                <div class="alert alert-warning bg-white bg-opacity-10 border-0 text-white small">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Halaman ini hanya untuk administrator sistem
                </div>
            </div>
        </div>

    </div>
</body>

</html>
