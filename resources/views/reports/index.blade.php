<x-app-layout>
    <x-slot name="title">Rekap Bulanan</x-slot>

    <x-page-header
        title="Rekap Bulanan"
        subtitle="Ringkasan kehadiran, keterlambatan, dan cuti karyawan per periode."
    >
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('reports.export.excel', array_filter($filters)) }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
            </a>
            <a href="{{ route('reports.export.pdf', array_filter($filters)) }}" class="btn btn-danger">
                <i class="bi bi-file-earmark-pdf me-1"></i>Export PDF
            </a>
        </div>
    </x-page-header>

    <div class="card mb-4">
        <div class="card-header">
            <span class="section-title mb-0"><i class="bi bi-funnel"></i>Filter Periode</span>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.index') }}" class="hris-filterbar">
                <div class="filter-field">
                    <label for="month">Bulan</label>
                    <select name="month" id="month" class="form-select form-select-sm">
                        @foreach ($monthNames as $number => $label)
                            <option value="{{ $number }}" @selected($filters['month'] === $number)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-field" style="max-width: 120px;">
                    <label for="year">Tahun</label>
                    <input type="number" name="year" id="year" value="{{ $filters['year'] }}" class="form-control form-control-sm" min="2000" max="2100">
                </div>
                @if ($canViewAll)
                    <div class="filter-field">
                        <label for="department_id">Departemen</label>
                        <select name="department_id" id="department_id" class="form-select form-select-sm">
                            <option value="">Semua departemen</option>
                            @foreach ($departments as $id => $name)
                                <option value="{{ $id }}" @selected($filters['department_id'] === $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-field">
                        <label for="user_id">Karyawan</label>
                        <select name="user_id" id="user_id" class="form-select form-select-sm">
                            <option value="">Semua karyawan</option>
                            @foreach ($filterUsers as $id => $name)
                                <option value="{{ $id }}" @selected($filters['user_id'] === $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="filter-actions">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-funnel me-1"></i>Tampilkan
                    </button>
                    <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-4 col-xl-2">
            <x-stat-card icon="bi-people" label="Karyawan" :value="$summary['employees']" />
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <x-stat-card icon="bi-calendar3" label="Hari Kerja" :value="$summary['working_days']" variant="secondary" />
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <x-stat-card icon="bi-check2-circle" label="Total Absen" :value="$summary['records']" variant="success" />
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <x-stat-card icon="bi-graph-up-arrow" label="Kehadiran" :value="$summary['attendance_rate'].'%'" variant="info" />
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <x-stat-card icon="bi-clock-history" label="Terlambat" :value="$summary['terlambat']" variant="warning" />
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <x-stat-card icon="bi-calendar2-check" label="Cuti Disetujui" :value="$summary['leave_days']" variant="danger" />
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="section-title mb-0"><i class="bi bi-bar-chart"></i>Distribusi Status Absensi</span>
            <span class="text-muted small">
                {{ format_tanggal($period['start']) }} — {{ format_tanggal($period['end']) }}
            </span>
        </div>
        <div class="card-body" style="height: 320px;">
            <canvas id="attendanceStatusChart" aria-label="Grafik distribusi status absensi" role="img"></canvas>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="section-title mb-0"><i class="bi bi-table"></i>Rekap per Karyawan — {{ $period['label'] }}</span>
            <span class="text-muted small">Hari kerja dihitung hingga tanggal akhir periode atau hari ini.</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Karyawan</th>
                        <th>Departemen</th>
                        <th class="text-center">Hari Kerja</th>
                        <th class="text-center">Hadir</th>
                        <th class="text-center">Terlambat</th>
                        <th class="text-center">Pulang Cepat</th>
                        <th class="text-center">Alpha</th>
                        <th class="text-center">Total Absen</th>
                        <th class="text-center">Cuti</th>
                        <th class="text-center">Kehadiran</th>
                        <th class="text-center">Belum Clock Out</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $row['name'] }}</div>
                                <div class="text-muted small">{{ $row['nik'] }}</div>
                            </td>
                            <td>{{ $row['department'] }}</td>
                            <td class="text-center">{{ $row['working_days'] }}</td>
                            <td class="text-center">{{ $row['hadir'] }}</td>
                            <td class="text-center">{{ $row['terlambat'] }}</td>
                            <td class="text-center">{{ $row['pulang_cepat'] }}</td>
                            <td class="text-center">{{ $row['alpha'] }}</td>
                            <td class="text-center fw-semibold">{{ $row['total_records'] }}</td>
                            <td class="text-center">{{ $row['leave_days'] }}</td>
                            <td class="text-center">
                                <span class="badge {{ $row['attendance_rate'] >= 90 ? 'bg-success-subtle text-success-emphasis' : ($row['attendance_rate'] >= 75 ? 'bg-warning-subtle text-warning-emphasis' : 'bg-danger-subtle text-danger-emphasis') }}">
                                    {{ $row['attendance_rate'] }}%
                                </span>
                            </td>
                            <td class="text-center">{{ $row['incomplete'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11">
                                <x-empty-state
                                    icon="bi-file-earmark-bar-graph"
                                    message="Belum ada karyawan pada filter ini."
                                    description="Ubah filter departemen atau karyawan untuk melihat rekap."
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
        <script>
            const attendanceChartData = {{ \Illuminate\Support\Js::from($chartData) }};
            const attendanceChartCanvas = document.getElementById('attendanceStatusChart');

            if (attendanceChartCanvas && typeof Chart !== 'undefined') {
                const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
                const tickColor = isDark ? '#9ca3af' : '#667085';
                const gridColor = isDark ? 'rgba(255, 255, 255, .08)' : 'rgba(16, 24, 40, .06)';

                new Chart(attendanceChartCanvas, {
                    type: 'bar',
                    data: attendanceChartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { callbacks: { label: (context) => `${context.parsed.y} kali` } }
                        },
                        scales: {
                            x: { ticks: { color: tickColor }, grid: { display: false } },
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0, color: tickColor },
                                grid: { color: gridColor }
                            }
                        }
                    }
                });
            }
        </script>
    @endpush
</x-app-layout>
