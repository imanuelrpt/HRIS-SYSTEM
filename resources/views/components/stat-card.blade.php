@props([
    'icon' => 'bi-grid-1x2',
    'label',
    'value',
    'hint' => null,
    'variant' => 'primary',
])

<div class="card h-100">
    <div class="card-body stat-card stat-card--{{ $variant }}">
        <span class="stat-icon"><i class="bi {{ $icon }}"></i></span>
        <div class="stat-copy">
            <div class="stat-label">{{ $label }}</div>
            <div class="stat-value">{{ $value }}</div>
            @if ($hint)
                <div class="stat-hint">{{ $hint }}</div>
            @endif
        </div>
    </div>
</div>
