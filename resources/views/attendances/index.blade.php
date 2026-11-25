<x-app-layout>
    <x-slot name="title">Riwayat Absensi</x-slot>

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
        title="Riwayat Absensi"
        subtitle="Catat kehadiran hari ini dan pantau riwayat absensi Anda."
    >
        <span class="badge bg-light text-dark border fs-6">
            <i class="bi bi-clock me-1"></i>{{ format_waktu($workSetting->check_in) }} — {{ format_waktu($workSetting->check_out) }}
        </span>
    </x-page-header>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-calendar-check me-2"></i>Absensi Hari Ini</span>
            <span class="text-muted small">{{ format_tanggal(today()) }}</span>
        </div>
        <div class="card-body">
            @if (! $isWorkingDay)
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-2"></i>Hari ini bukan hari kerja atau tanggal libur, sehingga absensi tidak dapat dilakukan.
                </div>
            @elseif ($todayAttendance === null)
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <div class="text-muted small">Status absensi</div>
                        <div class="h4 mb-0">Belum clock-in</div>
                    </div>
                    <form method="POST" action="{{ route('attendances.check-in') }}" class="js-loading-form">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Clock In
                        </button>
                    </form>
                </div>
            @else
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <div class="text-muted small">Clock In</div>
                        <div class="hris-clock-value">{{ format_waktu($todayAttendance->clock_in) }}</div>
                    </div>
                    <div class="col-auto">
                        <div class="text-muted small">Clock Out</div>
                        <div class="hris-clock-value {{ $todayAttendance->clock_out === null ? 'text-muted' : '' }}">
                            {{ $todayAttendance->clock_out === null ? '--:--' : format_waktu($todayAttendance->clock_out) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="text-muted small">Status</div>
                        <span class="badge {{ $todayAttendance->status->badgeClass() }} fs-6">
                            {{ $todayAttendance->status->label() }}
                        </span>
                    </div>
                    <div class="col-auto ms-lg-auto">
                        @if ($todayAttendance->clock_out === null)
                            <form method="POST" action="{{ route('attendances.check-out') }}" class="js-loading-form">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary btn-lg">
                                    <i class="bi bi-box-arrow-right me-2"></i>Clock Out
                                </button>
                            </form>
                        @else
                            <span class="text-success fw-semibold">
                                <i class="bi bi-check-circle-fill me-2"></i>Absensi hari ini sudah lengkap
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span>Daftar Absensi</span>
            <form method="GET" action="{{ route('attendances.index') }}" class="d-flex flex-wrap gap-2">
                @if ($canViewAll)
                    <select name="user_id" class="form-select form-select-sm" style="width: 180px;">
                        <option value="">Semua karyawan</option>
                        @foreach ($users as $id => $name)
                            <option value="{{ $id }}" @selected($selectedUserId == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                @endif
                <select name="month" class="form-select form-select-sm" style="width: 140px;">
                    @foreach ($monthNames as $number => $label)
                        <option value="{{ $number }}" @selected($month === $number)>{{ $label }}</option>
                    @endforeach
                </select>
                <input type="number" name="year" value="{{ $year }}" class="form-control form-control-sm" style="width: 100px;" min="2000" max="2100">
                <select name="status" class="form-select form-select-sm" style="width: 150px;">
                    <option value="">Semua status</option>
                    @foreach ($statusOptions as $value => $label)
                        <option value="{{ $value }}" @selected($statusFilter === $value)>{{ $label }}</option>
                    @endforeach
                </select>
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
                        <th>Clock In</th>
                        <th>Clock Out</th>
                        <th>Status</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($attendances as $attendance)
                        <tr>
                            <td class="text-nowrap fw-semibold">{{ format_tanggal($attendance->date) }}</td>
                            @if ($canViewAll)
                                <td>{{ $attendance->user?->name ?? '-' }}</td>
                            @endif
                            <td class="text-nowrap">{{ format_waktu($attendance->clock_in) }}</td>
                            <td class="text-nowrap">{{ format_waktu($attendance->clock_out) }}</td>
                            <td>
                                <span class="badge {{ $attendance->status->badgeClass() }}">
                                    {{ $attendance->status->label() }}
                                </span>
                            </td>
                            <td>
                                @if ($attendance->is_manual)
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis">Manual</span>
                                @endif
                                {{ $attendance->notes ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $canViewAll ? 6 : 5 }}">
                                <x-empty-state
                                    icon="bi-clock-history"
                                    message="Belum ada absensi pada periode ini."
                                    description="Ubah filter periode atau lakukan clock-in pada hari kerja."
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($attendances->hasPages())
            <div class="card-body border-top">{{ $attendances->links('pagination::bootstrap-5') }}</div>
        @endif
    </div>
</x-app-layout>
