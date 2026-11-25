<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h1 class="hris-page-title">Halo, {{ $user->name }} 👋</h1>
            <p class="hris-page-subtitle">Berikut ringkasan data kehadiran dan kepegawaian hari ini.</p>
        </div>
        <span class="badge bg-primary-subtle text-primary-emphasis fs-6">
            <i class="bi bi-person-badge me-1"></i>{{ $user->role->label() }}
        </span>
    </div>

    <div class="row g-3">
        <div class="col-6 col-lg-4 col-xl-2">
            <div class="card h-100">
                <div class="card-body stat-card">
                    <span class="stat-icon bg-primary-subtle text-primary-emphasis"><i class="bi bi-people"></i></span>
                    <div>
                        <div class="stat-label">Karyawan Aktif</div>
                        <div class="stat-value">{{ $stats['activeEmployees'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <div class="card h-100">
                <div class="card-body stat-card">
                    <span class="stat-icon bg-info-subtle text-info-emphasis"><i class="bi bi-diagram-3"></i></span>
                    <div>
                        <div class="stat-label">Departemen</div>
                        <div class="stat-value">{{ $stats['departments'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <div class="card h-100">
                <div class="card-body stat-card">
                    <span class="stat-icon bg-secondary-subtle text-secondary-emphasis"><i class="bi bi-briefcase"></i></span>
                    <div>
                        <div class="stat-label">Jabatan</div>
                        <div class="stat-value">{{ $stats['positions'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <div class="card h-100">
                <div class="card-body stat-card">
                    <span class="stat-icon bg-success-subtle text-success-emphasis"><i class="bi bi-clock-history"></i></span>
                    <div>
                        <div class="stat-label">Absensi Hari Ini</div>
                        <div class="stat-value">{{ $stats['todayAttendance'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <div class="card h-100">
                <div class="card-body stat-card">
                    <span class="stat-icon bg-warning-subtle text-warning-emphasis"><i class="bi bi-hourglass-split"></i></span>
                    <div>
                        <div class="stat-label">Cuti Menunggu</div>
                        <div class="stat-value">{{ $stats['pendingLeaves'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <div class="card h-100">
                <div class="card-body stat-card">
                    <span class="stat-icon bg-danger-subtle text-danger-emphasis"><i class="bi bi-calendar-x"></i></span>
                    <div>
                        <div class="stat-label">Hari Libur</div>
                        <div class="stat-value">{{ $stats['holidays'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-lightning-charge me-2"></i>Akses Cepat</span>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('attendances.index') }}" class="btn btn-outline-primary"><i class="bi bi-clock-history me-1"></i>Riwayat Absensi</a>
                <a href="{{ route('leave-requests.index') }}" class="btn btn-outline-primary"><i class="bi bi-calendar2-plus me-1"></i>Ajukan Cuti</a>

                @if ($user->isAdmin() || $user->isManager())
                    <a href="{{ route('approvals.index') }}" class="btn btn-outline-success"><i class="bi bi-check2-square me-1"></i>Persetujuan</a>
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-success"><i class="bi bi-file-earmark-bar-graph me-1"></i>Rekap Bulanan</a>
                @endif

                @if ($user->isAdmin())
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary"><i class="bi bi-people me-1"></i>Kelola Karyawan</a>
                    <a href="{{ route('work-settings.edit') }}" class="btn btn-outline-secondary"><i class="bi bi-clock me-1"></i>Jam Kerja</a>
                @endif

                <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary"><i class="bi bi-person me-1"></i>Profil Saya</a>
            </div>
        </div>
    </div>
</x-app-layout>
