<footer class="py-4" style="background:#1d4ed8;border-top:1px solid rgba(15,23,42,.8);">

    <div class="px-4 container-fluid text-light">

        <div class="mb-3 row">
            {{-- Kiri: logo + deskripsi --}}
            <div class="mb-3 col-md-5 mb-md-0">
                <div class="gap-2 mb-2 d-flex align-items-center">
                    <div class="d-flex align-items-center justify-content-center rounded-3"
                        style="width:40px;height:40px;background:#2563EB;">
                        <i class="text-white bi bi-envelope-fill fs-5"></i>
                    </div>
                    <div>
                        <div class="text-white fw-semibold">SIFORA</div>
                        <div class="small text-white-75">Sistem Informasi Persuratan</div>
                    </div>
                </div>
                <p class="mb-0 small text-white-75">
                    Kelompok Kerja Pelayanan Publik BBPBL Lampung - Sistem pengelolaan surat digital yang efisien dan
                    profesional.
                </p>
            </div>

            {{-- Tengah: menu cepat --}}
            <div class="mb-3 col-md-3 mb-md-0">
                <h6 class="mb-2 text-white">Menu Cepat</h6>
                <ul class="mb-0 list-unstyled small">
                    <li class="mb-1">
                        <a href="#fitur" class="footer-link">Fitur</a>
                    </li>
                    <li class="mb-1">
                        <a href="{{ route('login') }}" class="footer-link">Login</a>
                    </li>
                    <li class="mb-1">
                        <a href="{{ route('register') }}" class="footer-link">Registrasi</a>
                    </li>
                </ul>
            </div>

            {{-- Kanan: kontak --}}
            <div class="col-md-4">
                <h6 class="mb-2 text-white">Kontak</h6>
                <ul class="mb-0 list-unstyled small">
                    <li class="mb-1">
                        <i class="text-white bi bi-geo-alt me-2"></i>
                        <span class="text-white">Lampung, Indonesia</span>
                    </li>
                    <li class="mb-1 d-flex align-items-center">
                        <i class="text-white bi bi-envelope me-2"></i>
                        <a href="mailto:info@bbpbl.lampung.go.id" class="footer-link">
                            info@bbpbl.lampung.go.id
                        </a>
                    </li>
                    <li class="mb-1">
                        <i class="text-white bi bi-telephone me-2"></i>
                        <span class="text-white">(62 811-7257-770)</span>
                    </li>
                </ul>
            </div>
        </div>

        <hr class="border-opacity-25 border-light">

        <div class="d-flex justify-content-between align-items-center small text-white-75">
            <span>© {{ date('Y') }} Balai Besar Perikanan Budidaya Laut Lampung.</span>
        </div>
    </div>
</footer>

<style>
    .footer-link {
        color: #ffffff;
        text-decoration: none;
    }

    .footer-link:hover {
        text-decoration: underline;
    }
</style>
