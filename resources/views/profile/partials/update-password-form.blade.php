<form method="post" action="{{ route('password.update') }}" class="js-loading-form">
    @csrf
    @method('put')

    <div class="mb-3">
        <label for="update_password_current_password" class="form-label">Kata Sandi Saat Ini</label>
        <input id="update_password_current_password" name="current_password" type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" autocomplete="current-password">
        @error('current_password', 'updatePassword') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label for="update_password_password" class="form-label">Kata Sandi Baru</label>
        <input id="update_password_password" name="password" type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" autocomplete="new-password">
        @error('password', 'updatePassword') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label for="update_password_password_confirmation" class="form-label">Konfirmasi Kata Sandi Baru</label>
        <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password">
    </div>

    <button type="submit" class="btn btn-primary"><i class="bi bi-key me-1"></i>Ubah Kata Sandi</button>
</form>
