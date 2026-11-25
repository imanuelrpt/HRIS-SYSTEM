<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<form method="post" action="{{ route('profile.update') }}" class="js-loading-form">
    @csrf
    @method('patch')

    <div class="mb-3">
        <label for="name" class="form-label">Nama Lengkap</label>
        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" required autofocus autocomplete="name">
        @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror" required autocomplete="username">
        @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="alert alert-warning mt-2 small mb-0">
                Email Anda belum terverifikasi.
                <button type="submit" form="send-verification" class="btn btn-link btn-sm p-0 align-baseline">Kirim ulang tautan verifikasi</button>
                @if (session('status') === 'verification-link-sent')
                    <div class="mt-1">Tautan verifikasi baru telah dikirim.</div>
                @endif
            </div>
        @endif
    </div>

    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Profil</button>
</form>
