<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Dashboard')</title>

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    {{-- Vite --}}
    @vite(['resources/js/app.js'])

    <style>
        /* ====== MOBILE RESPONSIVE STYLES ====== */

        /* Mobile menu toggle button */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #0f172a;
            cursor: pointer;
            padding: 8px;
        }

        /* Sidebar overlay for mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 998;
        }

        /* Sidebar styles */
        .sidebar {
            width: 260px;
            background: #FFFFFF;
            color: #0f172a;
            min-height: 100vh;
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 999;
            transition: transform 0.3s ease;
        }

        /* Main content area */
        .main-content {
            margin-left: 260px;
            transition: margin-left 0.3s ease;
        }

        /* Mobile responsive breakpoint */
        @media (max-width: 991.98px) {
            .mobile-menu-btn {
                display: block;
            }

            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .sidebar-overlay.show {
                display: block;
            }

            .main-content {
                margin-left: 0;
            }

            /* Improve table scrolling on mobile */
            .table-responsive {
                -webkit-overflow-scrolling: touch;
            }

            /* Adjust container padding on mobile */
            .container-fluid {
                padding-left: 12px;
                padding-right: 12px;
            }

            /* Hide some text on very small screens */
            .hide-mobile {
                display: none !important;
            }
        }

        /* Mobile stat cards - keep horizontal but smaller */
        @media (max-width: 767.98px) {
            .stat-card-lg {
                padding: 12px 14px;
                min-height: 80px;
            }

            .stat-card-lg .stat-icon {
                width: 40px;
                height: 40px;
                font-size: 16px;
                margin-right: 10px !important;
            }

            .stat-card-lg h4 {
                font-size: 1.25rem;
            }

            .stat-card-lg .stat-label {
                font-size: 0.65rem;
            }

            .stat-card-lg .stat-sub {
                font-size: 0.6rem;
            }

            .stat-card-sm {
                padding: 10px 12px;
                height: auto;
                min-height: 60px;
            }

            .stat-card-sm .stat-icon {
                width: 28px;
                height: 28px;
                font-size: 14px;
                margin-right: 8px !important;
            }

            .stat-card-sm h5 {
                font-size: 0.95rem;
            }
        }

        /* Extra small screens */
        @media (max-width: 575.98px) {
            header h1 {
                font-size: 1rem;
            }

            .stat-card-lg {
                padding: 10px 12px;
                min-height: 70px;
            }

            .stat-card-lg h4 {
                font-size: 1.1rem;
            }

            .stat-card-sm {
                padding: 8px 10px;
            }

            /* Dashboard header responsive */
            .dashboard-wrapper h2 {
                font-size: 1.25rem;
            }
        }

        /* ====== EXISTING STYLES ====== */

        .hover-bg:hover {
            background: #F3F4F6;
        }

        .sidebar-active {
            background: #EFF6FF;
            box-shadow:
                0 0 0 1px rgba(37, 99, 235, 0.12),
                0 4px 10px rgba(37, 99, 235, 0.18);
        }

        /* ====== STAT CARDS DASHBOARD ====== */

        .stat-card {
            background: #ffffff;
            border-radius: 18px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
            display: flex;
        }

        /* card besar (baris atas) */
        .stat-card-lg {
            align-items: center;
            gap: 12px;
            padding: 18px 22px;
            min-height: 110px;
        }

        /* card kecil (baris bawah) */
        .stat-card-sm {
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            min-height: 0;
            height: 72px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
            border-radius: 14px;
        }

        .stat-icon {
            width: 34px;
            height: 34px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .stat-label {
            letter-spacing: .05em;
        }

        .extra-small {
            font-size: .7rem;
        }

        /* ====== OVERRIDE UNTUK CARD KECIL ====== */

        .stat-card-sm .stat-icon {
            width: 26px;
            height: 26px;
            font-size: 14px;
        }

        .stat-card-sm .stat-label {
            font-size: .7rem;
        }

        .stat-card-sm h5 {
            font-size: 1rem;
            margin-bottom: 0;
        }

        .stat-card-sm .stat-sub,
        .stat-card-sm .extra-small {
            font-size: .65rem;
        }

        /* Close button for mobile sidebar */
        .sidebar-close {
            display: none;
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #64748b;
            cursor: pointer;
        }

        @media (max-width: 991.98px) {
            .sidebar-close {
                display: block;
            }
        }
    </style>
</head>

<body class="bg-light">

    {{-- Mobile Sidebar Overlay --}}
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <div class="d-flex min-vh-100">

        {{-- SIDEBAR --}}
        <nav class="sidebar d-flex flex-column" id="sidebar">

            {{-- Close button for mobile --}}
            <button class="sidebar-close" onclick="closeSidebar()">
                <i class="bi bi-x-lg"></i>
            </button>

            {{-- Top logo + menu --}}
            <div class="flex-grow-1 d-flex flex-column">
                {{-- LOGO --}}
                <div class="d-flex align-items-center gap-2 px-4 py-3 border-bottom">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-primary text-white"
                        style="width:40px;height:40px;">
                        <i class="bi bi-envelope-fill fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-semibold small text-primary">SIFORA</div>
                        <div class="text-muted" style="font-size:.7rem;">Sistem Informasi Persuratan</div>
                    </div>
                </div>

                {{-- MENU --}}
                <ul class="nav flex-column px-3 pt-3 pb-1 small">

                    @php
                        $baseLink = 'd-flex align-items-center gap-2 px-3 py-2 rounded-3 text-decoration-none';
                        $iconBox = 'd-flex align-items-center justify-content-center rounded-3 me-1';
                    @endphp

                    {{-- Dashboard --}}
                    @php $isDashboard = request()->routeIs('dashboard'); @endphp
                    <li class="nav-item mb-1">
                        <a href="{{ route('dashboard') }}"
                            class="{{ $baseLink }} {{ $isDashboard ? 'sidebar-active text-primary fw-semibold' : 'text-secondary hover-bg' }}">
                            <div class="{{ $iconBox }}"
                                style="width:32px;height:32px;background:{{ $isDashboard ? '#EFF6FF' : '#E5E7EB' }};">
                                <i
                                    class="bi bi-grid-1x2-fill {{ $isDashboard ? 'text-primary' : 'text-secondary' }}"></i>
                            </div>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    {{-- Surat --}}
                    @php $isSurat = request()->routeIs('surat'); @endphp
                    <li class="nav-item mb-1">
                        <a href="{{ route('surat') }}"
                            class="{{ $baseLink }} {{ $isSurat ? 'sidebar-active text-primary fw-semibold' : 'text-secondary hover-bg' }}">
                            <div class="{{ $iconBox }}"
                                style="width:32px;height:32px;background:{{ $isSurat ? '#EFF6FF' : '#E5E7EB' }};">
                                <i class="bi bi-envelope-fill {{ $isSurat ? 'text-primary' : 'text-secondary' }}"></i>
                            </div>
                            <span>Surat</span>
                        </a>
                    </li>

                    {{-- Laporan --}}
                    @php $isLaporan = request()->routeIs('laporan.*'); @endphp
                    <li class="nav-item mb-1">
                        <a href="{{ route('laporan.index') }}"
                            class="{{ $baseLink }} {{ $isLaporan ? 'sidebar-active text-primary fw-semibold' : 'text-secondary hover-bg' }}">
                            <div class="{{ $iconBox }}"
                                style="width:32px;height:32px;background:{{ $isLaporan ? '#EFF6FF' : '#E5E7EB' }};">
                                <i
                                    class="bi bi-bar-chart-line-fill {{ $isLaporan ? 'text-primary' : 'text-secondary' }}"></i>
                            </div>
                            <span>Laporan</span>
                        </a>
                    </li>

                    {{-- Profil --}}
                    @php $isProfil = request()->routeIs('profil'); @endphp
                    <li class="nav-item mb-1">
                        <a href="{{ route('profil') }}"
                            class="{{ $baseLink }} {{ $isProfil ? 'sidebar-active text-primary fw-semibold' : 'text-secondary hover-bg' }}">
                            <div class="{{ $iconBox }}"
                                style="width:32px;height:32px;background:{{ $isProfil ? '#EFF6FF' : '#E5E7EB' }};">
                                <i class="bi bi-person-circle {{ $isProfil ? 'text-primary' : 'text-secondary' }}"></i>
                            </div>
                            <span>Profil</span>
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Bottom user + logout --}}
            <div class="px-3 pb-3 small">
                @auth
                    <div class="d-flex align-items-center gap-2 px-3 py-2 mb-2 rounded-4" style="background:#F3F4F6;">
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
                @endauth

                <form action="{{ route('logout') }}" method="POST" class="mt-1">
                    @csrf
                    <button type="submit"
                        class="w-100 d-flex align-items-center gap-2 px-3 py-2 rounded-3 border-0 bg-transparent text-danger">
                        <div class="d-flex align-items-center justify-content-center rounded-3 bg-danger bg-opacity-10"
                            style="width:32px;height:32px;">
                            <i class="bi bi-box-arrow-right"></i>
                        </div>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </nav>

        {{-- MAIN AREA --}}
        <div class="main-content flex-grow-1 d-flex flex-column">

            {{-- Topbar --}}
            <header class="bg-white border-bottom px-3 px-md-4 py-2 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    {{-- Mobile menu button --}}
                    <button class="mobile-menu-btn" onclick="toggleSidebar()">
                        <i class="bi bi-list"></i>
                    </button>
                    <h1 class="h5 mb-0">@yield('title', 'Dashboard')</h1>
                </div>
                <span class="text-muted small hide-mobile">Admin SIFORA</span>
            </header>

            {{-- Content --}}
            <main class="flex-grow-1">
                <div class="container-fluid py-3 py-md-4">
                    @yield('content')
                </div>
            </main>

            {{-- FOOTER --}}
            @include('include.footer')
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
            document.body.style.overflow = document.getElementById('sidebar').classList.contains('show') ? 'hidden' : '';
        }

        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('show');
            document.getElementById('sidebarOverlay').classList.remove('show');
            document.body.style.overflow = '';
        }

        // Close sidebar when clicking a menu link on mobile
        document.querySelectorAll('.sidebar .nav-link, .sidebar a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992) {
                    closeSidebar();
                }
            });
        });

        // Close sidebar on window resize if larger than breakpoint
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 992) {
                closeSidebar();
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
