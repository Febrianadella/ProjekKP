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
                    <div class="mb-3 hero-badge small">
                        Sistem Terpercaya &amp; Aman
                    </div>
                    <h1 class="mb-3 display-4 fw-bold">
                        Kelola Surat Masuk &amp; Keluar<br>
                        dengan Mudah
                    </h1>
                    <p class="mb-4 lead">
                        Sistem pengelolaan surat digital untuk Pelayanan Public BBPBL Lampung. Efisien, terorganisir,
                        dan profesional.
                    </p>
                    <div class="flex-wrap gap-2 d-flex">
                        <a href="{{ route('register') }}" class="px-4 btn btn-primary btn-lg"
                            style="background-color:#173895ff;border-color:#1d4ed8;">
                            Daftar
                        </a>
                        <a href="{{ route('login') }}" class="px-4 btn btn-outline-light btn-lg">
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
            <div class="mb-4 text-center">
                <h2 class="mb-1 fw-semibold">Fitur Unggulan</h2>
                <p class="mb-0 text-muted small">
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
                        <p class="mb-0 text-muted small">
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
                        <p class="mb-0 text-muted small">
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
                        <p class="mb-0 text-muted small">
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
                        <p class="mb-0 text-muted small">
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
                        <p class="mb-0 text-muted small">
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
                        <p class="mb-0 text-muted small">
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
            <div class="mb-4 text-center">
                <h2 class="mb-1 fw-semibold">Cara Kerja Sistem</h2>
                <p class="mb-0 text-muted small">
                    Proses pengelolaan surat yang sederhana dan efisien.
                </p>
            </div>

            <div class="text-center row g-3">
                <div class="col-6 col-md-3">
                    <div class="mb-2 step-circle">1</div>
                    <h6 class="mb-1 fw-semibold">Daftar Akun</h6>
                    <p class="mb-0 text-muted extra-small">
                        Buat akun baru dengan mengisi data yang diperlukan.
                    </p>
                </div>
                <div class="col-6 col-md-3">
                    <div class="mb-2 step-circle">2</div>
                    <h6 class="mb-1 fw-semibold">Login Sistem</h6>
                    <p class="mb-0 text-muted extra-small">
                        Masuk ke dashboard sesuai dengan role Anda.
                    </p>
                </div>
                <div class="col-6 col-md-3">
                    <div class="mb-2 step-circle">3</div>
                    <h6 class="mb-1 fw-semibold">Kelola Surat</h6>
                    <p class="mb-0 text-muted extra-small">
                        Upload, edit, dan kelola surat masuk dan keluar.
                    </p>
                </div>
                <div class="col-6 col-md-3">
                    <div class="mb-2 step-circle">4</div>
                    <h6 class="mb-1 fw-semibold">Generate Laporan</h6>
                    <p class="mb-0 text-muted extra-small">
                        Buat laporan dan export data sesuai kebutuhan.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-5 text-center text-white" style="background-color:#1d4ed8;">
        <div class="container">
            <h2 class="mb-2 fw-semibold">Siap Memulai Pengelolaan Surat Digital?</h2>
            <p class="mb-4 small">
                Bergabunglah dengan sistem pengelolaan surat modern SIFORA.
                Daftar sekarang dan rasakan kemudahan dalam mengelola surat instansi Anda.
            </p>
            <a href="{{ route('register') }}" class="px-4 btn btn-light btn-lg">
                Daftar Sekarang
            </a>
        </div>
    </section>

</body>

</html>
