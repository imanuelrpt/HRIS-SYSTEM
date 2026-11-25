<x-app-layout>
    <x-slot name="title">{{ $isEditing ? 'Ubah Hari Libur' : 'Tambah Hari Libur' }}</x-slot>

    <x-page-header
        :title="$isEditing ? 'Ubah Hari Libur' : 'Tambah Hari Libur'"
        :subtitle="$isEditing ? 'Perbarui tanggal dan keterangan hari libur.' : 'Tambahkan tanggal libur yang tidak dihitung sebagai hari kerja.'"
    />

    <div class="card" style="max-width: 760px;">
        <div class="card-body">
            <form method="POST" action="{{ $isEditing ? route('holidays.update', $holiday) : route('holidays.store') }}" class="js-loading-form">
                @csrf
                @if ($isEditing)
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label for="date" class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" id="date" name="date" value="{{ old('date', $holiday->date?->toDateString()) }}" class="form-control @error('date') is-invalid @enderror" required>
                    @error('date')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label">Keterangan <span class="text-danger">*</span></label>
                    <input type="text" id="description" name="description" value="{{ old('description', $holiday->description) }}" class="form-control @error('description') is-invalid @enderror" placeholder="Contoh: Hari Raya Idul Fitri" required>
                    @error('description')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                    <a href="{{ route('holidays.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
