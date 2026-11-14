@props(['route', 'active', 'icon'])
@php($isActive = request()->routeIs($active))
<li class="nav-item">
    <a href="{{ route($route) }}" @class(['nav-link', 'active' => $isActive]) @if ($isActive) aria-current="page" @endif>
        <i class="nav-icon bi bi-{{ $icon }}" aria-hidden="true"></i>
        <p>{{ $slot }}</p>
    </a>
</li>
