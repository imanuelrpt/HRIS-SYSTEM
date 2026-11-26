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
            <span class="section-title mb-0"><i class="bi bi-list-ul"></i>Daftar Pengajuan</span>
            <form method="GET" action="{{ route('leave-requests.index') }}" class="hris-filterbar">
                @if ($canViewAll)
                    <div class="filter-field">
                        <label for="user_id">Karyawan</label>
                        <select name="user_id" id="user_id" class="form-select form-select-sm">
                            <option value="">Semua karyawan</option>
                            @foreach ($users as $id => $name)
                                <option value="{{ $id }}" @selected($selectedUserId == $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="filter-field">
                    <label for="leave_type_id">Jenis Cuti</label>
                    <select name="leave_type_id" id="leave_type_id" class="form-select form-select-sm">
                        <option value="">Semua jenis</option>
                        @foreach ($leaveTypes as $id => $name)
                            <option value="{{ $id }}" @selected($leaveTypeId == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-field">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-select form-select-sm">
                        <option value="">Semua status</option>
                        @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected($statusFilter === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-field">
                    <label for="month">Bulan</label>
                    <select name="month" id="month" class="form-select form-select-sm">
                        <option value="">Semua bulan</option>
                        @foreach ($monthNames as $number => $label)
                            <option value="{{ $number }}" @selected($month === $number)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-field" style="max-width: 110px;">
                    <label for="year">Tahun</label>
                    <input type="number" name="year" id="year" value="{{ $year > 0 ? $year : '' }}" class="form-control form-control-sm" placeholder="Tahun" min="2000" max="2100">
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                </div>
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
                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($leaveRequest->attachment_path) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">
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
