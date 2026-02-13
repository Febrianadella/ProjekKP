@extends('layouts.app')

@section('title', 'Laporan Surat')

@section('content')
    <div class="dashboard-wrapper">

        {{-- HEADER --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-end gap-2 mb-4">
            <div>
                <h2 class="fw-semibold mb-1">Laporan Surat</h2>
                <p class="text-muted mb-0 small">Analisis dan ringkasan data surat.</p>
            </div>
            <div class="d-flex flex-column flex-sm-row gap-2 w-100 w-md-auto">
                <button type="button"
                    class="btn btn-danger d-flex align-items-center justify-content-center gap-2 btn-sm w-100 w-md-auto"
                    data-bs-toggle="modal" data-bs-target="#filePreviewModal"
                    data-preview-url="{{ route('laporan.export.pdf.preview', request()->query()) }}"
                    data-download-url="{{ route('laporan.export.pdf', request()->query()) }}"
                    data-title="Preview Laporan PDF" data-ext="pdf">
                    <i class="bi bi-filetype-pdf"></i>
                    Export PDF
                </button>
                <button type="button"
                    class="btn btn-success d-flex align-items-center justify-content-center gap-2 btn-sm w-100 w-md-auto"
                    data-bs-toggle="modal" data-bs-target="#filePreviewModal"
                    data-preview-url=""
                    data-download-url="{{ route('laporan.export.excel', request()->query()) }}"
                    data-title="Export Excel" data-ext="xlsx">
                    <i class="bi bi-file-earmark-spreadsheet"></i>
                    Export Excel
                </button>
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

                {{-- TABEL LAPORAN (DESKTOP) --}}
                <div class="table-responsive d-none d-md-block">
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
                                                    <button type="button"
                                                        class="btn btn-success-soft text-success rounded-circle border-0 p-1 shadow-sm file-btn"
                                                        data-bs-toggle="modal" data-bs-target="#filePreviewModal"
                                                        data-preview-url="{{ route('surat-masuk.preview', $row->id) }}"
                                                        data-download-url="{{ route('surat-masuk.download', $row->id) }}"
                                                        data-title="Preview Surat Masuk"
                                                        data-ext="{{ strtolower(pathinfo($row->file_surat, PATHINFO_EXTENSION)) }}"
                                                        title="Lihat Surat Masuk">
                                                        <i class="bi bi-download fs-6"></i>
                                                    </button>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </div>
                                            <div class="col-6 px-1">
                                                {{-- SURAT KELUAR (MERAH) --}}
                                                @if ($row->file_balasan)
                                                    <button type="button"
                                                        class="btn btn-danger-soft text-danger rounded-circle border-0 p-1 shadow-sm file-btn"
                                                        data-bs-toggle="modal" data-bs-target="#filePreviewModal"
                                                        data-preview-url="{{ route('surat-keluar.preview', $row->id) }}"
                                                        data-download-url="{{ route('surat-keluar.download', $row->id) }}"
                                                        data-title="Preview Surat Keluar"
                                                        data-ext="{{ strtolower(pathinfo($row->file_balasan, PATHINFO_EXTENSION)) }}"
                                                        title="Lihat Surat Keluar">
                                                        <i class="bi bi-download fs-6"></i>
                                                    </button>
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

                {{-- LIST LAPORAN (MOBILE) --}}
                <div class="d-block d-md-none">
                    @forelse($surat as $index => $row)
                        @php
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

                        <div class="card border-0 shadow-sm mb-2 laporan-card">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                    <div>
                                        <div class="fw-semibold">{{ $row->perihal }}</div>
                                        <div class="small text-muted">{{ $row->asal_surat }}</div>
                                    </div>
                                    <span class="{{ $statusClass }}">{{ $statusText }}</span>
                                </div>

                                <div class="small text-muted d-flex flex-column gap-1">
                                    <div>
                                        <i class="bi bi-calendar-event me-1"></i>
                                        Masuk: {{ \Carbon\Carbon::parse($row->tanggal_surat)->format('d M Y') }}
                                    </div>
                                    <div>
                                        <i class="bi bi-send me-1"></i>
                                        Keluar: {{ $tanggalKeluar }}
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-2 mt-3">
                                    @if ($row->file_surat)
                                        <button type="button"
                                            class="btn btn-success-soft text-success rounded-circle border-0 p-1 shadow-sm file-btn"
                                            data-bs-toggle="modal" data-bs-target="#filePreviewModal"
                                            data-preview-url="{{ route('surat-masuk.preview', $row->id) }}"
                                            data-download-url="{{ route('surat-masuk.download', $row->id) }}"
                                            data-title="Preview Surat Masuk"
                                            data-ext="{{ strtolower(pathinfo($row->file_surat, PATHINFO_EXTENSION)) }}"
                                            title="Lihat Surat Masuk">
                                            <i class="bi bi-download fs-6"></i>
                                        </button>
                                        <span class="small text-muted">Surat Masuk</span>
                                    @else
                                        <span class="small text-muted">Surat Masuk: -</span>
                                    @endif
                                </div>

                                <div class="d-flex align-items-center gap-2 mt-2">
                                    @if ($row->file_balasan)
                                        <button type="button"
                                            class="btn btn-danger-soft text-danger rounded-circle border-0 p-1 shadow-sm file-btn"
                                            data-bs-toggle="modal" data-bs-target="#filePreviewModal"
                                            data-preview-url="{{ route('surat-keluar.preview', $row->id) }}"
                                            data-download-url="{{ route('surat-keluar.download', $row->id) }}"
                                            data-title="Preview Surat Keluar"
                                            data-ext="{{ strtolower(pathinfo($row->file_balasan, PATHINFO_EXTENSION)) }}"
                                            title="Lihat Surat Keluar">
                                            <i class="bi bi-download fs-6"></i>
                                        </button>
                                        <span class="small text-muted">Surat Keluar</span>
                                    @else
                                        <span class="small text-muted">Surat Keluar: -</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center text-muted small py-4">
                                Tidak ada data untuk filter yang dipilih.
                            </div>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>

    {{-- MODAL PREVIEW FILE --}}
    <div class="modal fade" id="filePreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filePreviewTitle">Preview File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <iframe id="filePreviewFrame" class="w-100 border-0 d-none" style="height:70vh;"></iframe>
                    <div id="filePreviewFallback" class="small text-muted d-none">
                        Preview untuk file ini belum didukung. Silakan download untuk melihat isinya.
                    </div>
                </div>
                <div class="modal-footer">
                    <a id="filePreviewDownload" href="#" class="btn btn-primary">
                        Download
                    </a>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var previewModal = document.getElementById('filePreviewModal');
            if (!previewModal) return;

            previewModal.addEventListener('show.bs.modal', function(event) {
                var trigger = event.relatedTarget;
                if (!trigger) return;

                var previewUrl = trigger.getAttribute('data-preview-url') || '';
                var downloadUrl = trigger.getAttribute('data-download-url') || previewUrl;
                var title = trigger.getAttribute('data-title') || 'Preview File';
                var ext = (trigger.getAttribute('data-ext') || '').toLowerCase();

                var titleEl = document.getElementById('filePreviewTitle');
                var frame = document.getElementById('filePreviewFrame');
                var fallback = document.getElementById('filePreviewFallback');
                var downloadBtn = document.getElementById('filePreviewDownload');

                titleEl.textContent = title;
                downloadBtn.href = downloadUrl;

                // Reset state
                frame.src = '';
                frame.classList.add('d-none');
                fallback.classList.add('d-none');

                if (ext === 'pdf') {
                    frame.src = previewUrl;
                    frame.classList.remove('d-none');
                } else {
                    fallback.classList.remove('d-none');
                }
            });
        });
    </script>
@endpush