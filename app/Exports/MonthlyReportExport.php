<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MonthlyReportExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    /**
     * @param  Collection<int, array<string, mixed>>  $rows
     * @param  array<string, mixed>  $period
     */
    public function __construct(
        private readonly Collection $rows,
        private readonly array $period,
    ) {}

    public function collection(): Collection
    {
        return $this->rows;
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'NIK',
            'Nama Karyawan',
            'Departemen',
            'Hari Kerja',
            'Hadir',
            'Terlambat',
            'Pulang Cepat',
            'Alpha',
            'Total Absen',
            'Cuti Disetujui',
            'Belum Clock Out',
            'Persentase Kehadiran',
        ];
    }

    public function map(mixed $row): array
    {
        /** @var array<string, mixed> $data */
        $data = (array) $row;

        return [
            $data['nik'],
            $data['name'],
            $data['department'],
            $data['working_days'],
            $data['hadir'],
            $data['terlambat'],
            $data['pulang_cepat'],
            $data['alpha'],
            $data['total_records'],
            $data['leave_days'],
            $data['incomplete'],
            $data['attendance_rate'].'%',
        ];
    }

    /**
     * @return array<int|string, array<string, mixed>>
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF198754'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Rekap '.($this->period['label'] ?? 'Bulanan');
    }
}
