<x-app-layout>
    <x-slot name="title">Notifikasi</x-slot>

    <x-page-header title="Notifikasi" subtitle="Daftar pemberitahuan aktivitas pengajuan cuti Anda.">
        @if ($notifications->isNotEmpty())
            <form method="POST" action="{{ route('notifications.read-all') }}" class="js-loading-form">
                @csrf
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-check2-all me-1"></i>Tandai Semua Dibaca</button>
            </form>
        @endif
    </x-page-header>

    <div class="card">
        <div class="card-header">Daftar Notifikasi</div>
        <div class="list-group list-group-flush">
            @forelse ($notifications as $notification)
                <div class="list-group-item d-flex gap-3 {{ $notification->read_at === null ? 'bg-primary-subtle bg-opacity-10' : '' }}">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle flex-shrink-0 {{ $notification->read_at === null ? 'bg-primary text-white' : 'bg-light text-secondary' }}" style="width: 38px; height: 38px;">
                        <i class="bi {{ $notification->read_at === null ? 'bi-bell-fill' : 'bi-bell' }}"></i>
                    </span>
                    <div class="flex-grow-1">
                        <div class="d-flex flex-wrap justify-content-between gap-2">
                            <div class="fw-semibold">{{ $notification->data['title'] ?? 'Notifikasi' }}</div>
                            <div class="text-muted small">{{ $notification->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="text-muted small mt-1">{{ $notification->data['message'] ?? '' }}</div>
                    </div>
                </div>
            @empty
                <x-empty-state icon="bi-bell" message="Belum ada notifikasi." description="Notifikasi pengajuan cuti akan muncul di sini." />
            @endforelse
        </div>

        @if ($notifications->hasPages())
            <div class="card-body border-top">{{ $notifications->links('pagination::bootstrap-5') }}</div>
        @endif
    </div>
</x-app-layout>
