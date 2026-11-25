<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeaveTypeRequest;
use App\Http\Requests\UpdateLeaveTypeRequest;
use App\Models\LeaveType;
use App\Services\ActivityLogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class LeaveTypeController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(LeaveType::class, 'leaveType');
    }

    public function index(): View
    {
        $leaveTypes = LeaveType::query()
            ->withCount('leaveRequests')
            ->orderBy('name')
            ->paginate(10);

        return view('leave-types.index', compact('leaveTypes'));
    }

    public function create(): View
    {
        return view('leave-types.form', [
            'leaveType' => new LeaveType,
            'isEditing' => false,
        ]);
    }

    public function store(StoreLeaveTypeRequest $request, ActivityLogService $activityLog): RedirectResponse
    {
        $data = $request->validated();
        $data['deducts_balance'] = $request->boolean('deducts_balance');
        $data['requires_attachment'] = $request->boolean('requires_attachment');

        $leaveType = LeaveType::query()->create($data);

        $activityLog->log('leave-type.created', "Menambahkan jenis cuti {$leaveType->name}.", $leaveType);

        return redirect()->route('leave-types.index')->with('success', 'Jenis cuti berhasil ditambahkan.');
    }

    public function edit(LeaveType $leaveType): View
    {
        return view('leave-types.form', [
            'leaveType' => $leaveType,
            'isEditing' => true,
        ]);
    }

    public function update(
        UpdateLeaveTypeRequest $request,
        LeaveType $leaveType,
        ActivityLogService $activityLog,
    ): RedirectResponse {
        $data = $request->validated();
        $data['deducts_balance'] = $request->boolean('deducts_balance');
        $data['requires_attachment'] = $request->boolean('requires_attachment');

        $leaveType->update($data);

        $activityLog->log('leave-type.updated', "Memperbarui jenis cuti {$leaveType->name}.", $leaveType);

        return redirect()->route('leave-types.index')->with('success', 'Jenis cuti berhasil diperbarui.');
    }

    public function destroy(LeaveType $leaveType, ActivityLogService $activityLog): RedirectResponse
    {
        if ($leaveType->leaveRequests()->exists()) {
            return back()->with('error', 'Jenis cuti tidak bisa dihapus karena sudah memiliki pengajuan.');
        }

        $name = $leaveType->name;
        $activityLog->log('leave-type.deleted', "Menghapus jenis cuti {$name}.", $leaveType);
        $leaveType->delete();

        return redirect()->route('leave-types.index')->with('success', 'Jenis cuti berhasil dihapus.');
    }
}
