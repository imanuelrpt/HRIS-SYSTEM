<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ActivityLogService
{
    public function __construct(private readonly Request $request) {}

    public function log(
        string $action,
        string $description,
        ?Model $subject = null,
        ?User $user = null,
    ): ActivityLog {
        return ActivityLog::query()->create([
            'user_id' => ($user ?? $this->request->user())?->id,
            'action' => $action,
            'description' => $description,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'ip' => $this->request->ip(),
        ]);
    }
}
