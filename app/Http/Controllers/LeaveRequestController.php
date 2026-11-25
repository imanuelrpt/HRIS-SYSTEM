<?php

namespace App\Http\Controllers;

use App\Enums\LeaveStatus;
use App\Http\Requests\StoreLeaveRequestRequest;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use App\Services\LeaveRequestService;
use DomainException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LeaveRequestController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', LeaveRequest::class);

        /** @var User $user */
        $user = $request->user();
        $canViewAll = Gate::allows('viewAll', LeaveRequest::class);

        $statusFilter = $request->string('status')->toString();
        $statusOptions = collect(LeaveStatus::cases())
            ->mapWithKeys(fn (LeaveStatus $status) => [$status->value => $status->label()]);

        $leaveTypeId = $request->integer('leave_type_id');
        $selectedUserId = $canViewAll ? $request->integer('user_id') : $user->id;
        $month = $request->integer('month');
        $year = $request->integer('year');

        $leaveRequests = LeaveRequest::query()
            ->with([
                'user:id,name',
                'leaveType:id,name',
            ])
            ->when(! $canViewAll, fn ($query) => $query->where('user_id', $user->id))
            ->when($canViewAll && $selectedUserId > 0, fn ($query) => $query->where('user_id', $selectedUserId))
            ->when($statusOptions->has($statusFilter), fn ($query) => $query->where('status', $statusFilter))
            ->when($leaveTypeId > 0, fn ($query) => $query->where('leave_type_id', $leaveTypeId))
            ->when($month >= 1 && $month <= 12, fn ($query) => $query->whereMonth('start_date', $month))
            ->when($year >= 2000 && $year <= 2100, fn ($query) => $query->whereYear('start_date', $year))
            ->latest('created_at')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('leave-requests.index', [
            'leaveRequests' => $leaveRequests,
            'canViewAll' => $canViewAll,
            'users' => $canViewAll
                ? User::query()->active()->orderBy('name')->pluck('name', 'id')
                : collect(),
            'leaveTypes' => LeaveType::query()->orderBy('name')->pluck('name', 'id'),
            'statusOptions' => $statusOptions,
            'statusFilter' => $statusFilter,
            'selectedUserId' => $selectedUserId,
            'leaveTypeId' => $leaveTypeId,
            'month' => $month,
            'year' => $year,
        ]);
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', LeaveRequest::class);

        /** @var User $user */
        $user = $request->user();

        return view('leave-requests.create', [
            'leaveTypes' => LeaveType::query()
                ->orderBy('name')
                ->get(['id', 'name', 'quota_days', 'deducts_balance', 'requires_attachment']),
            'balances' => LeaveBalance::query()
                ->whereBelongsTo($user)
                ->where('year', now()->year)
                ->get()
                ->keyBy('leave_type_id'),
        ]);
    }

    public function store(
        StoreLeaveRequestRequest $request,
        LeaveRequestService $leaveRequests,
    ): RedirectResponse {
        Gate::authorize('create', LeaveRequest::class);

        /** @var User $user */
        $user = $request->user();
        $data = $request->validated();
        $data['attachment'] = $request->file('attachment');

        try {
            $leaveRequest = $leaveRequests->create($user, $data);
        } catch (DomainException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('leave-requests.index')
            ->with('success', "Pengajuan cuti #{$leaveRequest->id} berhasil dikirim untuk persetujuan atasan.");
    }

    public function cancel(
        Request $request,
        LeaveRequest $leaveRequest,
        LeaveRequestService $leaveRequests,
    ): RedirectResponse {
        Gate::authorize('cancel', $leaveRequest);

        /** @var User $user */
        $user = $request->user();

        try {
            $leaveRequests->cancel($user, $leaveRequest);
        } catch (DomainException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Pengajuan cuti berhasil dibatalkan.');
    }
}
