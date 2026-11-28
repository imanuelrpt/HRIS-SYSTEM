<x-app-layout>
    <x-slot name="title">Departemen</x-slot>

    <x-page-header title="Departemen" subtitle="Kelola unit organisasi dan struktur departemen perusahaan.">
        <a href="{{ route('departments.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Departemen
        </a>
    </x-page-header>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span>Daftar Departemen</span>
            <form method="GET" action="{{ route('departments.index') }}" class="d-flex gap-2">
                <input type="search" name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="Cari nama atau kode" style="width: 240px;">
                <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i></button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nama Departemen</th>
                        <th>Kode</th>
                        <th>Karyawan</th>
                        <th>Jabatan</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($departments as $department)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $department->name }}</div>
                                @if ($department->description)
                                    <div class="text-muted small">{{ \Illuminate\Support\Str::limit($department->description, 70) }}</div>
                                @endif
                            </td>
                            <td>
                                @if ($department->code)
                                    <span class="badge bg-light text-dark border">{{ $department->code }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td><span class="badge bg-secondary-subtle text-secondary-emphasis">{{ $department->users_count }}</span></td>
                            <td><span class="badge bg-secondary-subtle text-secondary-emphasis">{{ $department->positions_count }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('departments.edit', $department) }}" class="btn btn-sm btn-outline-primary" title="Ubah">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form method="POST" action="{{ route('departments.destroy', $department) }}" class="d-inline js-delete-form" data-confirm-text="Departemen {{ $department->name }} akan dihapus.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="Hapus"><i class="bi bi-trash3"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-empty-state icon="bi-diagram-3" message="Belum ada departemen." description="Tambahkan departemen pertama untuk mulai menyusun struktur organisasi." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($departments->hasPages())
            <div class="card-body border-top">{{ $departments->links('pagination::bootstrap-5') }}</div>
        @endif
    </div>
</x-app-layout>
