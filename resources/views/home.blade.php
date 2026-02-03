<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SIFORA - Sistem Informasi Persuratan</title>

    @vite(['resources/js/app.js'])

    <!-- Bootstrap Icons (wajib untuk bi bi-*) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background-color: #f3f4f6;
        }

        .hero-section {
            position: relative;
            min-height: 520px;
            color: #fff;
            background: url('/images/hero-office.jpg') center/cover no-repeat;
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background-color: #1d4ed8;
            /* biru flat sesuai header */
        }

        .hero-content {
            position: relative;
            z-index: 1;
            padding-top: 120px;
            padding-bottom: 120px;
        }

        .hero-badge {
            background: rgba(15, 23, 42, 0.85);
            border-radius: 999px;
            padding: 6px 14px;
            font-size: 0.75rem;
        }

        .feature-card {
            background: #ffffff;
            border-radius: 1.25rem;
            padding: 1.4rem 1.6rem;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.06);
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 0.9rem;
        }

        .bg-primary-soft {
            background: #e0ebff;
        }

        .bg-warning-soft {
            background: #fff5e6;
        }

        .bg-info-soft {
            background: #e6f4ff;
        }

        .bg-danger-soft {
            background: #ffecef;
        }

        .bg-success-soft {
            background: #e7f8f1;
        }

        .bg-purple-soft {
            background: #f2e9ff;
        }

        /* warna teks ungu karena tidak ada text-purple di Bootstrap */
        .text-purple {
            color: #8b5cf6;
        }

        .step-circle {
            width: 46px;
            height: 46px;
            border-radius: 999px;
            background: #e0ebff;
            color: #1d4ed8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1rem;
        }

        .extra-small {
            font-size: 0.78rem;
        }
    </style>
</head>

<body>

    {{-- HERO --}}
    <section class="hero-section">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <div class="row justify-content-between">
                <div class="col-lg-7">
                    <div class="hero-badge mb-3 small">
                        Sistem Terpercaya &amp; Aman
                    </div>
                    <h1 class="display-4 fw-bold mb-3">
                        Kelola Surat Masuk &amp; Keluar<br>
                        dengan Mudah
                    </h1>
                    <p class="lead mb-4">
                        Sistem pengelolaan surat digital untuk Balai Besar Perikanan Budidaya
                        Laut Lampung. Efisien, terorganisir, dan profesional.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-4"
                            style="background-color:#173895ff;border-color:#1d4ed8;">
                            Daftar
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4">
                            Masuk
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FITUR UNGGULAN --}}
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="fw-semibold mb-1">Fitur Unggulan</h2>
                <p class="text-muted small mb-0">
                    Sistem yang dirancang khusus untuk memudahkan pengelolaan surat di
                    instansi pemerintahan.
                </p>
            </div>

            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon bg-primary-soft text-primary">
                            <i class="bi bi-inbox"></i>
                        </div>
                        <h5 class="mb-1">Surat Masuk</h5>
                        <p class="text-muted small mb-0">
                            Kelola dan arsipkan surat masuk dengan sistem yang terorganisir.
                            Lacak status dan riwayat setiap surat dengan mudah.
                        </p>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon bg-warning-soft text-warning">
                            <i class="bi bi-send"></i>
                        </div>
                        <h5 class="mb-1">Surat Keluar</h5>
                        <p class="text-muted small mb-0">
                            Buat dan kirim surat balasan dengan cepat. Sistem otomatis
                            menghubungkan surat masuk dengan balasannya.
                        </p>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon bg-info-soft text-info">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <h5 class="mb-1">Laporan Detail</h5>
                        <p class="text-muted small mb-0">
                            Generate laporan lengkap dengan filter tanggal, status, dan
                            kategori. Ekspor ke PDF atau Excel dengan satu klik.
                        </p>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon bg-purple-soft text-purple">
                            <i class="bi bi-people"></i>
                        </div>
                        <h5 class="mb-1">Role-Based Access</h5>
                        <p class="text-muted small mb-0">
                            Sistem akses berbasis peran untuk Admin dan Pimpinan. Setiap
                            role memiliki hak akses yang sesuai dengan tugasnya.
                        </p>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon bg-danger-soft text-danger">
                            <i class="bi bi-file-earmark-pdf"></i>
                        </div>
                        <h5 class="mb-1">Download PDF</h5>
                        <p class="text-muted small mb-0">
                            Unduh setiap surat dalam format PDF untuk arsip atau keperluan
                            cetak. Kualitas dokumen terjaga dengan baik.
                        </p>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon bg-success-soft text-success">
                            <i class="bi bi-speedometer2"></i>
                        </div>
                        <h5 class="mb-1">Dashboard Real-time</h5>
                        <p class="text-muted small mb-0">
                            Monitor statistik surat masuk dan keluar secara real-time
                            melalui dashboard yang informatif dan mudah dipahami.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CARA KERJA SISTEM --}}
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="fw-semibold mb-1">Cara Kerja Sistem</h2>
                <p class="text-muted small mb-0">
                    Proses pengelolaan surat yang sederhana dan efisien.
                </p>
            </div>

            <div class="row g-3 text-center">
                <div class="col-6 col-md-3">
                    <div class="step-circle mb-2">1</div>
                    <h6 class="fw-semibold mb-1">Daftar Akun</h6>
                    <p class="text-muted extra-small mb-0">
                        Buat akun baru dengan mengisi data yang diperlukan.
                    </p>
                </div>
                <div class="col-6 col-md-3">
                    <div class="step-circle mb-2">2</div>
                    <h6 class="fw-semibold mb-1">Login Sistem</h6>
                    <p class="text-muted extra-small mb-0">
                        Masuk ke dashboard sesuai dengan role Anda.
                    </p>
                </div>
                <div class="col-6 col-md-3">
                    <div class="step-circle mb-2">3</div>
                    <h6 class="fw-semibold mb-1">Kelola Surat</h6>
                    <p class="text-muted extra-small mb-0">
                        Upload, edit, dan kelola surat masuk dan keluar.
                    </p>
                </div>
                <div class="col-6 col-md-3">
                    <div class="step-circle mb-2">4</div>
                    <h6 class="fw-semibold mb-1">Generate Laporan</h6>
                    <p class="text-muted extra-small mb-0">
                        Buat laporan dan export data sesuai kebutuhan.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-5 text-white text-center" style="background-color:#1d4ed8;">
        <div class="container">
            <h2 class="fw-semibold mb-2">Siap Memulai Pengelolaan Surat Digital?</h2>
            <p class="mb-4 small">
                Bergabunglah dengan sistem pengelolaan surat modern SIFORA.
                Daftar sekarang dan rasakan kemudahan dalam mengelola surat instansi Anda.
            </p>
            <a href="{{ route('register') }}" class="btn btn-light btn-lg px-4">
                Daftar Sekarang
            </a>
        </div>
    </section>

</body>

</html>
