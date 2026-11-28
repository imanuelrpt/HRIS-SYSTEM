<x-app-layout>
    <x-slot name="title">Hari Libur</x-slot>

    <x-page-header title="Hari Libur" subtitle="Tanggal libur yang tidak dihitung sebagai hari kerja.">
        <a href="{{ route('holidays.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Hari Libur
        </a>
    </x-page-header>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span>Daftar Hari Libur</span>
            <form method="GET" action="{{ route('holidays.index') }}" class="d-flex gap-2">
                <input type="search" name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="Cari keterangan" style="width: 200px;">
                <input type="number" name="year" value="{{ $year ?: '' }}" class="form-control form-control-sm" placeholder="Tahun" style="width: 100px;" min="2000" max="2100">
                <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i></button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($holidays as $holiday)
                        <tr>
                            <td class="fw-semibold">{{ format_tanggal($holiday->date) }}</td>
                            <td>{{ $holiday->description }}</td>
                            <td class="text-end">
                                <a href="{{ route('holidays.edit', $holiday) }}" class="btn btn-sm btn-outline-primary" title="Ubah"><i class="bi bi-pencil-square"></i></a>
                                <form method="POST" action="{{ route('holidays.destroy', $holiday) }}" class="d-inline js-delete-form" data-confirm-text="Hari libur {{ format_tanggal($holiday->date) }} akan dihapus.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="Hapus"><i class="bi bi-trash3"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">
                                <x-empty-state icon="bi-calendar-x" message="Belum ada hari libur." description="Tambahkan tanggal libur agar perhitungan cuti dan absensi akurat." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($holidays->hasPages())
            <div class="card-body border-top">{{ $holidays->links('pagination::bootstrap-5') }}</div>
        @endif
    </div>
</x-app-layout>
