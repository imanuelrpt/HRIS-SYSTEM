<x-app-layout>
    <x-slot name="title">Pengajuan Cuti</x-slot>

    @php
        $monthNames = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    @endphp

    <x-page-header
        title="Pengajuan Cuti"
        subtitle="Kelola pengajuan cuti dan pantau status persetujuannya."
    >
        <a href="{{ route('leave-requests.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Ajukan Cuti
        </a>
    </x-page-header>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span>Daftar Pengajuan</span>
            <form method="GET" action="{{ route('leave-requests.index') }}" class="d-flex flex-wrap gap-2">
                @if ($canViewAll)
                    <select name="user_id" class="form-select form-select-sm" style="width: 180px;">
                        <option value="">Semua karyawan</option>
                        @foreach ($users as $id => $name)
                            <option value="{{ $id }}" @selected($selectedUserId == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                @endif
                <select name="leave_type_id" class="form-select form-select-sm" style="width: 160px;">
                    <option value="">Semua jenis</option>
                    @foreach ($leaveTypes as $id => $name)
                        <option value="{{ $id }}" @selected($leaveTypeId == $id)>{{ $name }}</option>
                    @endforeach
                </select>
                <select name="status" class="form-select form-select-sm" style="width: 170px;">
                    <option value="">Semua status</option>
                    @foreach ($statusOptions as $value => $label)
                        <option value="{{ $value }}" @selected($statusFilter === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="month" class="form-select form-select-sm" style="width: 140px;">
                    <option value="">Semua bulan</option>
                    @foreach ($monthNames as $number => $label)
                        <option value="{{ $number }}" @selected($month === $number)>{{ $label }}</option>
                    @endforeach
                </select>
                <input type="number" name="year" value="{{ $year > 0 ? $year : '' }}" class="form-control form-control-sm" style="width: 100px;" placeholder="Tahun" min="2000" max="2100">
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-funnel"></i> Filter
                </button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        @if ($canViewAll)
                            <th>Karyawan</th>
                        @endif
                        <th>Jenis Cuti</th>
                        <th>Lama</th>
                        <th>Alasan</th>
                        <th>Status</th>
                        <th>Lampiran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($leaveRequests as $leaveRequest)
                        <tr>
                            <td class="text-nowrap">
                                <div class="fw-semibold">{{ format_tanggal($leaveRequest->start_date) }}</div>
                                <div class="text-muted small">s.d. {{ format_tanggal($leaveRequest->end_date) }}</div>
                            </td>
                            @if ($canViewAll)
                                <td>{{ $leaveRequest->user->name }}</td>
                            @endif
                            <td>{{ $leaveRequest->leaveType->name }}</td>
                            <td class="text-nowrap">{{ $leaveRequest->total_days }} hari</td>
                            <td style="min-width: 220px;">{{ $leaveRequest->reason }}</td>
                            <td>
                                <span class="badge {{ $leaveRequest->status->badgeClass() }}">
                                    {{ $leaveRequest->status->label() }}
                                </span>
                            </td>
                            <td>
                                @if ($leaveRequest->attachment_path)
                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($leaveRequest->attachment_path) }}" target="_blank" rel="noopener">
                                        <i class="bi bi-paperclip me-1"></i>Lihat
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if ($leaveRequest->user_id === auth()->id() && $leaveRequest->isPending())
                                    <form
                                        method="POST"
                                        action="{{ route('leave-requests.cancel', $leaveRequest) }}"
                                        class="js-confirm-form"
                                        data-confirm-title="Batalkan pengajuan?"
                                        data-confirm-text="Pengajuan cuti ini akan dibatalkan dan tidak dapat diproses kembali."
                                        data-confirm-button="Ya, batalkan"
                                    >
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-x-circle me-1"></i>Batalkan
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $canViewAll ? 8 : 7 }}">
                                <x-empty-state
                                    icon="bi-calendar2-x"
                                    message="Belum ada pengajuan cuti."
                                    description="Klik tombol Ajukan Cuti untuk membuat pengajuan pertama Anda."
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($leaveRequests->hasPages())
            <div class="card-body border-top">{{ $leaveRequests->links('pagination::bootstrap-5') }}</div>
        @endif
    </div>
</x-app-layout>
