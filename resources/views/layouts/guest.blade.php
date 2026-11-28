<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Masuk') — {{ config('app.name') }}</title>

    <script>
        (function () {
            const storedTheme = localStorage.getItem('hris-theme');
            const preferredTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            document.documentElement.setAttribute('data-bs-theme', storedTheme || preferredTheme);
        })();
    </script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <div class="auth-wrapper">
        <div class="w-100" style="max-width: 920px;">
            <div class="card auth-card mx-auto">
                <div class="row g-0">
                    <div class="col-md-5 auth-side d-none d-md-flex flex-column justify-content-between">
                        <div>
                            <span class="d-inline-flex align-items-center justify-content-center bg-white text-primary rounded" style="width: 46px; height: 46px;">
                                <i class="bi bi-grid-1x2-fill fs-5"></i>
                            </span>
                            <h2 class="mt-4 mb-2">HRIS Mini</h2>
                            <p class="mb-4 opacity-75">Sistem informasi kehadiran dan cuti karyawan.</p>
                            <div class="mt-4">
                                <div class="auth-feature"><i class="bi bi-clock-history"></i><span>Clock in/out</span></div>
                                <div class="auth-feature"><i class="bi bi-calendar2-check"></i><span>Pengajuan cuti</span></div>
                                <div class="auth-feature"><i class="bi bi-bar-chart"></i><span>Rekap kehadiran</span></div>
                            </div>
                        </div>
                        <div class="small opacity-75">
                            <i class="bi bi-shield-check me-2"></i>Akses hanya untuk karyawan terdaftar.
                        </div>
                    </div>
                    <div class="col-md-7 auth-form-panel">
                        @yield('content')
                    </div>
                </div>
            </div>
            <p class="text-center text-muted small mt-3 mb-0">&copy; {{ now()->year }} HRIS Mini. Dibangun dengan Laravel.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
