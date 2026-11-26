<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>

    <script>
        (function () {
            const storedTheme = localStorage.getItem('hris-theme');
            const preferredTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            document.documentElement.setAttribute('data-bs-theme', storedTheme || preferredTheme);

            if (window.innerWidth > 991.98 && localStorage.getItem('hris-sidebar-collapsed') === '1') {
                document.documentElement.classList.add('sidebar-collapsed');
            }
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <aside class="hris-sidebar" id="hrisSidebar">
        <a href="{{ route('dashboard') }}" class="brand">
            <span class="brand-icon"><i class="bi bi-grid-1x2-fill"></i></span>
            <span class="brand-copy">
                <span class="brand-text">HRIS Mini</span>
                <span class="brand-caption">People &amp; Attendance</span>
            </span>
        </a>

        <nav class="hris-sidebar-nav">
            <div class="nav-caption">Menu Utama</div>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i><span>Dashboard</span>
            </a>
            <a href="{{ route('attendances.index') }}" class="nav-link {{ request()->routeIs('attendances.*') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i><span>Riwayat Absensi</span>
            </a>
            <a href="{{ route('leave-requests.index') }}" class="nav-link {{ request()->routeIs('leave-requests.*') ? 'active' : '' }}">
                <i class="bi bi-calendar2-check"></i><span>Pengajuan Cuti</span>
            </a>

            @if (auth()->user()->isAdmin() || auth()->user()->isManager())
                <a href="{{ route('approvals.index') }}" class="nav-link {{ request()->routeIs('approvals.*') ? 'active' : '' }}">
                    <i class="bi bi-check2-square"></i><span>Persetujuan</span>
                </a>
                <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph"></i><span>Rekap Bulanan</span>
                </a>
            @endif

            @if (auth()->user()->isAdmin())
                <div class="nav-caption">Administrasi</div>
                <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i><span>Karyawan</span>
                </a>
                <a href="{{ route('departments.index') }}" class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}">
                    <i class="bi bi-diagram-3"></i><span>Departemen</span>
                </a>
                <a href="{{ route('positions.index') }}" class="nav-link {{ request()->routeIs('positions.*') ? 'active' : '' }}">
                    <i class="bi bi-briefcase"></i><span>Jabatan</span>
                </a>
                <a href="{{ route('holidays.index') }}" class="nav-link {{ request()->routeIs('holidays.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-x"></i><span>Hari Libur</span>
                </a>
                <a href="{{ route('leave-types.index') }}" class="nav-link {{ request()->routeIs('leave-types.*') ? 'active' : '' }}">
                    <i class="bi bi-list-check"></i><span>Jenis Cuti</span>
                </a>
                <a href="{{ route('work-settings.edit') }}" class="nav-link {{ request()->routeIs('work-settings.*') ? 'active' : '' }}">
                    <i class="bi bi-clock"></i><span>Jam Kerja</span>
                </a>
                <a href="{{ route('activity-logs.index') }}" class="nav-link {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}">
                    <i class="bi bi-shield-lock"></i><span>Audit Log</span>
                </a>
            @endif
        </nav>

        <div class="hris-sidebar-footer">
            <a href="{{ route('profile.edit') }}" class="hris-sidebar-user">
                <span class="avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                <span class="user-meta">
                    <span class="user-name d-block">{{ auth()->user()->name }}</span>
                    <span class="user-role">{{ auth()->user()->role->label() }}</span>
                </span>
                <i class="bi bi-chevron-right ms-auto text-white-50 small"></i>
            </a>
        </div>
    </aside>

    <div class="hris-sidebar-backdrop" id="sidebarBackdrop" aria-hidden="true"></div>

    <div class="hris-main">
        <nav class="hris-topbar">
            <div class="hris-topbar-title">
                <button
                    class="hris-icon-button"
                    type="button"
                    id="sidebarToggle"
                    aria-controls="hrisSidebar"
                    aria-expanded="true"
                    aria-label="Tutup sidebar"
                >
                    <i class="bi bi-layout-sidebar fs-5" id="sidebarToggleIcon"></i>
                </button>
                <span class="topbar-icon d-none d-sm-inline-flex"><i class="bi bi-grid-1x2"></i></span>
                <span class="topbar-label d-none d-sm-inline">@yield('title', 'Dashboard')</span>
                <span class="hris-topbar-divider d-none d-md-block"></span>
                <span class="hris-date-pill d-none d-md-inline-flex">
                    <i class="bi bi-calendar3"></i>{{ now()->translatedFormat('l, d F Y') }}
                </span>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button type="button" class="hris-icon-button" id="themeToggle" aria-label="Aktifkan mode gelap">
                    <i class="bi bi-moon-stars" id="themeIcon"></i>
                </button>

                <div class="dropdown">
                    <button class="hris-icon-button position-relative" type="button" data-bs-toggle="dropdown" aria-label="Notifikasi">
                        <i class="bi bi-bell fs-5"></i>
                        @if ($unreadNotificationsCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $unreadNotificationsCount }}
                            </span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end hris-notification-menu mt-3">
                        <div class="hris-notification-head">
                            <strong><i class="bi bi-bell me-2"></i>Notifikasi</strong>
                            <a href="{{ route('notifications.index') }}">Lihat semua</a>
                        </div>
                        <div class="hris-notification-body">
                            @forelse ($latestNotifications as $notification)
                                <div class="notification-item d-flex gap-2">
                                    <span class="notification-icon"><i class="bi bi-info-circle"></i></span>
                                    <div class="min-w-0">
                                        <div class="fw-semibold small">{{ $notification->data['title'] ?? 'Notifikasi' }}</div>
                                        <div class="text-muted small">{{ $notification->data['message'] ?? '' }}</div>
                                        <div class="text-muted mt-1" style="font-size: 11px;">{{ $notification->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-4 small">
                                    <i class="bi bi-bell-slash d-block fs-4 mb-2"></i>Belum ada notifikasi baru.
                                </div>
                            @endforelse
                        </div>
                        <div class="hris-notification-foot">
                            <a href="{{ route('notifications.index') }}" class="small text-decoration-none">Kelola notifikasi</a>
                        </div>
                    </div>
                </div>

                <div class="dropdown">
                    <button class="hris-avatar-button" type="button" data-bs-toggle="dropdown" aria-label="Menu akun">
                        @if (auth()->user()->photo_path)
                            <span class="hris-avatar"><img src="{{ Storage::url(auth()->user()->photo_path) }}" alt="Foto profil"></span>
                        @else
                            <span class="hris-avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                        @endif
                        <span class="user-name fw-semibold small">{{ auth()->user()->name }}</span>
                        <i class="bi bi-chevron-down small text-muted"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end mt-2">
                        <div class="px-3 py-2 small">
                            <div class="fw-semibold">{{ auth()->user()->name }}</div>
                            <div class="text-muted">{{ auth()->user()->email }}</div>
                            <span class="badge bg-primary-subtle text-primary-emphasis mt-1">{{ auth()->user()->role->label() }}</span>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Profil Saya</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <main class="hris-content">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <div class="flex-grow-1">{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    <div class="flex-grow-1">{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');

        function syncThemeIcon() {
            if (!themeIcon) {
                return;
            }

            const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
            themeIcon.className = isDark ? 'bi bi-sun' : 'bi bi-moon-stars';
            themeToggle?.setAttribute('aria-label', isDark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap');
        }

        themeToggle?.addEventListener('click', function () {
            const nextTheme = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-bs-theme', nextTheme);
            localStorage.setItem('hris-theme', nextTheme);
            syncThemeIcon();
        });
        syncThemeIcon();

        const sidebar = document.getElementById('hrisSidebar');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const compactSidebar = window.matchMedia('(max-width: 991.98px)');

        function isSidebarOpen() {
            return compactSidebar.matches
                ? sidebar?.classList.contains('show') === true
                : !document.documentElement.classList.contains('sidebar-collapsed');
        }

        function syncSidebarToggle() {
            const isOpen = isSidebarOpen();
            sidebarToggle?.setAttribute('aria-expanded', String(isOpen));
            sidebarToggle?.setAttribute('aria-label', isOpen ? 'Tutup sidebar' : 'Buka sidebar');
        }

        function closeMobileSidebar() {
            sidebar?.classList.remove('show');
            sidebarBackdrop?.classList.remove('show');
            syncSidebarToggle();
        }

        function toggleSidebar() {
            if (compactSidebar.matches) {
                const shouldOpen = !sidebar?.classList.contains('show');
                sidebar?.classList.toggle('show', shouldOpen);
                sidebarBackdrop?.classList.toggle('show', shouldOpen);
            } else {
                const isCollapsed = document.documentElement.classList.toggle('sidebar-collapsed');
                localStorage.setItem('hris-sidebar-collapsed', isCollapsed ? '1' : '0');
                sidebar?.classList.remove('show');
                sidebarBackdrop?.classList.remove('show');
            }

            syncSidebarToggle();
        }

        sidebarToggle?.addEventListener('click', toggleSidebar);
        sidebarBackdrop?.addEventListener('click', closeMobileSidebar);
        document.querySelectorAll('#hrisSidebar .nav-link').forEach(function (link) {
            link.addEventListener('click', function () {
                if (compactSidebar.matches) {
                    closeMobileSidebar();
                }
            });
        });
        compactSidebar.addEventListener('change', function () {
            closeMobileSidebar();

            if (!compactSidebar.matches) {
                document.documentElement.classList.toggle('sidebar-collapsed', localStorage.getItem('hris-sidebar-collapsed') === '1');
            }

            syncSidebarToggle();
        });
        syncSidebarToggle();

        @if (session('swal'))
            Swal.fire({
                icon: @json(session('swal.icon', 'success')),
                title: @json(session('swal.title', 'Berhasil')),
                text: @json(session('swal.text', '')),
                timer: 2500,
                showConfirmButton: false
            });
        @endif

        document.querySelectorAll('.js-delete-form').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();

                Swal.fire({
                    icon: 'warning',
                    title: 'Hapus data?',
                    text: form.dataset.confirmText || 'Data yang dihapus tidak dapat dikembalikan.',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        document.querySelectorAll('.js-loading-form').forEach(function (form) {
            form.addEventListener('submit', function () {
                form.querySelectorAll('[type="submit"]').forEach(function (button) {
                    if (!button.disabled) {
                        button.disabled = true;
                        button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
                    }
                });
            });
        });

        document.querySelectorAll('.js-confirm-form').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();

                Swal.fire({
                    icon: 'warning',
                    title: form.dataset.confirmTitle || 'Konfirmasi',
                    text: form.dataset.confirmText || 'Lanjutkan tindakan ini?',
                    showCancelButton: true,
                    confirmButtonText: form.dataset.confirmButton || 'Ya, lanjutkan',
                    cancelButtonText: 'Batal'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
