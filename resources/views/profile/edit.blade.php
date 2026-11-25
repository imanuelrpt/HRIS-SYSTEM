<x-app-layout>
    <x-slot name="title">Profil Saya</x-slot>

    <x-page-header title="Profil Saya" subtitle="Kelola data pribadi, kata sandi, dan akun Anda." />

    @if ($user->must_change_password)
        <div class="alert alert-warning">
            <i class="bi bi-shield-exclamation me-2"></i>Untuk keamanan, ubah kata sandi Anda terlebih dahulu sebelum mengakses menu lain.
        </div>
    @endif

    @if (session('status') === 'profile-updated')
        <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Profil berhasil diperbarui.</div>
    @endif

    @if (session('status') === 'password-updated')
        <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Kata sandi berhasil diubah.</div>
    @endif

    <div class="row g-3 align-items-start">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">Informasi Profil</div>
                <div class="card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card mb-3">
                <div class="card-header">Ubah Kata Sandi</div>
                <div class="card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
            <div class="card border-danger-subtle">
                <div class="card-header text-danger">Hapus Akun</div>
                <div class="card-body">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
