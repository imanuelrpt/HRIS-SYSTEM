<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <x-page-header
        title="Dashboard"
        subtitle="Ringkasan kehadiran dan kepegawaian hari ini."
    >
        <span class="badge bg-secondary-subtle text-secondary-emphasis">{{ $user->role->label() }}</span>
    </x-page-header>

    <div class="row g-3">
        <div class="col-6 col-lg-4 col-xl-2">
            <x-stat-card icon="bi-people" label="Karyawan Aktif" :value="$stats['activeEmployees']" />
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <x-stat-card icon="bi-diagram-3" label="Departemen" :value="$stats['departments']" />
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <x-stat-card icon="bi-briefcase" label="Jabatan" :value="$stats['positions']" />
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <x-stat-card icon="bi-clock-history" label="Absensi Hari Ini" :value="$stats['todayAttendance']" />
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <x-stat-card icon="bi-hourglass-split" label="Cuti Menunggu" :value="$stats['pendingLeaves']" />
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <x-stat-card icon="bi-calendar-x" label="Hari Libur" :value="$stats['holidays']" />
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <span class="section-title mb-0"><i class="bi bi-arrow-right-square"></i>Akses Cepat</span>
        </div>
        <div class="card-body">
            <div class="quick-actions">
                <a href="{{ route('attendances.index') }}" class="quick-action">
                    <span class="quick-action-icon"><i class="bi bi-clock-history"></i></span>
                    <span class="quick-action-title">Riwayat Absensi</span>
                </a>
                <a href="{{ route('leave-requests.index') }}" class="quick-action">
                    <span class="quick-action-icon"><i class="bi bi-calendar2-plus"></i></span>
                    <span class="quick-action-title">Ajukan Cuti</span>
                </a>

                @if ($user->isAdmin() || $user->isManager())
                    <a href="{{ route('approvals.index') }}" class="quick-action">
                        <span class="quick-action-icon"><i class="bi bi-check2-square"></i></span>
                        <span class="quick-action-title">Persetujuan</span>
                    </a>
                    <a href="{{ route('reports.index') }}" class="quick-action">
                        <span class="quick-action-icon"><i class="bi bi-file-earmark-bar-graph"></i></span>
                        <span class="quick-action-title">Rekap Bulanan</span>
                    </a>
                @endif

                @if ($user->isAdmin())
                    <a href="{{ route('users.index') }}" class="quick-action">
                        <span class="quick-action-icon"><i class="bi bi-people"></i></span>
                        <span class="quick-action-title">Kelola Karyawan</span>
                    </a>
                    <a href="{{ route('work-settings.edit') }}" class="quick-action">
                        <span class="quick-action-icon"><i class="bi bi-clock"></i></span>
                        <span class="quick-action-title">Jam Kerja</span>
                    </a>
                @endif

                <a href="{{ route('profile.edit') }}" class="quick-action">
                    <span class="quick-action-icon"><i class="bi bi-person"></i></span>
                    <span class="quick-action-title">Profil Saya</span>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
