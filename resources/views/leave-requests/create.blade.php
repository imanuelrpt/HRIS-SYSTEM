<x-app-layout>
    <x-slot name="title">Ajukan Cuti</x-slot>

    <x-page-header
        title="Ajukan Cuti"
        subtitle="Isi detail pengajuan cuti Anda. Persetujuan atasan dan HR diperlukan sebelum cuti disetujui."
    />

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <span class="section-title mb-0"><i class="bi bi-calendar2-plus"></i>Detail Pengajuan</span>
                </div>
                <form method="POST" action="{{ route('leave-requests.store') }}" enctype="multipart/form-data" class="js-loading-form">
                    @csrf

                    <div class="card-body">
                        <div class="mb-3">
                            <label for="leave_type_id" class="form-label">Jenis Cuti <span class="text-danger">*</span></label>
                            <select
                                name="leave_type_id"
                                id="leave_type_id"
                                class="form-select @error('leave_type_id') is-invalid @enderror"
                                required
                            >
                                <option value="">Pilih jenis cuti</option>
                                @foreach ($leaveTypes as $leaveType)
                                    <option
                                        value="{{ $leaveType->id }}"
                                        data-deducts-balance="{{ $leaveType->deducts_balance ? 1 : 0 }}"
                                        data-requires-attachment="{{ $leaveType->requires_attachment ? 1 : 0 }}"
                                        @selected(old('leave_type_id') == $leaveType->id)
                                    >{{ $leaveType->name }}</option>
                                @endforeach
                            </select>
                            @error('leave_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info py-2 d-flex align-items-center gap-2" id="leaveTypeInfo">
                            <i class="bi bi-info-circle-fill"></i><span>Pilih jenis cuti untuk melihat informasi kuota.</span>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input
                                    type="date"
                                    name="start_date"
                                    id="start_date"
                                    value="{{ old('start_date') }}"
                                    min="{{ today()->toDateString() }}"
                                    class="form-control @error('start_date') is-invalid @enderror"
                                    required
                                >
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                                <input
                                    type="date"
                                    name="end_date"
                                    id="end_date"
                                    value="{{ old('end_date') }}"
                                    min="{{ old('start_date', today()->toDateString()) }}"
                                    class="form-control @error('end_date') is-invalid @enderror"
                                    required
                                >
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="reason" class="form-label">Alasan <span class="text-danger">*</span></label>
                            <textarea
                                name="reason"
                                id="reason"
                                rows="4"
                                maxlength="1000"
                                class="form-control @error('reason') is-invalid @enderror"
                                placeholder="Jelaskan alasan pengajuan cuti Anda"
                                required
                            >{{ old('reason') }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-0">
                            <label for="attachment" class="form-label">Lampiran</label>
                            <input
                                type="file"
                                name="attachment"
                                id="attachment"
                                accept=".pdf,.jpg,.jpeg,.png"
                                class="form-control @error('attachment') is-invalid @enderror"
                            >
                            <div class="form-text" id="attachmentNote">Lampiran wajib diunggah untuk jenis cuti ini.</div>
                            @error('attachment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <a href="{{ route('leave-requests.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-1"></i>Kirim Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <span class="section-title mb-0"><i class="bi bi-pie-chart"></i>Ringkasan Kuota Tahun {{ now()->year }}</span>
                </div>
                <div class="list-group list-group-flush">
                    @foreach ($leaveTypes as $leaveType)
                        @php
                            $balance = $balances->get($leaveType->id);
                        @endphp
                        <div class="list-group-item d-flex justify-content-between align-items-center gap-2">
                            <span>{{ $leaveType->name }}</span>
                            <span class="badge bg-light text-dark border">
                                {{ $leaveType->deducts_balance ? ($balance?->remaining() ?? 0).' hari' : 'Tanpa potong kuota' }}
                            </span>
                        </div>
                    @endforeach
                </div>
                <div class="card-body small text-muted mb-0">
                    Hari kerja dihitung dari hari Senin sampai Jumat dan tidak termasuk tanggal libur.
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const leaveTypeSelect = document.getElementById('leave_type_id');
            const leaveTypeInfo = document.getElementById('leaveTypeInfo');
            const attachmentInput = document.getElementById('attachment');
            const attachmentNote = document.getElementById('attachmentNote');
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const remainingBalances = @json($balances->mapWithKeys(fn ($balance, $id) => [$id => $balance->remaining()]));

            function syncLeaveTypeInfo() {
                const selected = leaveTypeSelect.options[leaveTypeSelect.selectedIndex];

                if (!selected || !selected.value) {
                    leaveTypeInfo.textContent = 'Pilih jenis cuti untuk melihat informasi kuota.';
                    attachmentInput.required = false;
                    attachmentNote.classList.add('d-none');
                    return;
                }

                const deductsBalance = selected.dataset.deductsBalance === '1';
                const requiresAttachment = selected.dataset.requiresAttachment === '1';
                const remaining = remainingBalances[selected.value] ?? 0;

                leaveTypeInfo.innerHTML = deductsBalance
                    ? `Sisa kuota: <strong>${remaining} hari</strong>. Lama cuti dihitung dalam hari kerja.`
                    : 'Jenis cuti ini tidak memotong kuota cuti tahunan.';

                attachmentInput.required = requiresAttachment;
                attachmentNote.classList.toggle('d-none', !requiresAttachment);
            }

            leaveTypeSelect.addEventListener('change', syncLeaveTypeInfo);
            startDateInput.addEventListener('change', function () {
                endDateInput.min = this.value || '{{ today()->toDateString() }}';
            });
            syncLeaveTypeInfo();
        </script>
    @endpush
</x-app-layout>
