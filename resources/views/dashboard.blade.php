@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="dashboard-wrapper">

        {{-- HEADER --}}
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-2 mb-4">
            <div>
                <h2 class="fw-semibold mb-1 fs-4">Dashboard</h2>
                <p class="text-muted mb-0 small">Sistem Pengelolaan Surat</p>
            </div>
            <div class="text-sm-end">
                <span class="small text-muted d-block">Administrator</span>
                <span class="badge rounded-pill bg-primary-soft text-primary small">
                    SIFORA
                </span>
            </div>
        </div>

        {{-- BARIS ATAS: 3 CARD PERIHAL, LEBAR SAMA (BESAR) --}}
        <div class="row row-cols-1 row-cols-lg-3 g-3 mb-3">
            {{-- Surat perihal PKL --}}
            <div class="col">
                <div class="stat-card stat-card-lg">
                    <div class="stat-icon bg-primary-soft text-primary me-3">
                        <i class="bi bi-mortarboard"></i>
                    </div>
                    <div>
                        <p class="stat-label mb-0 small text-uppercase text-muted">Surat Perihal PKL</p>
                        <h4 class="mb-0">{{ $totalPKL }}</h4>
                        <span class="stat-sub text-muted extra-small">
                            Total surat dengan perihal PKL
                        </span>
                    </div>
                </div>
            </div>

            {{-- Surat perihal Kunjungan --}}
            <div class="col">
                <div class="stat-card stat-card-lg">
                    <div class="stat-icon bg-warning-soft text-warning me-3">
                        <i class="bi bi-geo-alt"></i>
                    </div>
                    <div>
                        <p class="stat-label mb-0 small text-uppercase text-muted">Surat Perihal Kunjungan</p>
                        <h4 class="mb-0">{{ $totalKunjungan }}</h4>
                        <span class="stat-sub text-muted extra-small">
                            Total surat dengan perihal Kunjungan
                        </span>
                    </div>
                </div>
            </div>

            {{-- Surat perihal Lainnya --}}
            <div class="col">
                <div class="stat-card stat-card-lg">
                    <div class="stat-icon bg-info-soft text-info me-3">
                        <i class="bi bi-three-dots"></i>
                    </div>
                    <div>
                        <p class="stat-label mb-0 small text-uppercase text-muted">Surat Perihal Lainnya</p>
                        <h4 class="mb-0">{{ $totalLainnya }}</h4>
                        <span class="stat-sub text-muted extra-small">
                            Total surat dengan perihal lainnya
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- BARIS TENGAH: 4 CARD (KECIL) --}}
        <div class="row row-cols-1 row-cols-md-4 g-3 mb-4">
            {{-- Surat bulan ini --}}
            <div class="col">
                <div class="stat-card stat-card-sm">
                    <div class="stat-icon bg-success-soft text-success me-3">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <div>
                        <p class="stat-label mb-0 small text-uppercase text-muted">Surat Bulan Ini</p>
                        <h5 class="mb-0" id="surat-bulan-ini">{{ $suratBulanIni }}</h5>
                        <span class="stat-sub text-primary extra-small">
                            {{ now()->translatedFormat('F Y') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Total surat masuk --}}
            <div class="col">
                <div class="stat-card stat-card-sm">
                    <div class="stat-icon bg-primary-soft text-primary me-3">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <div>
                        <p class="stat-label mb-0 small text-uppercase text-muted">Surat Masuk</p>
                        <h5 class="mb-0" id="total-masuk">{{ $totalMasuk }}</h5>
                        {{-- deskripsi dihapus --}}
                    </div>
                </div>
            </div>

            {{-- Total surat keluar --}}
            <div class="col">
                <div class="stat-card stat-card-sm">
                    <div class="stat-icon bg-warning-soft text-warning me-3">
                        <i class="bi bi-send"></i>
                    </div>
                    <div>
                        <p class="stat-label mb-0 small text-uppercase text-muted">Surat Keluar</p>
                        <h5 class="mb-0" id="total-keluar">{{ $totalKeluar }}</h5>
                        {{-- deskripsi dihapus --}}
                    </div>
                </div>
            </div>

            {{-- Belum dibalas --}}
            <div class="col">
                <div class="stat-card stat-card-sm">
                    <div class="stat-icon bg-danger-soft text-danger me-3">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <p class="stat-label mb-0 small text-uppercase text-muted">Belum Dibalas</p>
                        <h5 class="mb-0" id="belum-dibalas">{{ $belumDibalas }}</h5>
                        <span class="badge rounded-pill bg-danger-subtle text-danger extra-small">
                            Perlu tindak lanjut
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- SURAT MASUK TERBARU --}}
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Surat Masuk Terbaru</h5>
                    <a href="{{ route('surat') }}" class="small text-primary">Lihat Semua</a>
                </div>

                @forelse ($suratTerbaru as $surat)
                    <div class="latest-item d-flex align-items-center gap-3 mb-3 p-3 rounded-3">
                        <div
                            class="latest-icon bg-primary-soft text-primary d-flex align-items-center justify-content-center rounded-3">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-0 fw-semibold small">
                                {{ 'SM/' . str_pad($surat->id, 3, '0', STR_PAD_LEFT) . '/' . date('Y') }}
                            </p>
                            <p class="mb-0 text-muted small">{{ $surat->perihal }}</p>
                            <span class="text-muted extra-small">
                                {{ $surat->asal_surat }}
                                • {{ \Carbon\Carbon::parse($surat->tanggal_surat)->translatedFormat('d F Y') }}
                            </span>
                        </div>

                        @php
                            $statusText = $surat->status ?? 'Belum Dibalas';
                            $lower = mb_strtolower($statusText);
                            $isSelesai = str_contains($lower, 'sudah') && str_contains($lower, 'dibalas');
                            $badgeClass = $isSelesai ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger';
                        @endphp

                        <span class="badge rounded-pill {{ $badgeClass }} extra-small">
                            {{ $statusText }}
                        </span>
                    </div>
                @empty
                    <p class="text-muted small mb-0">Belum ada data surat.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
