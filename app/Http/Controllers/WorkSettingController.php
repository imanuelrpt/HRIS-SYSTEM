<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateWorkSettingRequest;
use App\Models\WorkSetting;
use App\Services\ActivityLogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class WorkSettingController extends Controller
{
    public function edit(): View
    {
        $workSetting = WorkSetting::query()->firstOrFail();
        Gate::authorize('view', $workSetting);

        return view('work-settings.edit', compact('workSetting'));
    }

    public function update(UpdateWorkSettingRequest $request, ActivityLogService $activityLog): RedirectResponse
    {
        $workSetting = WorkSetting::query()->firstOrFail();
        Gate::authorize('update', $workSetting);

        $data = $request->validated();
        $data['working_days'] = collect($request->input('working_days', []))
            ->map(fn ($day) => (int) $day)
            ->values()
            ->all();

        $workSetting->update($data);

        $activityLog->log('work-setting.updated', 'Memperbarui pengaturan jam kerja dan hari kerja.', $workSetting);

        return redirect()->route('work-settings.edit')->with('success', 'Pengaturan jam kerja berhasil diperbarui.');
    }
}
