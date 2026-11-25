<?php

namespace App\Services;

use App\Models\Holiday;
use App\Models\WorkSetting;
use Carbon\Carbon;

class WorkCalendarService
{
    /** @return array<int, int> Daftar nomor hari ISO (1 = Senin). */
    public function workingDays(): array
    {
        return WorkSetting::current()->working_days;
    }

    public function isWorkingDay(Carbon $date): bool
    {
        return in_array($date->isoWeekday(), $this->workingDays(), true)
            && ! Holiday::query()->whereDate('date', $date->toDateString())->exists();
    }

    /**
     * Menghitung jumlah hari kerja dalam rentang tanggal (inklusif).
     * Sabtu/Minggu dan tanggal pada tabel holidays tidak dihitung.
     */
    public function countWorkingDays(Carbon $start, Carbon $end): int
    {
        if ($end->lessThan($start)) {
            return 0;
        }

        $holidays = Holiday::query()
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->pluck('date')
            ->map(fn ($date) => $date->toDateString())
            ->all();

        $days = 0;
        $cursor = $start->copy()->startOfDay();

        while ($cursor->lessThanOrEqualTo($end)) {
            if (in_array($cursor->isoWeekday(), $this->workingDays(), true)
                && ! in_array($cursor->toDateString(), $holidays, true)) {
                $days++;
            }

            $cursor->addDay();
        }

        return $days;
    }
}
