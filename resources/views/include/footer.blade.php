<footer class="py-4"
        style="background:#1d4ed8;border-top:1px solid rgba(15,23,42,.8);">

    <div class="container-fluid px-4 text-light">

        <div class="row mb-3">
            {{-- Kiri: logo + deskripsi --}}
            <div class="col-md-5 mb-3 mb-md-0">
                <div class="d-flex align-items-center mb-2 gap-2">
                    <div class="d-flex align-items-center justify-content-center rounded-3"
                         style="width:40px;height:40px;background:#2563EB;">
                        <i class="bi bi-envelope-fill text-white fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-semibold text-white">SIFORA</div>
                        <div class="small text-white-75">Sistem Informasi Persuratan</div>
                    </div>
                </div>
                <p class="small text-white-75 mb-0">
                    Balai Besar Perikanan Budidaya Laut Lampung - Sistem pengelolaan surat digital yang efisien dan profesional.
                </p>
            </div>

            {{-- Tengah: menu cepat --}}
            <div class="col-md-3 mb-3 mb-md-0">
                <h6 class="text-white mb-2">Menu Cepat</h6>
                <ul class="list-unstyled small mb-0">
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
                <h6 class="text-white mb-2">Kontak</h6>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-1">
                        <i class="bi bi-geo-alt me-2 text-white"></i>
                        <span class="text-white">Lampung, Indonesia</span>
                    </li>
                    <li class="mb-1 d-flex align-items-center">
                        <i class="bi bi-envelope me-2 text-white"></i>
                        <a href="mailto:info@bbpbl.lampung.go.id"
                           class="footer-link">
                            info@bbpbl.lampung.go.id
                        </a>
                    </li>
                    <li class="mb-1">
                        <i class="bi bi-telephone me-2 text-white"></i>
                        <span class="text-white">(62 811-7257-770)</span>
                    </li>
                </ul>
            </div>
        </div>

        <hr class="border-light border-opacity-25">

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
