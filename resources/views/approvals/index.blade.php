<x-app-layout>
    <x-slot name="title">Persetujuan Cuti</x-slot>

    <x-page-header
        title="Persetujuan Cuti"
        subtitle="Tinjau pengajuan cuti yang menunggu keputusan Anda."
    >
        @if ($canApproveHr)
            <span class="hris-date-pill fs-6">
                <i class="bi bi-shield-check"></i>Level 1 &amp; Level 2
            </span>
        @else
            <span class="hris-date-pill fs-6">
                <i class="bi bi-person-check"></i>Level 1 — Atasan
            </span>
        @endif
    </x-page-header>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="section-title mb-0"><i class="bi bi-inbox"></i>Antrian Persetujuan</span>
            @if ($canApproveHr)
                <form method="GET" action="{{ route('approvals.index') }}" class="hris-filterbar">
                    <div class="filter-field" style="min-width: 210px;">
                        <label for="level">Level Persetujuan</label>
                        <select name="level" id="level" class="form-select form-select-sm">
                            <option value="">Semua level</option>
                            <option value="1" @selected($levelFilter === 1)>Level 1 — Atasan</option>
                            <option value="2" @selected($levelFilter === 2)>Level 2 — HR</option>
                        </select>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                    </div>
                </form>
            @endif
        </div>

        <div class="card-body">
            @forelse ($leaveRequests as $leaveRequest)
                <article class="approval-item">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                        <div class="d-flex align-items-center gap-3">
                            <span class="approval-avatar">{{ mb_substr($leaveRequest->user->name, 0, 1) }}</span>
                            <div>
                                <h5 class="fw-bold mb-1">{{ $leaveRequest->user->name }}</h5>
                                <div class="text-muted small">{{ $leaveRequest->user->email }}</div>
                            </div>
                        </div>
                        <span class="badge fs-6 {{ $leaveRequest->status->badgeClass() }}">
                            {{ $leaveRequest->status->label() }}
                        </span>
                    </div>

                    <div class="approval-meta">
                        <div>
                            <div class="approval-meta-label">Jenis Cuti</div>
                            <div class="approval-meta-value">{{ $leaveRequest->leaveType->name }}</div>
                        </div>
                        <div>
                            <div class="approval-meta-label">Periode</div>
                            <div class="approval-meta-value">
                                {{ format_tanggal($leaveRequest->start_date) }} — {{ format_tanggal($leaveRequest->end_date) }}
                            </div>
                        </div>
                        <div>
                            <div class="approval-meta-label">Lama</div>
                            <div class="approval-meta-value">{{ $leaveRequest->total_days }} hari kerja</div>
                        </div>
                        <div>
                            <div class="approval-meta-label">Diajukan</div>
                            <div class="approval-meta-value">{{ format_tanggal_waktu($leaveRequest->created_at) }}</div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="approval-meta-label">Alasan</div>
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
                            <div class="fw-semibold small mb-2"><i class="bi bi-clock-history me-1"></i>Riwayat Persetujuan</div>
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
                        <button type="submit" name="action" value="approve" class="btn btn-sm btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Setujui
                        </button>
                        <button type="submit" name="action" value="reject" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-x-lg me-1"></i>Tolak
                        </button>
                    </form>
                </article>
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
