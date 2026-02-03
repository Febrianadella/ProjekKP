<aside class="d-flex flex-column justify-content-between min-vh-100"
       style="width:260px;background:#FFFFFF;color:#0f172a;border-right:1px solid #e5e7eb;">

    {{-- TOP SECTION --}}
    <div>
        <div class="d-flex align-items-center gap-2 px-4 py-3 border-bottom">
            <div class="d-flex align-items-center justify-content-center rounded-circle"
                 style="width:40px;height:40px;background:#e5e7eb;">
                <i class="bi bi-person-circle text-secondary fs-3"></i>
            </div>
            <div>
                <div class="fw-semibold small text-primary">SIFORA</div>
                <div class="text-muted" style="font-size:.7rem;">Sistem Surat</div>
            </div>
        </div>

        <nav class="mt-3 px-3">
            @php
                $baseLink = 'd-flex align-items-center gap-2 px-3 py-2 mb-2 rounded-3 text-decoration-none small';
                $iconBox  = 'd-flex align-items-center justify-content-center rounded-3 me-1';
            @endphp

            @php $isDashboard = request()->routeIs('dashboard'); @endphp
            <a href="{{ route('dashboard') }}"
               class="{{ $baseLink }} {{ $isDashboard ? 'sidebar-active text-primary fw-semibold' : 'text-secondary hover-bg' }}">
                <div class="{{ $iconBox }}"
                     style="width:34px;height:34px;background:{{ $isDashboard ? '#EFF6FF' : '#E5E7EB' }};">
                    <i class="bi bi-grid-1x2-fill {{ $isDashboard ? 'text-primary' : 'text-secondary' }}"></i>
                </div>
                <span>Dashboard</span>
            </a>

            @php $isSurat = request()->routeIs('surat'); @endphp
            <a href="{{ route('surat') }}"
               class="{{ $baseLink }} {{ $isSurat ? 'sidebar-active text-primary fw-semibold' : 'text-secondary hover-bg' }}">
                <div class="{{ $iconBox }}"
                     style="width:34px;height:34px;background:{{ $isSurat ? '#EFF6FF' : '#E5E7EB' }};">
                    <i class="bi bi-envelope-fill {{ $isSurat ? 'text-primary' : 'text-secondary' }}"></i>
                </div>
                <span>Surat</span>
            </a>

            @php $isLaporan = request()->routeIs('laporan.*'); @endphp
            <a href="{{ route('laporan.index') }}"
               class="{{ $baseLink }} {{ $isLaporan ? 'sidebar-active text-primary fw-semibold' : 'text-secondary hover-bg' }}">
                <div class="{{ $iconBox }}"
                     style="width:34px;height:34px;background:{{ $isLaporan ? '#EFF6FF' : '#E5E7EB' }};">
                    <i class="bi bi-bar-chart-line-fill {{ $isLaporan ? 'text-primary' : 'text-secondary' }}"></i>
                </div>
                <span>Laporan</span>
            </a>

            @php $isProfil = request()->routeIs('profil'); @endphp
            <a href="{{ route('profil') }}"
               class="{{ $baseLink }} {{ $isProfil ? 'sidebar-active text-primary fw-semibold' : 'text-secondary hover-bg' }}">
                <div class="{{ $iconBox }}"
                     style="width:34px;height:34px;background:{{ $isProfil ? '#EFF6FF' : '#E5E7EB' }};">
                    <i class="bi bi-person-fill {{ $isProfil ? 'text-primary' : 'text-secondary' }}"></i>
                </div>
                <span>Profil</span>
            </a>
        </nav>
    </div>

    <div class="px-3 pb-3">
        <div class="d-flex align-items-center gap-2 px-3 py-2 mb-2 rounded-4"
             style="background:#F3F4F6;">
            <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white fw-semibold"
                 style="width:32px;height:32px;font-size:.85rem;">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div>
                <div class="small fw-semibold">{{ Auth::user()->name }}</div>
                <div class="text-muted" style="font-size:.7rem;">
                    {{ Auth::user()->role ?? 'Admin' }}
                </div>
            </div>
        </div>

        <form action="{{ route('logout') }}" method="POST" class="mt-1">
            @csrf
            <button type="submit"
                    class="w-100 d-flex align-items-center gap-2 px-3 py-2 rounded-3 border-0 bg-transparent text-danger small">
                <div class="d-flex align-items-center justify-content-center rounded-3 bg-danger bg-opacity-10"
                     style="width:32px;height:32px;">
                    <i class="bi bi-box-arrow-right"></i>
                </div>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>

<style>
    .hover-bg:hover { background:#F3F4F6; }
    .sidebar-active {
        background:#EFF6FF;
        box-shadow:0 0 0 1px rgba(37,99,235,.12),
                   0 4px 10px rgba(37,99,235,.18);
    }
</style>
