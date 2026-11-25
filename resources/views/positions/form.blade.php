<x-app-layout>
    <x-slot name="title">{{ $isEditing ? 'Ubah Jabatan' : 'Tambah Jabatan' }}</x-slot>

    <x-page-header
        :title="$isEditing ? 'Ubah Jabatan' : 'Tambah Jabatan'"
        :subtitle="$isEditing ? 'Perbarui data jabatan yang sudah terdaftar.' : 'Isi formulir berikut untuk menambahkan jabatan baru.'"
    />

    <div class="card" style="max-width: 760px;">
        <div class="card-body">
            <form method="POST" action="{{ $isEditing ? route('positions.update', $position) : route('positions.store') }}" class="js-loading-form">
                @csrf
                @if ($isEditing)
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label for="department_id" class="form-label">Departemen <span class="text-danger">*</span></label>
                    <select id="department_id" name="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                        <option value="">Pilih departemen</option>
                        @foreach ($departments as $id => $name)
                            <option value="{{ $id }}" @selected(old('department_id', $position->department_id) == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label">Nama Jabatan <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $position->name) }}" class="form-control @error('name') is-invalid @enderror" required autofocus>
                    @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea id="description" name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $position->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                    <a href="{{ route('positions.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
