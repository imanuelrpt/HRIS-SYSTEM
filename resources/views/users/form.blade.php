<x-app-layout>
    <x-slot name="title">{{ $isEditing ? 'Ubah Karyawan' : 'Tambah Karyawan' }}</x-slot>

    <x-page-header
        :title="$isEditing ? 'Ubah Karyawan' : 'Tambah Karyawan'"
        :subtitle="$isEditing ? 'Perbarui data dan hak akses karyawan.' : 'Buat akun baru dan lengkapi data kepegawaiannya.'"
    />

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ $isEditing ? route('users.update', $user) : route('users.store') }}" class="js-loading-form" enctype="multipart/form-data">
                @csrf
                @if ($isEditing)
                    @method('PUT')
                @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" required autofocus>
                        @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror" required>
                        @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="nik" class="form-label">NIK</label>
                        <input type="text" id="nik" name="nik" value="{{ old('nik', $user->nik) }}" class="form-control @error('nik') is-invalid @enderror" maxlength="30">
                        @error('nik') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="phone" class="form-label">No. Telepon</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control @error('phone') is-invalid @enderror" maxlength="20">
                        @error('phone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="join_date" class="form-label">Tanggal Bergabung</label>
                        <input type="date" id="join_date" name="join_date" value="{{ old('join_date', $user->join_date?->toDateString()) }}" class="form-control @error('join_date') is-invalid @enderror">
                        @error('join_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="role" class="form-label">Peran <span class="text-danger">*</span></label>
                        <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                            @foreach ($roleOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('role', $user->role?->value ?? \App\Enums\Role::Employee->value) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('role') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="department_id" class="form-label">Departemen</label>
                        <select id="department_id" name="department_id" class="form-select @error('department_id') is-invalid @enderror">
                            <option value="">Tidak ada</option>
                            @foreach ($departments as $id => $name)
                                <option value="{{ $id }}" @selected(old('department_id', $user->department_id) == $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('department_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="position_id" class="form-label">Jabatan</label>
                        <select id="position_id" name="position_id" class="form-select @error('position_id') is-invalid @enderror">
                            <option value="">Tidak ada</option>
                            @foreach ($positions as $id => $label)
                                <option value="{{ $id }}" @selected(old('position_id', $user->position_id) == $id)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('position_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="manager_id" class="form-label">Atasan Langsung</label>
                        <select id="manager_id" name="manager_id" class="form-select @error('manager_id') is-invalid @enderror">
                            <option value="">Tidak ada</option>
                            @foreach ($managers as $id => $name)
                                <option value="{{ $id }}" @selected(old('manager_id', $user->manager_id) == $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('manager_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="photo" class="form-label">Foto Profil</label>
                        <input type="file" id="photo" name="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/jpeg,image/png,image/webp">
                        <div class="form-text">Opsional. Format JPG, PNG, atau WebP, maksimal 2 MB.</div>
                        @error('photo') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        @if ($user->photo_path)
                            <img src="{{ Storage::url($user->photo_path) }}" alt="Foto profil saat ini" class="mt-2 rounded" width="56" height="56" style="object-fit: cover;">
                        @endif
                    </div>

                    <div class="col-md-6">
                        <label for="password" class="form-label">Kata Sandi {{ $isEditing ? '(kosongkan jika tidak diubah)' : '*' }}</label>
                        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" {{ $isEditing ? '' : 'required' }} autocomplete="new-password">
                        @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" {{ $isEditing ? '' : 'required' }} autocomplete="new-password">
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex flex-wrap gap-4">
                    <div class="form-check">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" id="is_active" name="is_active" value="1" class="form-check-input" @checked(old('is_active', $user->exists ? $user->is_active : true))>
                        <label for="is_active" class="form-check-label">Akun aktif</label>
                    </div>
                    <div class="form-check">
                        <input type="hidden" name="must_change_password" value="0">
                        <input type="checkbox" id="must_change_password" name="must_change_password" value="1" class="form-check-input" @checked(old('must_change_password', $user->must_change_password))>
                        <label for="must_change_password" class="form-check-label">Wajib ubah kata sandi saat login</label>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
