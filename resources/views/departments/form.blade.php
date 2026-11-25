<x-app-layout>
    <x-slot name="title">{{ $isEditing ? 'Ubah Departemen' : 'Tambah Departemen' }}</x-slot>

    <x-page-header
        :title="$isEditing ? 'Ubah Departemen' : 'Tambah Departemen'"
        :subtitle="$isEditing ? 'Perbarui data departemen yang sudah terdaftar.' : 'Isi formulir berikut untuk menambahkan departemen baru.'"
    />

    <div class="card" style="max-width: 760px;">
        <div class="card-body">
            <form method="POST" action="{{ $isEditing ? route('departments.update', $department) : route('departments.store') }}" class="js-loading-form">
                @csrf
                @if ($isEditing)
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label for="name" class="form-label">Nama Departemen <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $department->name) }}" class="form-control @error('name') is-invalid @enderror" required autofocus>
                    @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="code" class="form-label">Kode Singkat</label>
                    <input type="text" id="code" name="code" value="{{ old('code', $department->code) }}" class="form-control @error('code') is-invalid @enderror" placeholder="Contoh: HRD" maxlength="20">
                    @error('code')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea id="description" name="description" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="Tugas dan fungsi departemen">{{ old('description', $department->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                    <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
