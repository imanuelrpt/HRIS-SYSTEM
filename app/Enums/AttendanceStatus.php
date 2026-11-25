<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case Hadir = 'hadir';
    case Terlambat = 'terlambat';
    case PulangCepat = 'pulang_cepat';
    case Alpha = 'alpha';

    public function label(): string
    {
        return match ($this) {
            self::Hadir => 'Hadir',
            self::Terlambat => 'Terlambat',
            self::PulangCepat => 'Pulang Cepat',
            self::Alpha => 'Alpha',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Hadir => 'bg-success-subtle text-success-emphasis',
            self::Terlambat => 'bg-warning-subtle text-warning-emphasis',
            self::PulangCepat => 'bg-info-subtle text-info-emphasis',
            self::Alpha => 'bg-danger-subtle text-danger-emphasis',
        };
    }
}
