<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Models\Department;
use App\Services\ActivityLogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Department::class, 'department');
    }

    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $departments = Department::query()
            ->withCount(['users', 'positions'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('departments.index', compact('departments', 'search'));
    }

    public function create(): View
    {
        return view('departments.form', [
            'department' => new Department,
            'isEditing' => false,
        ]);
    }

    public function store(StoreDepartmentRequest $request, ActivityLogService $activityLog): RedirectResponse
    {
        $department = Department::query()->create($request->validated());

        $activityLog->log('department.created', "Menambahkan departemen {$department->name}.", $department);

        return redirect()->route('departments.index')->with('success', 'Departemen berhasil ditambahkan.');
    }

    public function edit(Department $department): View
    {
        return view('departments.form', [
            'department' => $department,
            'isEditing' => true,
        ]);
    }

    public function update(
        UpdateDepartmentRequest $request,
        Department $department,
        ActivityLogService $activityLog,
    ): RedirectResponse {
        $department->update($request->validated());

        $activityLog->log('department.updated', "Memperbarui departemen {$department->name}.", $department);

        return redirect()->route('departments.index')->with('success', 'Departemen berhasil diperbarui.');
    }

    public function destroy(Department $department, ActivityLogService $activityLog): RedirectResponse
    {
        if ($department->users()->exists() || $department->positions()->exists()) {
            return back()->with('error', 'Departemen tidak bisa dihapus karena masih memiliki karyawan atau jabatan.');
        }

        $name = $department->name;
        $activityLog->log('department.deleted', "Menghapus departemen {$name}.", $department);
        $department->delete();

        return redirect()->route('departments.index')->with('success', 'Departemen berhasil dihapus.');
    }
}
