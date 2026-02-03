@extends('layouts.app')

@section('title', 'Profil')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="row g-0">

                        {{-- KOLOM KIRI: AVATAR + INFO RINGKAS --}}
                        <div class="col-md-4 d-flex flex-column justify-content-between bg-white border-end">
                            <div class="p-4 text-center">
                                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-primary text-white fw-semibold"
                                    style="width:90px;height:90px;font-size:2.4rem;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <h5 class="mb-1">{{ $user->name }}</h5>
                                <p class="small mb-2 text-muted">{{ $user->email }}</p>
                                <span class="badge bg-primary-soft text-primary small">
                                    Pengguna Aktif
                                </span>
                            </div>

                            <div class="px-4 pb-3 small text-muted bg-light-subtle">
                                <div class="d-flex justify-content-between">
                                    <span class="extra-small">Terakhir diubah</span>
                                    <span class="extra-small">
                                        {{ optional($user->updated_at)->timezone(config('app.timezone'))->translatedFormat('d M Y, H:i') ?? '-' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- KOLOM KANAN: FORM DETAIL --}}
                        <div class="col-md-8">
                            <div class="card-body p-4">
                                <h6 class="mb-3">Detail Profil</h6>

                                <form method="POST" action="{{ route('profil.update') }}" id="profileForm">
                                    @csrf

                                    <div class="mb-3">
                                        <label class="form-label small">Nama Lengkap</label>
                                        <input type="text" name="name" class="form-control form-control-sm"
                                            value="{{ old('name', $user->name) }}" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small">Email</label>
                                        <input type="email" name="email" class="form-control form-control-sm"
                                            value="{{ old('email', $user->email) }}" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small">No. HP</label>
                                        <input type="text" name="phone" class="form-control form-control-sm"
                                            value="{{ old('phone', $user->phone) }}" readonly>
                                    </div>

                                    <p class="text-muted extra-small mb-0">
                                        Data ini digunakan untuk identitas pengguna di SIFORA.
                                    </p>

                                    <div class="d-flex justify-content-end gap-2 mt-4">
                                        <button type="button"
                                            class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1"
                                            id="btnEdit">
                                            <i class="bi bi-pencil"></i>
                                            <span>Edit Profil</span>
                                        </button>

                                        <button type="submit"
                                            class="btn btn-primary btn-sm d-flex align-items-center gap-1 d-none"
                                            id="btnSelesai">
                                            <i class="bi bi-check-circle"></i>
                                            <span>Selesai</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- POPUP SUKSES --}}
    @if (session('success'))
        <div class="modal fade show" id="successModal" tabindex="-1" style="display:block;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-body text-center p-5">
                        <i class="bi bi-check-circle-fill text-success fs-1 mb-3"></i>
                        <h5 class="mb-2">Profil Berhasil Diubah</h5>
                        <p class="text-muted small mb-4">
                            Perubahan profil kamu telah berhasil disimpan.
                        </p>
                        <button class="btn btn-primary btn-sm" onclick="closeModal()">OK</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-backdrop fade show"></div>
    @endif
@endsection

@push('scripts')
    <script>
        const btnEdit = document.getElementById('btnEdit');
        const btnSelesai = document.getElementById('btnSelesai');
        const inputs = document.querySelectorAll('#profileForm input');

        // Aktifkan mode edit
        if (btnEdit) {
            btnEdit.addEventListener('click', function() {
                inputs.forEach(input => input.removeAttribute('readonly'));
                btnEdit.classList.add('d-none');
                btnSelesai.classList.remove('d-none');
            });
        }

        // Tutup modal sukses + reload supaya updated_at dan data baru tampil
        function closeModal() {
            document.getElementById('successModal')?.remove();
            document.querySelector('.modal-backdrop')?.remove();
            window.location.reload();
        }
    </script>
@endpush
