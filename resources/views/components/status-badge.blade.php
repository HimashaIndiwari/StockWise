{{-- Consistent status badge: always icon + text, never color alone. type: success|warning|danger|neutral|navy --}}
@props(['type' => 'neutral', 'icon' => null])
<span {{ $attributes->class(['badge-status', 'status-'.$type]) }}>
    @if ($icon)<i class="bi bi-{{ $icon }}" aria-hidden="true"></i>@endif
    {{ $slot }}
</span>
