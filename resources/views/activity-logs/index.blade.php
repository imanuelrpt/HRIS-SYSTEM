<x-app-layout>
    <x-slot name="title">Audit Log</x-slot>

    @php
        $actionLabels = [
            'login' => 'Masuk ke sistem',
            'attendance.clock_in' => 'Clock in',
            'attendance.clock_out' => 'Clock out',
            'leave.requested' => 'Pengajuan cuti dibuat',
            'leave.cancelled' => 'Pengajuan cuti dibatalkan',
            'leave.approved_level_1' => 'Cuti disetujui atasan',
            'leave.approved' => 'Cuti disetujui HR',
            'leave.rejected' => 'Pengajuan cuti ditolak',
            'user.created' => 'Karyawan ditambahkan',
            'user.updated' => 'Data karyawan diubah',
            'user.deleted' => 'Karyawan dinonaktifkan',
            'department.created' => 'Departemen ditambahkan',
            'department.updated' => 'Departemen diubah',
            'department.deleted' => 'Departemen dihapus',
            'position.created' => 'Jabatan ditambahkan',
            'position.updated' => 'Jabatan diubah',
            'position.deleted' => 'Jabatan dihapus',
            'holiday.created' => 'Hari libur ditambahkan',
            'holiday.updated' => 'Hari libur diubah',
            'holiday.deleted' => 'Hari libur dihapus',
            'leave-type.created' => 'Jenis cuti ditambahkan',
            'leave-type.updated' => 'Jenis cuti diubah',
            'leave-type.deleted' => 'Jenis cuti dihapus',
            'work-setting.updated' => 'Jam kerja diubah',
        ];
    @endphp

    <x-page-header title="Audit Log" subtitle="Catatan aktivitas penting yang dilakukan di dalam aplikasi." />

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span>Riwayat Aktivitas</span>
            <form method="GET" action="{{ route('activity-logs.index') }}" class="d-flex flex-wrap gap-2">
                <input type="search" name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="Cari aktivitas" style="width: 200px;">
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control form-control-sm" style="width: 160px;">
                <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control form-control-sm" style="width: 160px;">
                <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i></button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Pengguna</th>
                        <th>Aktivitas</th>
                        <th>Keterangan</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td class="text-nowrap">{{ format_tanggal_waktu($log->created_at) }}</td>
                            <td>{{ $log->user?->name ?? 'Sistem' }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $actionLabels[$log->action] ?? \Illuminate\Support\Str::headline($log->action) }}</span>
                            </td>
                            <td>{{ $log->description }}</td>
                            <td class="text-muted small">{{ $log->ip ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-empty-state icon="bi-shield-lock" message="Belum ada aktivitas." description="Aktivitas pengelolaan data akan tercatat di sini." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($logs->hasPages())
            <div class="card-body border-top">{{ $logs->links('pagination::bootstrap-5') }}</div>
        @endif
    </div>
</x-app-layout>
