<x-app-layout>
    <x-slot name="title">Persetujuan Cuti</x-slot>

    <x-page-header
        title="Persetujuan Cuti"
        subtitle="Tinjau pengajuan cuti yang menunggu keputusan Anda."
    >
        @if ($canApproveHr)
            <span class="badge bg-light text-dark border fs-6">
                <i class="bi bi-shield-check me-1"></i>Level 1 & Level 2
            </span>
        @else
            <span class="badge bg-light text-dark border fs-6">
                <i class="bi bi-person-check me-1"></i>Level 1 — Atasan
            </span>
        @endif
    </x-page-header>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span>Antrian Persetujuan</span>
            @if ($canApproveHr)
                <form method="GET" action="{{ route('approvals.index') }}" class="d-flex flex-wrap gap-2">
                    <select name="level" class="form-select form-select-sm" style="width: 200px;">
                        <option value="">Semua level</option>
                        <option value="1" @selected($levelFilter === 1)>Level 1 — Atasan</option>
                        <option value="2" @selected($levelFilter === 2)>Level 2 — HR</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                </form>
            @endif
        </div>

        <div class="list-group list-group-flush">
            @forelse ($leaveRequests as $leaveRequest)
                <div class="list-group-item p-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                        <div>
                            <h5 class="mb-1">{{ $leaveRequest->user->name }}</h5>
                            <div class="text-muted small">{{ $leaveRequest->user->email }}</div>
                        </div>
                        <span class="badge fs-6 {{ $leaveRequest->status->badgeClass() }}">
                            {{ $leaveRequest->status->label() }}
                        </span>
                    </div>

                    <div class="row g-3 mt-2 small">
                        <div class="col-md-3">
                            <div class="text-muted">Jenis Cuti</div>
                            <div class="fw-semibold">{{ $leaveRequest->leaveType->name }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted">Periode</div>
                            <div class="fw-semibold">
                                {{ format_tanggal($leaveRequest->start_date) }} — {{ format_tanggal($leaveRequest->end_date) }}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-muted">Lama</div>
                            <div class="fw-semibold">{{ $leaveRequest->total_days }} hari kerja</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted">Diajukan</div>
                            <div class="fw-semibold">{{ format_tanggal_waktu($leaveRequest->created_at) }}</div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="text-muted small">Alasan</div>
                        <div>{{ $leaveRequest->reason }}</div>
                    </div>

                    @if ($leaveRequest->attachment_path)
                        <div class="mt-2">
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($leaveRequest->attachment_path) }}" target="_blank" rel="noopener" class="small">
                                <i class="bi bi-paperclip me-1"></i>Lihat lampiran
                            </a>
                        </div>
                    @endif

                    @if ($leaveRequest->approvals->isNotEmpty())
                        <div class="mt-3 border-top pt-3">
                            <div class="fw-semibold small mb-2">Riwayat Persetujuan</div>
                            @foreach ($leaveRequest->approvals as $approval)
                                <div class="d-flex flex-wrap align-items-center gap-2 small mb-1">
                                    <span class="badge {{ $approval->action->badgeClass() }}">
                                        {{ $approval->action->label() }}
                                    </span>
                                    <span>Level {{ $approval->level }} — {{ $approval->approver->name }}</span>
                                    <span class="text-muted">{{ format_tanggal_waktu($approval->acted_at) }}</span>
                                </div>
                                @if ($approval->note)
                                    <div class="text-muted small ms-1 mb-2">{{ $approval->note }}</div>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('approvals.decision', $leaveRequest) }}" class="js-loading-form mt-3 d-flex flex-wrap align-items-center gap-2">
                        @csrf
                        <input
                            type="text"
                            name="note"
                            class="form-control form-control-sm"
                            style="width: 280px;"
                            maxlength="500"
                            placeholder="Catatan (wajib saat menolak)"
                        >
                        <button type="submit" name="action" value="approve" class="btn btn-sm btn-success">
                            <i class="bi bi-check-lg me-1"></i>Setujui
                        </button>
                        <button type="submit" name="action" value="reject" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-x-lg me-1"></i>Tolak
                        </button>
                    </form>
                </div>
            @empty
                <x-empty-state
                    icon="bi-check2-square"
                    message="Tidak ada pengajuan yang menunggu persetujuan."
                    description="Antrian persetujuan baru akan muncul di sini."
                />
            @endforelse
        </div>

        @if ($leaveRequests->hasPages())
            <div class="card-body border-top">{{ $leaveRequests->links('pagination::bootstrap-5') }}</div>
        @endif
    </div>
</x-app-layout>
