<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePositionRequest;
use App\Http\Requests\UpdatePositionRequest;
use App\Models\Department;
use App\Models\Position;
use App\Services\ActivityLogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Position::class, 'position');
    }

    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $departmentId = $request->integer('department_id');

        $positions = Position::query()
            ->with('department:id,name')
            ->withCount('users')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($departmentId > 0, fn ($query) => $query->where('department_id', $departmentId))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('positions.index', [
            'positions' => $positions,
            'search' => $search,
            'departmentId' => $departmentId,
            'departments' => Department::query()->orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function create(): View
    {
        return view('positions.form', [
            'position' => new Position,
            'isEditing' => false,
            'departments' => Department::query()->orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function store(StorePositionRequest $request, ActivityLogService $activityLog): RedirectResponse
    {
        $position = Position::query()->create($request->validated());

        $activityLog->log('position.created', "Menambahkan jabatan {$position->name}.", $position);

        return redirect()->route('positions.index')->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function edit(Position $position): View
    {
        return view('positions.form', [
            'position' => $position,
            'isEditing' => true,
            'departments' => Department::query()->orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function update(
        UpdatePositionRequest $request,
        Position $position,
        ActivityLogService $activityLog,
    ): RedirectResponse {
        $position->update($request->validated());

        $activityLog->log('position.updated', "Memperbarui jabatan {$position->name}.", $position);

        return redirect()->route('positions.index')->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function destroy(Position $position, ActivityLogService $activityLog): RedirectResponse
    {
        if ($position->users()->exists()) {
            return back()->with('error', 'Jabatan tidak bisa dihapus karena masih digunakan oleh karyawan.');
        }

        $name = $position->name;
        $activityLog->log('position.deleted', "Menghapus jabatan {$name}.", $position);
        $position->delete();

        return redirect()->route('positions.index')->with('success', 'Jabatan berhasil dihapus.');
    }
}
