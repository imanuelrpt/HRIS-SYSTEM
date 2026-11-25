@props(['title', 'subtitle' => null])

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="hris-page-title">{{ $title }}</h1>
        @if ($subtitle)
            <p class="hris-page-subtitle">{{ $subtitle }}</p>
        @endif
    </div>
    {{ $slot }}
</div>
