<x-app-layout>
    <x-slot name="title">Jam Kerja</x-slot>

    @php
        $dayNames = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
        $selectedDays = array_map('intval', old('working_days', $workSetting->working_days));
    @endphp

    <x-page-header title="Jam Kerja" subtitle="Atur jam masuk, jam pulang, toleransi keterlambatan, dan hari kerja." />

    <div class="card" style="max-width: 860px;">
        <div class="card-body">
            <form method="POST" action="{{ route('work-settings.update') }}" class="js-loading-form">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="check_in" class="form-label">Jam Masuk <span class="text-danger">*</span></label>
                        <input type="time" id="check_in" name="check_in" value="{{ old('check_in', \Illuminate\Support\Carbon::parse($workSetting->check_in)->format('H:i')) }}" class="form-control @error('check_in') is-invalid @enderror" required>
                        @error('check_in')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="check_out" class="form-label">Jam Pulang <span class="text-danger">*</span></label>
                        <input type="time" id="check_out" name="check_out" value="{{ old('check_out', \Illuminate\Support\Carbon::parse($workSetting->check_out)->format('H:i')) }}" class="form-control @error('check_out') is-invalid @enderror" required>
                        @error('check_out')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="late_tolerance_minutes" class="form-label">Toleransi Terlambat (menit) <span class="text-danger">*</span></label>
                        <input type="number" id="late_tolerance_minutes" name="late_tolerance_minutes" value="{{ old('late_tolerance_minutes', $workSetting->late_tolerance_minutes) }}" class="form-control @error('late_tolerance_minutes') is-invalid @enderror" min="0" max="480" required>
                        @error('late_tolerance_minutes')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label class="form-label">Hari Kerja <span class="text-danger">*</span></label>
                    <div class="d-flex flex-wrap gap-3">
                        @foreach ($dayNames as $day => $label)
                            <div class="form-check">
                                <input type="checkbox" id="day_{{ $day }}" name="working_days[]" value="{{ $day }}" class="form-check-input" @checked(in_array($day, $selectedDays, true))>
                                <label for="day_{{ $day }}" class="form-check-label">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>
                    @error('working_days')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    @error('working_days.*')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Pengaturan</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
