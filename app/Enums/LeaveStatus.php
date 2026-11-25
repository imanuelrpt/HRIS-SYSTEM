<?php

namespace App\Enums;

enum LeaveStatus: string
{
    case PendingManager = 'pending_manager';
    case PendingHr = 'pending_hr';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PendingManager => 'Menunggu Atasan',
            self::PendingHr => 'Menunggu HR',
            self::Approved => 'Disetujui',
            self::Rejected => 'Ditolak',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PendingManager, self::PendingHr => 'bg-warning-subtle text-warning-emphasis',
            self::Approved => 'bg-success-subtle text-success-emphasis',
            self::Rejected => 'bg-danger-subtle text-danger-emphasis',
            self::Cancelled => 'bg-secondary-subtle text-secondary-emphasis',
        };
    }

    public function isPending(): bool
    {
        return in_array($this, [self::PendingManager, self::PendingHr], true);
    }
}
