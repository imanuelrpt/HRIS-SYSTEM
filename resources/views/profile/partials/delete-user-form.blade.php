<p class="text-muted small">
    Menghapus akun akan menghentikan akses Anda ke aplikasi. Masukkan kata sandi untuk mengonfirmasi.
</p>

<form method="post" action="{{ route('profile.destroy') }}" class="js-delete-form" data-confirm-text="Akun Anda akan dihapus dan tidak dapat dikembalikan.">
    @csrf
    @method('delete')

    <div class="mb-3">
        <label for="delete_password" class="form-label">Kata Sandi</label>
        <input id="delete_password" name="password" type="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror" placeholder="Masukkan kata sandi" autocomplete="current-password">
        @error('password', 'userDeletion') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
    </div>

    <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash3 me-1"></i>Hapus Akun Saya</button>
</form>
