<x-app-layout>
    <x-slot name="title">{{ $isEditing ? 'Ubah Jenis Cuti' : 'Tambah Jenis Cuti' }}</x-slot>

    <x-page-header
        :title="$isEditing ? 'Ubah Jenis Cuti' : 'Tambah Jenis Cuti'"
        :subtitle="$isEditing ? 'Perbarui kuota dan ketentuan jenis cuti.' : 'Isi formulir berikut untuk menambahkan jenis cuti baru.'"
    />

    <div class="card" style="max-width: 760px;">
        <div class="card-body">
            <form method="POST" action="{{ $isEditing ? route('leave-types.update', $leaveType) : route('leave-types.store') }}" class="js-loading-form">
                @csrf
                @if ($isEditing)
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label for="name" class="form-label">Nama Jenis Cuti <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $leaveType->name) }}" class="form-control @error('name') is-invalid @enderror" placeholder="Contoh: Cuti Tahunan" required autofocus>
                    @error('name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="quota_days" class="form-label">Kuota per Tahun (hari) <span class="text-danger">*</span></label>
                    <input type="number" id="quota_days" name="quota_days" value="{{ old('quota_days', $leaveType->quota_days ?? 0) }}" class="form-control @error('quota_days') is-invalid @enderror" min="0" max="365" required>
                    @error('quota_days')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 form-check">
                    <input type="hidden" name="deducts_balance" value="0">
                    <input type="checkbox" id="deducts_balance" name="deducts_balance" value="1" class="form-check-input @error('deducts_balance') is-invalid @enderror" @checked(old('deducts_balance', $leaveType->deducts_balance))>
                    <label for="deducts_balance" class="form-check-label">Potong saldo cuti karyawan</label>
                    @error('deducts_balance')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4 form-check">
                    <input type="hidden" name="requires_attachment" value="0">
                    <input type="checkbox" id="requires_attachment" name="requires_attachment" value="1" class="form-check-input @error('requires_attachment') is-invalid @enderror" @checked(old('requires_attachment', $leaveType->requires_attachment))>
                    <label for="requires_attachment" class="form-check-label">Wajib lampiran dokumen pendukung</label>
                    @error('requires_attachment')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                    <a href="{{ route('leave-types.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
