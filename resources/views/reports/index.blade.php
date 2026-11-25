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
        <div class="card-body">
            <form method="GET" action="{{ route('reports.index') }}" class="row g-2 align-items-end">
                <div class="col-6 col-md-2">
                    <label for="month" class="form-label small text-muted mb-1">Bulan</label>
                    <select name="month" id="month" class="form-select form-select-sm">
                        @foreach ($monthNames as $number => $label)
                            <option value="{{ $number }}" @selected($filters['month'] === $number)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label for="year" class="form-label small text-muted mb-1">Tahun</label>
                    <input type="number" name="year" id="year" value="{{ $filters['year'] }}" class="form-control form-control-sm" min="2000" max="2100">
                </div>
                @if ($canViewAll)
                    <div class="col-6 col-md-3">
                        <label for="department_id" class="form-label small text-muted mb-1">Departemen</label>
                        <select name="department_id" id="department_id" class="form-select form-select-sm">
                            <option value="">Semua departemen</option>
                            @foreach ($departments as $id => $name)
                                <option value="{{ $id }}" @selected($filters['department_id'] === $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label for="user_id" class="form-label small text-muted mb-1">Karyawan</label>
                        <select name="user_id" id="user_id" class="form-select form-select-sm">
                            <option value="">Semua karyawan</option>
                            @foreach ($filterUsers as $id => $name)
                                <option value="{{ $id }}" @selected($filters['user_id'] === $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="col-12 col-md-auto d-flex gap-2">
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
            <div class="card h-100">
                <div class="card-body stat-card">
                    <span class="stat-icon bg-primary-subtle text-primary-emphasis"><i class="bi bi-people"></i></span>
                    <div>
                        <div class="stat-label">Karyawan</div>
                        <div class="stat-value">{{ $summary['employees'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <div class="card h-100">
                <div class="card-body stat-card">
                    <span class="stat-icon bg-secondary-subtle text-secondary-emphasis"><i class="bi bi-calendar3"></i></span>
                    <div>
                        <div class="stat-label">Hari Kerja</div>
                        <div class="stat-value">{{ $summary['working_days'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <div class="card h-100">
                <div class="card-body stat-card">
                    <span class="stat-icon bg-success-subtle text-success-emphasis"><i class="bi bi-check2-circle"></i></span>
                    <div>
                        <div class="stat-label">Total Absen</div>
                        <div class="stat-value">{{ $summary['records'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <div class="card h-100">
                <div class="card-body stat-card">
                    <span class="stat-icon bg-info-subtle text-info-emphasis"><i class="bi bi-graph-up-arrow"></i></span>
                    <div>
                        <div class="stat-label">Kehadiran</div>
                        <div class="stat-value">{{ $summary['attendance_rate'] }}%</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <div class="card h-100">
                <div class="card-body stat-card">
                    <span class="stat-icon bg-warning-subtle text-warning-emphasis"><i class="bi bi-clock-history"></i></span>
                    <div>
                        <div class="stat-label">Terlambat</div>
                        <div class="stat-value">{{ $summary['terlambat'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <div class="card h-100">
                <div class="card-body stat-card">
                    <span class="stat-icon bg-danger-subtle text-danger-emphasis"><i class="bi bi-calendar2-check"></i></span>
                    <div>
                        <div class="stat-label">Cuti Disetujui</div>
                        <div class="stat-value">{{ $summary['leave_days'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span><i class="bi bi-bar-chart me-2"></i>Distribusi Status Absensi</span>
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
            <span>Rekap per Karyawan — {{ $period['label'] }}</span>
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
                            y: { beginAtZero: true, ticks: { precision: 0 } }
                        }
                    }
                });
            }
        </script>
    @endpush
</x-app-layout>
