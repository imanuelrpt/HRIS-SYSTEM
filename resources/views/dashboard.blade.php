<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <div class="hris-hero d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <span class="badge mb-2"><i class="bi bi-stars me-1"></i>Ringkasan Hari Ini</span>
            <h1>Halo, {{ $user->name }} 👋</h1>
            <p>Berikut ringkasan data kehadiran dan kepegawaian hari ini.</p>
        </div>
        <span class="badge fs-6">
            <i class="bi bi-person-badge me-1"></i>{{ $user->role->label() }}
        </span>
    </div>

    <div class="row g-3">
        <div class="col-6 col-lg-4 col-xl-2">
            <x-stat-card icon="bi-people" label="Karyawan Aktif" :value="$stats['activeEmployees']" />
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <x-stat-card icon="bi-diagram-3" label="Departemen" :value="$stats['departments']" variant="info" />
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <x-stat-card icon="bi-briefcase" label="Jabatan" :value="$stats['positions']" variant="secondary" />
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <x-stat-card icon="bi-clock-history" label="Absensi Hari Ini" :value="$stats['todayAttendance']" variant="success" />
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <x-stat-card icon="bi-hourglass-split" label="Cuti Menunggu" :value="$stats['pendingLeaves']" variant="warning" />
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <x-stat-card icon="bi-calendar-x" label="Hari Libur" :value="$stats['holidays']" variant="danger" />
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="section-title mb-0"><i class="bi bi-lightning-charge"></i>Akses Cepat</span>
            <span class="text-muted small">Pintasan menu yang paling sering digunakan</span>
        </div>
        <div class="card-body">
            <div class="quick-actions">
                <a href="{{ route('attendances.index') }}" class="quick-action">
                    <span class="quick-action-icon"><i class="bi bi-clock-history"></i></span>
                    <span>
                        <span class="quick-action-title d-block">Riwayat Absensi</span>
                        <span class="quick-action-desc d-block">Clock in/out dan lihat riwayat</span>
                    </span>
                </a>
                <a href="{{ route('leave-requests.index') }}" class="quick-action">
                    <span class="quick-action-icon"><i class="bi bi-calendar2-plus"></i></span>
                    <span>
                        <span class="quick-action-title d-block">Ajukan Cuti</span>
                        <span class="quick-action-desc d-block">Buat dan pantau pengajuan</span>
                    </span>
                </a>

                @if ($user->isAdmin() || $user->isManager())
                    <a href="{{ route('approvals.index') }}" class="quick-action">
                        <span class="quick-action-icon"><i class="bi bi-check2-square"></i></span>
                        <span>
                            <span class="quick-action-title d-block">Persetujuan</span>
                            <span class="quick-action-desc d-block">Tinjau antrian cuti</span>
                        </span>
                    </a>
                    <a href="{{ route('reports.index') }}" class="quick-action">
                        <span class="quick-action-icon"><i class="bi bi-file-earmark-bar-graph"></i></span>
                        <span>
                            <span class="quick-action-title d-block">Rekap Bulanan</span>
                            <span class="quick-action-desc d-block">Unduh laporan kehadiran</span>
                        </span>
                    </a>
                @endif

                @if ($user->isAdmin())
                    <a href="{{ route('users.index') }}" class="quick-action">
                        <span class="quick-action-icon"><i class="bi bi-people"></i></span>
                        <span>
                            <span class="quick-action-title d-block">Kelola Karyawan</span>
                            <span class="quick-action-desc d-block">Atur akun dan peran</span>
                        </span>
                    </a>
                    <a href="{{ route('work-settings.edit') }}" class="quick-action">
                        <span class="quick-action-icon"><i class="bi bi-clock"></i></span>
                        <span>
                            <span class="quick-action-title d-block">Jam Kerja</span>
                            <span class="quick-action-desc d-block">Sesuaikan jadwal absensi</span>
                        </span>
                    </a>
                @endif

                <a href="{{ route('profile.edit') }}" class="quick-action">
                    <span class="quick-action-icon"><i class="bi bi-person"></i></span>
                    <span>
                        <span class="quick-action-title d-block">Profil Saya</span>
                        <span class="quick-action-desc d-block">Perbarui data pribadi</span>
                    </span>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
