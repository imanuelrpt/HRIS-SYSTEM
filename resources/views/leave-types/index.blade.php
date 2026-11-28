<x-app-layout>
    <x-slot name="title">Jenis Cuti</x-slot>

    <x-page-header title="Jenis Cuti" subtitle="Atur kuota dan ketentuan setiap jenis pengajuan cuti atau izin.">
        <a href="{{ route('leave-types.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Jenis Cuti
        </a>
    </x-page-header>

    <div class="card">
        <div class="card-header">Daftar Jenis Cuti</div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Kuota</th>
                        <th>Potong Saldo</th>
                        <th>Wajib Lampiran</th>
                        <th>Pengajuan</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($leaveTypes as $leaveType)
                        <tr>
                            <td class="fw-semibold">{{ $leaveType->name }}</td>
                            <td>{{ $leaveType->quota_days }} hari</td>
                            <td>
                                @if ($leaveType->deducts_balance)
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis">Ya</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis">Tidak</span>
                                @endif
                            </td>
                            <td>
                                @if ($leaveType->requires_attachment)
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis">Ya</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis">Tidak</span>
                                @endif
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $leaveType->leave_requests_count }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('leave-types.edit', $leaveType) }}" class="btn btn-sm btn-outline-primary" title="Ubah"><i class="bi bi-pencil-square"></i></a>
                                <form method="POST" action="{{ route('leave-types.destroy', $leaveType) }}" class="d-inline js-delete-form" data-confirm-text="Jenis cuti {{ $leaveType->name }} akan dihapus.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="Hapus"><i class="bi bi-trash3"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-empty-state icon="bi-list-check" message="Belum ada jenis cuti." description="Tambahkan jenis cuti seperti Cuti Tahunan, Sakit, atau Izin." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($leaveTypes->hasPages())
            <div class="card-body border-top">{{ $leaveTypes->links('pagination::bootstrap-5') }}</div>
        @endif
    </div>
</x-app-layout>
