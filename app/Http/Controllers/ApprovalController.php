<?php

namespace App\Http\Controllers;

use App\Enums\ApprovalAction;
use App\Enums\LeaveStatus;
use App\Http\Requests\DecisionLeaveRequestRequest;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Services\LeaveApprovalService;
use DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ApprovalController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAll', LeaveRequest::class);

        /** @var User $user */
        $user = $request->user();
        $levelFilter = $request->integer('level');

        if (! in_array($levelFilter, [1, 2], true)) {
            $levelFilter = 0;
        }

        if ($user->isManager()) {
            $levelFilter = 1;
        }

        $leaveRequests = LeaveRequest::query()
            ->pending()
            ->with([
                'user:id,name,email,manager_id',
                'leaveType:id,name',
                'approvals.approver:id,name',
            ])
            ->when($user->isManager(), fn ($query) => $query->whereHas(
                'user',
                fn ($query) => $query->where('manager_id', $user->id),
            ))
            ->when($levelFilter === 1, fn ($query) => $query->where('status', LeaveStatus::PendingManager->value))
            ->when($levelFilter === 2, fn ($query) => $query->where('status', LeaveStatus::PendingHr->value))
            ->latest('created_at')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('approvals.index', [
            'leaveRequests' => $leaveRequests,
            'levelFilter' => $levelFilter,
            'canApproveHr' => $user->isAdmin(),
        ]);
    }

    public function decision(
        DecisionLeaveRequestRequest $request,
        LeaveRequest $leaveRequest,
        LeaveApprovalService $approvals,
    ): RedirectResponse {
        $action = ApprovalAction::from($request->string('action')->toString());
        $ability = $action === ApprovalAction::Approve ? 'approve' : 'reject';

        Gate::authorize($ability, $leaveRequest);

        /** @var User $approver */
        $approver = $request->user();
        $note = $request->string('note')->toString() ?: null;

        try {
            $action === ApprovalAction::Approve
                ? $approvals->approve($leaveRequest, $approver, $note)
                : $approvals->reject($leaveRequest, $approver, $note);
        } catch (DomainException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with(
            'success',
            $action === ApprovalAction::Approve
                ? 'Pengajuan cuti berhasil disetujui.'
                : 'Pengajuan cuti berhasil ditolak.',
        );
    }
}
