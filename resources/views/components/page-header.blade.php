@props(['title', 'subtitle' => null, 'eyebrow' => null])

<div class="hris-page-header">
    <div>
        @if ($eyebrow)
            <div class="hris-eyebrow">{{ $eyebrow }}</div>
        @endif
        <h1 class="hris-page-title">{{ $title }}</h1>
        @if ($subtitle)
            <p class="hris-page-subtitle">{{ $subtitle }}</p>
        @endif
    </div>
    {{ $slot }}
</div>
