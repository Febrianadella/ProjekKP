@extends('layouts.app')

@section('title', 'Manajemen Surat')

@section('content')
    <div class="dashboard-wrapper">

        {{-- HEADER + SEARCH --}}
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
            <div>
                <h2 class="fw-semibold mb-1 fs-4">Manajemen Surat</h2>
                <p class="text-muted small mb-0">Kelola surat masuk dan surat keluar</p>
            </div>

            <div class="d-flex align-items-center gap-2">
                {{-- FORM PENCARIAN --}}
                <form method="GET" action="{{ route('surat') }}" class="d-flex align-items-center">
                    <input type="text" name="q" class="form-control me-2" style="min-width: 200px; max-width: 280px;"
                        placeholder="Cari surat..." value="{{ request('q') }}">
                    <button type="submit" class="btn btn-outline-primary me-1">
                        <i class="bi bi-search"></i>
                    </button>
                    @if (request('q'))
                        <a href="{{ route('surat') }}" class="btn btn-outline-secondary">
                            Reset
                        </a>
                    @endif
                </form>

                {{-- Tombol pemicu modal surat masuk (TAMBAH) - Hidden for Pimpinan --}}
                @if (auth()->user()->canModify())
                    <button type="button"
                        class="btn btn-primary d-flex align-items-center justify-content-center gap-2 px-3 px-sm-4"
                        data-bs-toggle="modal" data-bs-target="#modalSuratMasuk" data-mode="create">
                        <i class="bi bi-envelope-plus"></i>
                        <span class="d-none d-sm-inline">Tambah Surat Masuk</span>
                        <span class="d-sm-none">Tambah</span>
                    </button>
                @endif
            </div>
        </div>

        {{-- MODAL TAMBAH / EDIT SURAT MASUK --}}
        <div class="modal fade" id="modalSuratMasuk" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="titleSuratMasuk">Tambah Surat Masuk</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form id="formSuratMasuk" action="{{ route('surat-masuk.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        {{-- untuk edit kita set @method PUT lewat JS --}}
                        <input type="hidden" name="_method" id="methodSuratMasuk" value="POST">

                        <div class="modal-body">

                            <div class="mb-3">
                                <label class="form-label">Nomor Surat</label>
                                <input type="text" id="inputNoSuratMasuk" class="form-control bg-light"
                                    value="(Akan digenerate)" readonly disabled>
                                <small class="text-muted" id="noSuratHintMasuk">Nomor surat akan digenerate otomatis</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tanggal Surat</label>
                                <input type="date" name="tanggal_surat" id="inputTanggalSuratMasuk" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Asal Surat</label>
                                <input type="text" name="asal_surat" id="inputAsalSuratMasuk" class="form-control"
                                    placeholder="Nama instansi pengirim">
                            </div>

                            {{-- PERIHAL: SELECT + LAINNYA (SURAT MASUK) --}}
                            <div class="mb-3">
                                <label class="form-label">Perihal</label>
                                <select name="perihal" id="perihalSelectMasuk" class="form-select">
                                    <option value="" selected disabled>-- Pilih Perihal --</option>
                                    <option value="PKL">PKL</option>
                                    <option value="Kunjungan">Kunjungan</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>

                            <div class="mb-3 d-none" id="perihalLainnyaGroupMasuk">
                                <label class="form-label">Perihal Lainnya</label>
                                <input type="text" name="perihal_lainnya" id="inputPerihalLainnyaMasuk"
                                    class="form-control" placeholder="Isi perihal lainnya">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Upload File PDF</label>
                                <input type="file" name="file_surat" class="form-control" accept="application/pdf">
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" id="btnSubmitSuratMasuk">
                                Simpan
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        {{-- MODAL TAMBAH / EDIT BALASAN SURAT KELUAR --}}
        <div class="modal fade" id="modalSuratKeluar" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="titleSuratKeluar">Tambah Balasan Surat Keluar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form id="formSuratKeluar" action="{{ route('surat-keluar.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="surat_masuk_id" id="suratMasukId">
                        <input type="hidden" name="_method" id="methodSuratKeluar" value="POST">

                        <div class="modal-body">

                            <div class="mb-3">
                                <label class="form-label">Nomor Surat</label>
                                <input type="text" id="inputNoSuratKeluar" class="form-control bg-light"
                                    value="(Akan digenerate)" readonly disabled>
                                <small class="text-muted" id="noSuratHintKeluar">Nomor surat akan digenerate
                                    otomatis</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tanggal Surat</label>
                                <input type="date" name="tanggal_surat" id="inputTanggalSuratKeluar"
                                    class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tujuan Surat</label>
                                <input type="text" name="tujuan_surat" id="inputTujuanSuratKeluar"
                                    class="form-control" placeholder="Nama instansi tujuan">
                            </div>

                            {{-- PERIHAL: SELECT + LAINNYA (SURAT KELUAR) --}}
                            <div class="mb-3">
                                <label class="form-label">Perihal</label>
                                <select name="perihal" id="perihalSelectKeluar" class="form-select">
                                    <option value="" selected disabled>-- Pilih Perihal --</option>
                                    <option value="PKL">PKL</option>
                                    <option value="Kunjungan">Kunjungan</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>

                            <div class="mb-3 d-none" id="perihalLainnyaGroupKeluar">
                                <label class="form-label">Perihal Lainnya</label>
                                <input type="text" name="perihal_lainnya" id="inputPerihalLainnyaKeluar"
                                    class="form-control" placeholder="Isi perihal lainnya">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Upload File PDF</label>
                                <input type="file" name="file_balasan" class="form-control" accept="application/pdf">
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning text-white" id="btnSubmitSuratKeluar">
                                Simpan
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>



        {{-- LIST SURAT --}}
        @forelse($surat as $item)
            {{-- Kartu SURAT MASUK --}}
            <div class="card mb-3" style="background:#ffffff;">
                <div class="card-body p-0" style="background:#ffffff;">

                    {{-- BARIS ATAS: SURAT MASUK --}}
                    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom"
                        style="background:#ffffff;">
                        <div class="d-flex align-items-center gap-3">
                            {{-- ICON KIRI --}}
                            <div class="surat-icon bg-primary-soft text-primary">
                                <i class="bi bi-envelope-fill"></i>
                            </div>

                            {{-- INFO SURAT --}}
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge rounded-pill bg-primary text-white extra-small">
                                        SURAT MASUK
                                    </span>
                                    <span class="fw-semibold small">
                                        {{ $item->no_surat ?? 'SM/' . str_pad($item->id, 3, '0', STR_PAD_LEFT) . '/' . date('Y') }}
                                    </span>
                                </div>

                                <p class="mb-0 fw-semibold small">{{ $item->perihal }}</p>

                                <div class="text-muted extra-small mt-1 d-flex align-items-center gap-3">
                                    <span>
                                        <i class="bi bi-building me-1"></i>
                                        {{ $item->asal_surat }}
                                    </span>
                                    <span>
                                        <i class="bi bi-calendar-event me-1"></i>
                                        {{ optional($item->tanggal_surat)->format('d F Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- AKSI KANAN SURAT MASUK --}}
                        <div class="d-flex align-items-center gap-2">
                            @if ($item->file_surat)
                                <a href="{{ route('surat-masuk.download', $item->id) }}"
                                    class="btn btn-light btn-sm rounded-circle" title="Download Surat">
                                    <i class="bi bi-download"></i>
                                </a>
                            @endif

                            @if (auth()->user()->canModify())
                                {{-- EDIT SURAT MASUK: buka modalSuratMasuk, isi data via JS --}}
                                <button type="button" class="btn btn-light btn-sm rounded-circle" title="Edit"
                                    data-bs-toggle="modal" data-bs-target="#modalSuratMasuk" data-mode="edit"
                                    data-id="{{ $item->id }}" data-no_surat="{{ $item->no_surat }}"
                                    data-tanggal_surat="{{ optional($item->tanggal_surat)->format('Y-m-d') }}"
                                    data-asal_surat="{{ $item->asal_surat }}" data-perihal="{{ $item->perihal }}"
                                    data-perihal_lainnya="{{ $item->perihal_lainnya }}">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <form action="{{ route('surat-resource.destroy', $item->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus surat ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-light btn-sm rounded-circle text-danger"
                                        title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    {{-- BARIS BAWAH: BALASAN / STATUS --}}
                    <div class="px-4 py-3" style="background:#ffffff;">

                        @if ($item->file_balasan)
                            {{-- BALASAN SURAT KELUAR --}}
                            <div class="card border-0 mb-2" style="background:#fff7e6;">
                                <div class="card-body py-3 px-3 d-flex justify-content-between align-items-start">
                                    <div class="d-flex gap-3">
                                        <div class="surat-icon bg-warning-soft text-warning">
                                            <i class="bi bi-send-fill"></i>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <span class="badge rounded-pill bg-warning text-dark extra-small">
                                                    SURAT KELUAR
                                                </span>
                                                <span class="fw-semibold small">
                                                    {{ $item->no_surat_balasan ?? 'SK/' . str_pad($item->id, 3, '0', STR_PAD_LEFT) . '/' . date('Y') }}
                                                </span>
                                            </div>
                                            <p class="mb-0 fw-semibold small">
                                                {{ $item->perihal_balasan ?? 'Balasan: ' . $item->perihal }}
                                            </p>
                                            <div class="text-muted extra-small mt-1 d-flex align-items-center gap-3">
                                                <span>
                                                    <i class="bi bi-building me-1"></i>
                                                    {{ $item->tujuan_surat ?? $item->asal_surat }}
                                                </span>
                                                <span>
                                                    <i class="bi bi-calendar-event me-1"></i>
                                                    {{ optional($item->tanggal_balasan ?? $item->updated_at)->format('d F Y') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- AKSI KANAN BALASAN --}}
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ route('surat-keluar.download', $item->id) }}"
                                            class="btn btn-light btn-sm rounded-circle" title="Download Balasan">
                                            <i class="bi bi-download"></i>
                                        </a>

                                        @if (auth()->user()->canModify())
                                            {{-- EDIT BALASAN: buka modalSuratKeluar, isi data via JS --}}
                                            <button type="button" class="btn btn-light btn-sm rounded-circle"
                                                title="Edit Balasan" data-bs-toggle="modal"
                                                data-bs-target="#modalSuratKeluar" data-ref="{{ $item->id }}"
                                                data-no_surat="{{ $item->no_surat_balasan }}"
                                                data-tanggal_surat="{{ optional($item->tanggal_balasan)->format('Y-m-d') }}"
                                                data-tujuan_surat="{{ $item->tujuan_surat }}"
                                                data-perihal="{{ $item->perihal_balasan }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>

                                            <form action="{{ route('surat-keluar.balasan.destroy', $item->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus balasan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-light btn-sm rounded-circle text-danger"
                                                    title="Hapus Balasan">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-1 small text-success">
                                <i class="bi bi-check2-circle fs-6"></i>
                                <span>Sudah Dibalas</span>
                            </div>
                        @else
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-1 small text-muted">
                                    <i class="bi bi-envelope-open fs-6"></i>
                                    <span>Belum Dibalas</span>
                                </div>

                                @if (auth()->user()->canModify())
                                    <a href="#" class="small fw-semibold text-success text-decoration-none"
                                        data-bs-toggle="modal" data-bs-target="#modalSuratKeluar"
                                        data-ref="{{ $item->id }}">
                                        + Tambah Balasan
                                    </a>
                                @endif

                            </div>
                        @endif
                    </div>

                </div>
            </div>
        @empty
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body text-center text-muted small">
                    Belum ada data surat.
                </div>
            </div>
        @endforelse
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ==== MODAL SURAT KELUAR (TAMBAH / EDIT BALASAN) ====
            var modalKeluar = document.getElementById('modalSuratKeluar');

            // Store the last trigger button for reference
            var lastTriggerKeluar = null;

            // Capture click on edit buttons to store trigger data
            document.querySelectorAll('[data-bs-target="#modalSuratKeluar"]').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    lastTriggerKeluar = this;
                });
            });

            // Function to populate modal fields
            function populateModalKeluar(trigger) {
                if (!trigger) return;

                var refId = trigger.getAttribute('data-ref');
                var noSurat = trigger.getAttribute('data-no_surat');
                var tglSurat = trigger.getAttribute('data-tanggal_surat');
                var tujuan = trigger.getAttribute('data-tujuan_surat');
                var perihal = trigger.getAttribute('data-perihal');

                console.log('Populating modal with:', {
                    refId,
                    noSurat,
                    tglSurat,
                    tujuan,
                    perihal
                });

                document.getElementById('suratMasukId').value = refId || '';

                var form = document.getElementById('formSuratKeluar');
                var method = document.getElementById('methodSuratKeluar');
                var title = document.getElementById('titleSuratKeluar');
                var btnSubmit = document.getElementById('btnSubmitSuratKeluar');

                // Reset fields first
                document.getElementById('inputNoSuratKeluar').value = '(Akan digenerate)';
                document.getElementById('noSuratHintKeluar').textContent = 'Nomor surat akan digenerate otomatis';
                document.getElementById('inputTanggalSuratKeluar').value = '';
                document.getElementById('inputTujuanSuratKeluar').value = '';
                document.getElementById('perihalSelectKeluar').value = '';
                document.getElementById('inputPerihalLainnyaKeluar').value = '';
                document.getElementById('perihalLainnyaGroupKeluar').classList.add('d-none');

                // Default mode (tambah)
                form.action = "{{ route('surat-keluar.store') }}";
                method.value = "POST";
                title.textContent = 'Tambah Balasan Surat Keluar';
                btnSubmit.textContent = 'Simpan';

                // Check if this is edit mode
                if (noSurat || tglSurat || tujuan || perihal) {
                    // Mode EDIT
                    title.textContent = 'Edit Balasan Surat Keluar';
                    btnSubmit.textContent = 'Update';
                    method.value = "PUT";
                    form.action = "{{ url('/surat-keluar') }}/" + refId;

                    // Show actual no_surat for edit mode
                    document.getElementById('inputNoSuratKeluar').value = noSurat || '-';
                    document.getElementById('noSuratHintKeluar').textContent = '';
                    document.getElementById('inputTanggalSuratKeluar').value = tglSurat || '';
                    document.getElementById('inputTujuanSuratKeluar').value = tujuan || '';

                    var select = document.getElementById('perihalSelectKeluar');
                    if (perihal === 'PKL' || perihal === 'Kunjungan') {
                        select.value = perihal;
                    } else if (perihal) {
                        select.value = 'lainnya';
                        document.getElementById('perihalLainnyaGroupKeluar').classList.remove('d-none');
                        document.getElementById('inputPerihalLainnyaKeluar').value = perihal;
                    }
                }
            }

            // Use shown.bs.modal (after modal is fully visible) for more reliable timing
            modalKeluar.addEventListener('shown.bs.modal', function(event) {
                var trigger = event.relatedTarget || lastTriggerKeluar;
                populateModalKeluar(trigger);
            });

            // Also try on show.bs.modal as backup
            modalKeluar.addEventListener('show.bs.modal', function(event) {
                var trigger = event.relatedTarget || lastTriggerKeluar;
                // Set a small timeout to ensure modal is ready
                setTimeout(function() {
                    populateModalKeluar(trigger);
                }, 50);
            });

            // ==== MODAL SURAT MASUK (TAMBAH / EDIT) ====
            var modalMasuk = document.getElementById('modalSuratMasuk');
            modalMasuk.addEventListener('show.bs.modal', function(event) {
                var trigger = event.relatedTarget;
                if (!trigger) return;

                var mode = trigger.getAttribute('data-mode') || 'create';

                var form = document.getElementById('formSuratMasuk');
                var method = document.getElementById('methodSuratMasuk');
                var title = document.getElementById('titleSuratMasuk');
                var btnSubmit = document.getElementById('btnSubmitSuratMasuk');

                // reset nilai dulu
                document.getElementById('inputNoSuratMasuk').value = '';
                document.getElementById('inputTanggalSuratMasuk').value = '';
                document.getElementById('inputAsalSuratMasuk').value = '';
                document.getElementById('perihalSelectMasuk').value = '';
                document.getElementById('inputPerihalLainnyaMasuk').value = '';
                document.getElementById('perihalLainnyaGroupMasuk').classList.add('d-none');

                if (mode === 'edit') {
                    // MODE EDIT SURAT MASUK
                    var id = trigger.getAttribute('data-id');
                    var noSurat = trigger.getAttribute('data-no_surat');
                    var tglSurat = trigger.getAttribute('data-tanggal_surat');
                    var asal = trigger.getAttribute('data-asal_surat');
                    var perihal = trigger.getAttribute('data-perihal');
                    var perihalL = trigger.getAttribute('data-perihal_lainnya');

                    title.textContent = 'Edit Surat Masuk';
                    btnSubmit.textContent = 'Update';
                    method.value = "PUT";
                    form.action = "{{ url('/surat-masuk') }}/" + id; // route surat-masuk.update

                    // Show actual no_surat
                    document.getElementById('inputNoSuratMasuk').value = noSurat || '-';
                    document.getElementById('noSuratHintMasuk').textContent = '';
                    document.getElementById('inputTanggalSuratMasuk').value = tglSurat || '';
                    document.getElementById('inputAsalSuratMasuk').value = asal || '';

                    var select = document.getElementById('perihalSelectMasuk');
                    if (perihal === 'PKL' || perihal === 'Kunjungan' || perihal === 'lainnya') {
                        select.value = perihal;
                    } else if (perihal) {
                        select.value = 'lainnya';
                        document.getElementById('perihalLainnyaGroupMasuk').classList.remove('d-none');
                        document.getElementById('inputPerihalLainnyaMasuk').value = perihalL || perihal;
                    }

                } else {
                    // MODE TAMBAH
                    title.textContent = 'Tambah Surat Masuk';
                    btnSubmit.textContent = 'Simpan';
                    method.value = "POST";
                    form.action = "{{ route('surat-masuk.store') }}";

                    // Reset no_surat to placeholder
                    document.getElementById('inputNoSuratMasuk').value = '(Akan digenerate)';
                    document.getElementById('noSuratHintMasuk').textContent =
                        'Nomor surat akan digenerate otomatis';
                }
            });

            // handle perihal lainnya surat masuk
            const selectPerihalMasuk = document.getElementById('perihalSelectMasuk');
            const groupLainnyaMasuk = document.getElementById('perihalLainnyaGroupMasuk');

            if (selectPerihalMasuk) {
                selectPerihalMasuk.addEventListener('change', function() {
                    if (this.value === 'lainnya') {
                        groupLainnyaMasuk.classList.remove('d-none');
                    } else {
                        groupLainnyaMasuk.classList.add('d-none');
                    }
                });
            }

            // handle perihal lainnya surat keluar
            const selectPerihalKeluar = document.getElementById('perihalSelectKeluar');
            const groupLainnyaKeluar = document.getElementById('perihalLainnyaGroupKeluar');

            if (selectPerihalKeluar) {
                selectPerihalKeluar.addEventListener('change', function() {
                    if (this.value === 'lainnya') {
                        groupLainnyaKeluar.classList.remove('d-none');
                    } else {
                        groupLainnyaKeluar.classList.add('d-none');
                    }
                });
            }
        });
    </script>
@endpush
