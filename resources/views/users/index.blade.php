<x-app-layout>
    <x-slot name="title">Karyawan</x-slot>

    <x-page-header title="Karyawan" subtitle="Kelola akun, peran, dan data kepegawaian karyawan.">
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i>Tambah Karyawan
        </a>
    </x-page-header>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="section-title mb-0"><i class="bi bi-people"></i>Daftar Karyawan</span>
            <form method="GET" action="{{ route('users.index') }}" class="hris-filterbar">
                <div class="filter-field" style="min-width: 230px;">
                    <label for="search">Pencarian</label>
                    <input type="search" name="search" id="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="Cari nama, email, atau NIK">
                </div>
                <div class="filter-field">
                    <label for="role">Peran</label>
                    <select name="role" id="role" class="form-select form-select-sm">
                        <option value="">Semua peran</option>
                        @foreach ($roles as $value => $label)
                            <option value="{{ $value }}" @selected($roleFilter === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-field">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-select form-select-sm">
                        <option value="">Semua status</option>
                        <option value="active" @selected($statusFilter === 'active')>Aktif</option>
                        <option value="inactive" @selected($statusFilter === 'inactive')>Nonaktif</option>
                    </select>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-search me-1"></i>Cari
                    </button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Karyawan</th>
                        <th>NIK</th>
                        <th>Peran</th>
                        <th>Departemen</th>
                        <th>Jabatan</th>
                        <th>Atasan</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $employee)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if ($employee->photo_path)
                                        <img src="{{ Storage::url($employee->photo_path) }}" alt="Foto {{ $employee->name }}" class="hris-avatar" style="width: 36px; height: 36px;">
                                    @else
                                        <span class="hris-avatar" style="width: 36px; height: 36px;">
                                            {{ mb_substr($employee->name, 0, 1) }}
                                        </span>
                                    @endif
                                    <div>
                                        <div class="fw-semibold">{{ $employee->name }}</div>
                                        <div class="text-muted small">{{ $employee->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $employee->nik ?? '-' }}</td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                    {{ $employee->role->label() }}
                                </span>
                            </td>
                            <td>{{ $employee->department?->name ?? '-' }}</td>
                            <td>{{ $employee->position?->name ?? '-' }}</td>
                            <td>{{ $employee->manager?->name ?? '-' }}</td>
                            <td>
                                @if ($employee->is_active)
                                    <span class="badge bg-success-subtle text-success-emphasis">Aktif</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('users.edit', $employee) }}" class="btn btn-sm btn-outline-primary" title="Ubah"><i class="bi bi-pencil-square"></i></a>
                                @if ($employee->id !== auth()->id())
                                    <form method="POST" action="{{ route('users.destroy', $employee) }}" class="d-inline js-delete-form" data-confirm-text="Karyawan {{ $employee->name }} akan dinonaktifkan.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-secondary" title="Nonaktifkan"><i class="bi bi-trash3"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <x-empty-state icon="bi-people" message="Belum ada karyawan." description="Tambahkan akun karyawan untuk mulai menggunakan HRIS Mini." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="card-body border-top">{{ $users->links('pagination::bootstrap-5') }}</div>
        @endif
    </div>
</x-app-layout>
