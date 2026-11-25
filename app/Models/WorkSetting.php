<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'check_in',
        'check_out',
        'late_tolerance_minutes',
        'working_days',
    ];

    protected $casts = [
        'late_tolerance_minutes' => 'integer',
        'working_days' => 'array',
    ];

    public static function current(): self
    {
        return static::query()->firstOrFail();
    }
}
