@props(['label', 'value', 'icon', 'color' => 'navy', 'href' => null])
<div {{ $attributes->class('card h-100') }}>
    <div class="stat-card">
        <div class="stat-icon icon-{{ $color }}"><i class="bi bi-{{ $icon }}" aria-hidden="true"></i></div>
        <div class="flex-grow-1">
            <div class="stat-value">{{ $value }}</div>
            <div class="stat-label">
                @if ($href)
                    <a href="{{ $href }}" class="stretched-link text-reset text-decoration-none">{{ $label }}</a>
                @else
                    {{ $label }}
                @endif
            </div>
        </div>
    </div>
</div>
