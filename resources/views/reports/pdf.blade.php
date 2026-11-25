<!doctype html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Rekap Absensi {{ $period['label'] }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            color: #212529;
            margin: 0;
        }

        .header {
            border-bottom: 2px solid #198754;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }

        .header h1 {
            font-size: 18px;
            margin: 0 0 4px;
            color: #146c43;
        }

        .header p {
            margin: 0;
            color: #6c757d;
        }

        .summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .summary td {
            border: 1px solid #dee2e6;
            padding: 6px;
            text-align: center;
        }

        .summary .label {
            color: #6c757d;
            font-size: 8px;
            text-transform: uppercase;
        }

        .summary .value {
            font-size: 13px;
            font-weight: bold;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
        }

        table.data th {
            background-color: #198754;
            color: #ffffff;
            font-size: 8px;
            text-transform: uppercase;
            padding: 6px 4px;
            border: 1px solid #146c43;
        }

        table.data td {
            border: 1px solid #dee2e6;
            padding: 5px 4px;
            text-align: center;
        }

        table.data td.left {
            text-align: left;
        }

        table.data tr:nth-child(even) td {
            background-color: #f8f9fa;
        }

        .footer {
            margin-top: 14px;
            color: #6c757d;
            font-size: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>HRIS Mini — Rekap Absensi Bulanan</h1>
        <p>
            Periode: {{ $period['label'] }} |
            Rentang: {{ format_tanggal($period['start']) }} sampai {{ format_tanggal($period['end']) }}
        </p>
        <p>Dicetak pada: {{ format_tanggal_waktu(now()) }} oleh {{ auth()->user()?->name ?? 'Sistem' }}</p>
    </div>

    <table class="summary">
        <tr>
            <td>
                <div class="label">Karyawan</div>
                <div class="value">{{ $summary['employees'] }}</div>
            </td>
            <td>
                <div class="label">Hari Kerja</div>
                <div class="value">{{ $summary['working_days'] }}</div>
            </td>
            <td>
                <div class="label">Total Absen</div>
                <div class="value">{{ $summary['records'] }}</div>
            </td>
            <td>
                <div class="label">Kehadiran</div>
                <div class="value">{{ $summary['attendance_rate'] }}%</div>
            </td>
            <td>
                <div class="label">Terlambat</div>
                <div class="value">{{ $summary['terlambat'] }}</div>
            </td>
            <td>
                <div class="label">Cuti Disetujui</div>
                <div class="value">{{ $summary['leave_days'] }}</div>
            </td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th>NIK</th>
                <th>Nama Karyawan</th>
                <th>Departemen</th>
                <th>Hari Kerja</th>
                <th>Hadir</th>
                <th>Terlambat</th>
                <th>Pulang Cepat</th>
                <th>Alpha</th>
                <th>Cuti</th>
                <th>Kehadiran</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td class="left">{{ $row['nik'] }}</td>
                    <td class="left">{{ $row['name'] }}</td>
                    <td class="left">{{ $row['department'] }}</td>
                    <td>{{ $row['working_days'] }}</td>
                    <td>{{ $row['hadir'] }}</td>
                    <td>{{ $row['terlambat'] }}</td>
                    <td>{{ $row['pulang_cepat'] }}</td>
                    <td>{{ $row['alpha'] }}</td>
                    <td>{{ $row['leave_days'] }}</td>
                    <td>{{ $row['attendance_rate'] }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10">Tidak ada karyawan pada filter yang dipilih.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Hari kerja dihitung berdasarkan jam kerja dan hari libur yang dikonfigurasi, hingga akhir periode atau hari ini untuk bulan berjalan.
    </div>
</body>
</html>
