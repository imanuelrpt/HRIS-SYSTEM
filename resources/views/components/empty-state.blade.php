@props(['icon' => 'bi-inbox', 'message' => 'Belum ada data.', 'description' => null])

<div {{ $attributes->merge(['class' => 'empty-state']) }}>
    <i class="bi {{ $icon }} d-block mb-3"></i>
    <div class="fw-semibold">{{ $message }}</div>
    @if ($description)
        <div class="text-muted small mt-1">{{ $description }}</div>
    @endif
</div>
