<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Masuk') — {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="auth-wrapper">
        <div class="w-100" style="max-width: 880px;">
            <div class="card auth-card mx-auto overflow-hidden" style="max-width: 880px;">
                <div class="row g-0">
                    <div class="col-md-5 auth-side d-none d-md-flex flex-column justify-content-between">
                        <div>
                            <span class="d-inline-flex align-items-center justify-content-center bg-white text-primary rounded" style="width: 42px; height: 42px;">
                                <i class="bi bi-grid-1x2-fill fs-5"></i>
                            </span>
                            <h2 class="mt-4 mb-2 fw-bold">HRIS Mini</h2>
                            <p class="mb-0 opacity-75">Absensi, pengajuan cuti, approval, dan rekap kehadiran dalam satu aplikasi.</p>
                        </div>
                        <div class="small opacity-75">
                            <i class="bi bi-shield-check me-2"></i>Akses hanya untuk karyawan terdaftar.
                        </div>
                    </div>
                    <div class="col-md-7 p-4 p-md-5">
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
