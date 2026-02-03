<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Registrasi - SIFORA</title>

    @vite(['resources/js/app.js'])

    <!-- Bootstrap Icons (sama seperti login) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        body {
            margin: 0;
            min-height: 100vh;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background-color: #f3f4f6;
        }

        .auth-layout {
            display: flex;
            min-height: 100vh;
        }

        /* KIRI: HERO (sama nuansa dengan login-right) */
        .auth-left {
            flex: 1;
            position: relative;
            color: #fff;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .auth-left::before {
            content: "";
            position: absolute;
            inset: 0;
            background: url('/images/register-bg.jpg') center/cover no-repeat;
            opacity: 0.2;
        }

        .auth-left-content {
            position: relative;
            z-index: 1;
            max-width: 430px;
        }

        .left-icon-wrap {
            width: 72px;
            height: 72px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.16);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 18px;
        }

        .auth-right {
            flex: 1;
            padding: 40px 64px;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .brand-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 32px;
        }

        /* Brand logo kanan pakai ikon, bukan gambar file */
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

        .form-title {
            font-size: 1.7rem;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .form-sub {
            font-size: 0.9rem;
            color: #6b7280;
            margin-bottom: 24px;
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

        .btn-primary-custom {
            border-radius: 999px;
            background-color: #2563eb;
            border-color: #2563eb;
            font-weight: 600;
        }

        .btn-primary-custom:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
        }

        .auth-meta {
            font-size: 0.8rem;
            color: #6b7280;
        }

        @media (max-width: 992px) {
            .auth-layout {
                flex-direction: column;
            }

            .auth-left {
                display: none;
            }

            .auth-right {
                padding: 32px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="auth-layout">

        {{-- LEFT HERO --}}
        <div class="auth-left">
            <div class="auth-left-content">
                <div class="left-icon-wrap">
                    <i class="bi bi-people-fill"></i>
                </div>
                <h2 class="fw-semibold mb-2">
                    Bergabung dengan Tim SIFORA
                </h2>
                <p class="mb-0 small">
                    Daftar sekarang dan mulai kelola surat instansi dengan sistem digital
                    yang modern dan efisien.
                </p>
            </div>
        </div>

        {{-- RIGHT FORM --}}
        <div class="auth-right">
            <div class="brand-row">
                <div class="brand-logo-wrap">
                    <i class="bi bi-envelope-fill"></i>
                </div>
                <div>
                    <div class="brand-text-title">SIFORA</div>
                    <div class="brand-text-sub">Sistem Informasi Persuratan</div>
                </div>
            </div>

            <div class="mb-3">
                <h1 class="form-title">Buat Akun Baru</h1>
                <p class="form-sub">
                    Lengkapi data di bawah untuk mendaftar.
                </p>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                {{-- Nama Lengkap --}}
                <div class="mb-3">
                    <label for="name" class="form-label-small">Nama Lengkap</label>
                    <input id="name" type="text"
                        class="form-control form-control-custom @error('name') is-invalid @enderror" name="name"
                        value="{{ old('name') }}" required autofocus autocomplete="name">
                    @error('name')
                        <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label for="email" class="form-label-small">Email</label>
                    <input id="email" type="email"
                        class="form-control form-control-custom @error('email') is-invalid @enderror" name="email"
                        value="{{ old('email') }}" required autocomplete="email">
                    @error('email')
                        <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Nomor Telepon (hapus jika tidak dipakai di tabel users) --}}
                <div class="mb-3">
                    <label for="phone" class="form-label-small">Nomor Telepon</label>
                    <input id="phone" type="text"
                        class="form-control form-control-custom @error('phone') is-invalid @enderror" name="phone"
                        value="{{ old('phone') }}" placeholder="08xxxxxxxxxx">
                    @error('phone')
                        <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-3">
                    <label for="password" class="form-label-small">Password</label>
                    <input id="password" type="password"
                        class="form-control form-control-custom @error('password') is-invalid @enderror" name="password"
                        required autocomplete="new-password">
                    <small class="auth-meta">Minimal 8 karakter</small>
                    @error('password')
                        <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Konfirmasi Password --}}
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label-small">Konfirmasi Password</label>
                    <input id="password_confirmation" type="password" class="form-control form-control-custom"
                        name="password_confirmation" required autocomplete="new-password">
                </div>

                {{-- Syarat & ketentuan (opsional) --}}
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="terms">
                    <label class="form-check-label auth-meta" for="terms">
                        Saya setuju dengan syarat dan ketentuan yang berlaku
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-primary-custom w-100 py-2">
                    Daftar Sekarang
                </button>
            </form>

            <div class="text-center auth-meta mt-4">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-primary text-decoration-none fw-semibold">
                    Masuk di sini
                </a>
            </div>

            <div class="mt-3">
                <a href="{{ route('home') }}" class="auth-meta text-decoration-none">
                    ← Kembali ke beranda
                </a>
            </div>
        </div>

    </div>
</body>

</html>
