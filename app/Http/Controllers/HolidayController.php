<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHolidayRequest;
use App\Http\Requests\UpdateHolidayRequest;
use App\Models\Holiday;
use App\Services\ActivityLogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Holiday::class, 'holiday');
    }

    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $year = $request->integer('year');

        $holidays = Holiday::query()
            ->when($search !== '', fn ($query) => $query->where('description', 'like', "%{$search}%"))
            ->when($year > 0, fn ($query) => $query->whereYear('date', $year))
            ->orderByDesc('date')
            ->paginate(10)
            ->withQueryString();

        return view('holidays.index', compact('holidays', 'search', 'year'));
    }

    public function create(): View
    {
        return view('holidays.form', [
            'holiday' => new Holiday,
            'isEditing' => false,
        ]);
    }

    public function store(StoreHolidayRequest $request, ActivityLogService $activityLog): RedirectResponse
    {
        $holiday = Holiday::query()->create($request->validated());

        $activityLog->log('holiday.created', "Menambahkan hari libur {$holiday->description}.", $holiday);

        return redirect()->route('holidays.index')->with('success', 'Hari libur berhasil ditambahkan.');
    }

    public function edit(Holiday $holiday): View
    {
        return view('holidays.form', [
            'holiday' => $holiday,
            'isEditing' => true,
        ]);
    }

    public function update(
        UpdateHolidayRequest $request,
        Holiday $holiday,
        ActivityLogService $activityLog,
    ): RedirectResponse {
        $holiday->update($request->validated());

        $activityLog->log('holiday.updated', "Memperbarui hari libur {$holiday->description}.", $holiday);

        return redirect()->route('holidays.index')->with('success', 'Hari libur berhasil diperbarui.');
    }

    public function destroy(Holiday $holiday, ActivityLogService $activityLog): RedirectResponse
    {
        $description = $holiday->description;
        $activityLog->log('holiday.deleted', "Menghapus hari libur {$description}.", $holiday);
        $holiday->delete();

        return redirect()->route('holidays.index')->with('success', 'Hari libur berhasil dihapus.');
    }
}
