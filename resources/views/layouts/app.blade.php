<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <aside class="hris-sidebar" id="hrisSidebar">
        <a href="{{ route('dashboard') }}" class="brand">
            <span class="brand-icon"><i class="bi bi-grid-1x2-fill"></i></span>
            <span class="brand-text">HRIS Mini</span>
        </a>

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
    </aside>

    <div class="hris-main">
        <nav class="hris-topbar">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-light d-lg-none" type="button" id="sidebarToggle" aria-label="Buka menu">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <span class="text-muted d-none d-sm-inline">@yield('title', 'Dashboard')</span>
            </div>

            <div class="d-flex align-items-center gap-2">
                <div class="dropdown">
                    <button class="btn btn-light position-relative" type="button" data-bs-toggle="dropdown" aria-label="Notifikasi">
                        <i class="bi bi-bell fs-5"></i>
                        @if ($unreadNotificationsCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $unreadNotificationsCount }}
                            </span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-3" style="width: 320px;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong>Notifikasi</strong>
                            <a href="{{ route('notifications.index') }}" class="small text-decoration-none">Lihat semua</a>
                        </div>
                        @forelse ($latestNotifications as $notification)
                            <div class="notification-item small">
                                <div class="fw-semibold">{{ $notification->data['title'] ?? 'Notifikasi' }}</div>
                                <div class="text-muted">{{ $notification->data['message'] ?? '' }}</div>
                                <div class="text-muted mt-1">{{ $notification->created_at->diffForHumans() }}</div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-3 small">Belum ada notifikasi baru.</div>
                        @endforelse
                    </div>
                </div>

                <div class="dropdown">
                    <button class="btn btn-light d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                        @if (auth()->user()->photo_path)
                            <img src="{{ Storage::url(auth()->user()->photo_path) }}" alt="Foto profil" class="rounded-circle" width="28" height="28" style="object-fit: cover;">
                        @else
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white" style="width: 28px; height: 28px;">
                                {{ mb_substr(auth()->user()->name, 0, 1) }}
                            </span>
                        @endif
                        <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                        <i class="bi bi-chevron-down small"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <div class="px-3 py-2 small text-muted">
                            {{ auth()->user()->email }}<br>
                            <span class="badge bg-light text-dark mt-1">{{ auth()->user()->role->label() }}</span>
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
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function () {
            document.getElementById('hrisSidebar')?.classList.toggle('show');
        });

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
