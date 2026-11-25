<x-guest-layout>
    <h3 class="fw-bold mb-1">Selamat datang kembali</h3>
    <p class="text-muted mb-4">Silakan masuk menggunakan akun yang diberikan Admin HR.</p>

    @if ($errors->any())
        <div class="alert alert-danger small">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ $errors->first() }}
        </div>
    @endif

    @if (session('status'))
        <div class="alert alert-success small">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus autocomplete="username">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Kata Sandi</label>
            <input id="password" type="password" name="password" class="form-control" required autocomplete="current-password">
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <label class="form-check-label small">
                <input class="form-check-input" type="checkbox" name="remember" value="1">
                Ingat saya
            </label>
            @if (Route::has('password.request'))
                <a class="small text-decoration-none" href="{{ route('password.request') }}">Lupa kata sandi?</a>
            @endif
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2">
            <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
        </button>
    </form>

    <div class="mt-4">
        <div class="d-flex align-items-center mb-2">
            <span class="text-muted small fw-semibold">AKUN DEMO</span>
            <hr class="flex-grow-1 mx-3">
        </div>
        @foreach ([
            ['Admin HR', 'admin@hris.test'],
            ['Manager', 'manager@hris.test'],
            ['Karyawan', 'karyawan@hris.test'],
        ] as [$label, $email])
            <div class="demo-account mb-2">
                <span class="small"><i class="bi bi-person-badge me-2 text-primary"></i>{{ $label }}</span>
                <span class="text-muted small">{{ $email }} / <code>password</code></span>
            </div>
        @endforeach
        <p class="text-muted small mt-2 mb-0">Registrasi publik dinonaktifkan. Akun dibuat oleh Admin HR.</p>
    </div>
</x-guest-layout>
