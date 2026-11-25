<x-app-layout>
    <x-slot name="title">Jabatan</x-slot>

    <x-page-header title="Jabatan" subtitle="Kelola daftar jabatan beserta departemen induknya.">
        <a href="{{ route('positions.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Jabatan
        </a>
    </x-page-header>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span>Daftar Jabatan</span>
            <form method="GET" action="{{ route('positions.index') }}" class="d-flex flex-wrap gap-2">
                <input type="search" name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="Cari jabatan" style="width: 200px;">
                <select name="department_id" class="form-select form-select-sm" style="width: 190px;">
                    <option value="">Semua departemen</option>
                    @foreach ($departments as $id => $name)
                        <option value="{{ $id }}" @selected($departmentId == $id)>{{ $name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i></button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nama Jabatan</th>
                        <th>Departemen</th>
                        <th>Karyawan</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($positions as $position)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $position->name }}</div>
                                @if ($position->description)
                                    <div class="text-muted small">{{ \Illuminate\Support\Str::limit($position->description, 70) }}</div>
                                @endif
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $position->department->name }}</span></td>
                            <td><span class="badge bg-primary-subtle text-primary-emphasis">{{ $position->users_count }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('positions.edit', $position) }}" class="btn btn-sm btn-outline-primary" title="Ubah"><i class="bi bi-pencil-square"></i></a>
                                <form method="POST" action="{{ route('positions.destroy', $position) }}" class="d-inline js-delete-form" data-confirm-text="Jabatan {{ $position->name }} akan dihapus.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash3"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <x-empty-state icon="bi-briefcase" message="Belum ada jabatan." description="Tambahkan jabatan untuk melengkapi struktur departemen." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($positions->hasPages())
            <div class="card-body border-top">{{ $positions->links('pagination::bootstrap-5') }}</div>
        @endif
    </div>
</x-app-layout>
