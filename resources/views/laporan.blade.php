@extends('layouts.app')

@section('title', 'Laporan Surat')

@section('content')
    <div class="dashboard-wrapper">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="fw-semibold mb-1">Laporan Surat</h2>
                <p class="text-muted mb-0 small">Analisis dan ringkasan data surat.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('laporan.export.pdf', request()->query()) }}"
                    class="btn btn-danger d-flex align-items-center gap-2 btn-sm">
                    <i class="bi bi-filetype-pdf"></i>
                    Export PDF
                </a>
                <a href="{{ route('laporan.export.excel', request()->query()) }}"
                    class="btn btn-success d-flex align-items-center gap-2 btn-sm">
                    <i class="bi bi-file-earmark-spreadsheet"></i>
                    Export Excel
                </a>
            </div>
        </div>

        {{-- CARD FILTER + TABEL --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">

                {{-- FILTER DATA --}}
                <div class="mb-3">
                    <form method="GET" action="{{ route('laporan.index') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-12 col-md-3">
                                <label class="form-label small mb-1">Tanggal Mulai</label>
                                <input type="date" name="tanggal_mulai" class="form-control form-control-sm"
                                    value="{{ $tanggalMulai ?? '' }}">
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label small mb-1">Tanggal Akhir</label>
                                <input type="date" name="tanggal_akhir" class="form-control form-control-sm"
                                    value="{{ $tanggalAkhir ?? '' }}">
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label small mb-1">Perihal</label>
                                <input type="text" name="perihal" class="form-control form-control-sm"
                                    placeholder="Cari perihal..." value="{{ $perihal ?? '' }}">
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label small mb-1">Status</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="semua" {{ ($status ?? 'semua') == 'semua' ? 'selected' : '' }}>Semua
                                        Status</option>
                                    <option value="Sudah Dibalas"
                                        {{ ($status ?? '') == 'Sudah Dibalas' ? 'selected' : '' }}>Sudah Dibalas</option>
                                    <option value="Belum Dibalas"
                                        {{ ($status ?? '') == 'Belum Dibalas' ? 'selected' : '' }}>Belum Dibalas</option>
                                </select>
                            </div>
                            <div class="col-12 d-flex flex-wrap gap-2 mt-2">
                                <a href="{{ route('laporan.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                                </a>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-search me-1"></i> Terapkan Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- GARIS PEMISAH --}}
                <hr class="border-light border-opacity-50 mb-3">

                {{-- TABEL LAPORAN --}}
                <div class="table-responsive">
                    <table class="table align-middle table-hover small mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width:60px;">No</th>
                                <th>Tanggal Masuk</th>
                                <th>Asal Surat</th>
                                <th>Perihal</th>
                                <th>Tanggal Keluar</th>
                                <th>Status</th>
                                <th class="text-center" style="width:160px; min-width:160px;">File</th>
                            </tr>
                        </thead>
                        <tbody id="laporan-body">
                            @forelse($surat as $index => $row)
                                @php
                                    // Status berdasarkan file_balasan
                                    $isDibalas = !empty($row->file_balasan);

                                    if ($isDibalas) {
                                        $statusClass = 'badge bg-success-soft text-success extra-small';
                                        $statusText = 'Sudah Dibalas';
                                        $tanggalKeluar = $row->tanggal_balasan
                                            ? \Carbon\Carbon::parse($row->tanggal_balasan)->format('d M Y')
                                            : '-';
                                    } else {
                                        $statusClass = 'badge bg-danger-soft text-danger extra-small';
                                        $statusText = 'Belum Dibalas';
                                        $tanggalKeluar = '-';
                                    }
                                @endphp

                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($row->tanggal_surat)->format('d M Y') }}</td>
                                    <td>{{ $row->asal_surat }}</td>
                                    <td>{{ $row->perihal }}</td>
                                    <td>{{ $tanggalKeluar }}</td> {{-- FIX: tanggal_balasan --}}
                                    <td>
                                        <span class="{{ $statusClass }}">{{ $statusText }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="row g-1 justify-content-center align-items-center mx-0">
                                            <div class="col-6 px-1">
                                                {{-- SURAT MASUK (HIJAU) --}}
                                                @if ($row->file_surat)
                                                    <a href="{{ asset('storage/' . $row->file_surat) }}"
                                                        class="btn btn-success-soft text-success rounded-circle border-0 p-1 shadow-sm"
                                                        title="Download Surat Masuk" style="width: 28px; height: 28px;">
                                                        <i class="bi bi-download fs-6"></i>
                                                    </a>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </div>
                                            <div class="col-6 px-1">
                                                {{-- SURAT KELUAR (MERAH) --}}
                                                @if ($row->file_balasan)
                                                    <a href="{{ asset('storage/' . $row->file_balasan) }}"
                                                        class="btn btn-danger-soft text-danger rounded-circle border-0 p-1 shadow-sm"
                                                        title="Download Surat Keluar" style="width: 28px; height: 28px;">
                                                        <i class="bi bi-download fs-6"></i>
                                                    </a>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        Tidak ada data untuk filter yang dipilih.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
@endsection
