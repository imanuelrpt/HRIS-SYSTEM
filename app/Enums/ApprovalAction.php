<?php

namespace App\Enums;

enum ApprovalAction: string
{
    case Approve = 'approve';
    case Reject = 'reject';

    public function label(): string
    {
        return match ($this) {
            self::Approve => 'Disetujui',
            self::Reject => 'Ditolak',
        };
    }

    public function badgeClass(): string
    {
        return $this === self::Approve
            ? 'bg-success-subtle text-success-emphasis'
            : 'bg-danger-subtle text-danger-emphasis';
    }
}
