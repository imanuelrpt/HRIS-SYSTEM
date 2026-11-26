<x-guest-layout>
    <div class="mb-4">
        <span class="hris-eyebrow">Portal Karyawan</span>
        <h3>Selamat datang kembali</h3>
        <p class="text-muted mb-0">Silakan masuk menggunakan akun yang diberikan Admin HR.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger small d-flex align-items-start gap-2">
            <i class="bi bi-exclamation-triangle-fill mt-1"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    @if (session('status'))
        <div class="alert alert-success small">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="nama@hris.test" required autofocus autocomplete="username">
            </div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Kata Sandi</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input id="password" type="password" name="password" class="form-control" placeholder="Masukkan kata sandi" required autocomplete="current-password">
                <button class="btn btn-outline-secondary password-toggle" type="button" id="passwordToggle" aria-label="Tampilkan kata sandi">
                    <i class="bi bi-eye" id="passwordToggleIcon"></i>
                </button>
            </div>
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
        <div class="d-flex align-items-center mb-3">
            <span class="text-muted small fw-bold">AKUN DEMO</span>
            <hr class="flex-grow-1 mx-3">
        </div>
        @foreach ([
            ['Admin HR', 'admin@hris.test'],
            ['Manager', 'manager@hris.test'],
            ['Karyawan', 'karyawan@hris.test'],
        ] as [$label, $email])
            <button type="button" class="demo-account mb-2" data-email="{{ $email }}" data-password="password">
                <span class="small fw-semibold"><i class="bi bi-person-badge me-2 text-primary"></i>{{ $label }}</span>
                <span class="text-muted small text-end">{{ $email }}<br><code>password</code></span>
                <i class="bi bi-arrow-up-left text-primary ms-2"></i>
            </button>
        @endforeach
        <p class="text-muted small mt-2 mb-0">Klik akun demo untuk mengisi otomatis. Registrasi publik dinonaktifkan.</p>
    </div>

    @push('scripts')
        <script>
            document.querySelectorAll('.demo-account').forEach(function (button) {
                button.addEventListener('click', function () {
                    document.getElementById('email').value = button.dataset.email;
                    document.getElementById('password').value = button.dataset.password;
                    document.getElementById('password').focus();
                });
            });

            const passwordToggle = document.getElementById('passwordToggle');
            const passwordInput = document.getElementById('password');
            const passwordToggleIcon = document.getElementById('passwordToggleIcon');

            passwordToggle?.addEventListener('click', function () {
                const showPassword = passwordInput.type === 'password';
                passwordInput.type = showPassword ? 'text' : 'password';
                passwordToggleIcon.className = showPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
                passwordToggle.setAttribute('aria-label', showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
            });
        </script>
    @endpush
</x-guest-layout>
