<?php

use Carbon\Carbon;

if (! function_exists('format_tanggal')) {
    function format_tanggal(DateTimeInterface|string|null $date): string
    {
        return $date === null
            ? '-'
            : Carbon::parse($date)->translatedFormat('d M Y');
    }
}

if (! function_exists('format_waktu')) {
    function format_waktu(DateTimeInterface|string|null $date): string
    {
        return $date === null ? '-' : Carbon::parse($date)->format('H:i');
    }
}

if (! function_exists('format_tanggal_waktu')) {
    function format_tanggal_waktu(DateTimeInterface|string|null $date): string
    {
        return $date === null
            ? '-'
            : Carbon::parse($date)->translatedFormat('d M Y H:i');
    }
}
