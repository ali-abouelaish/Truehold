@props(['current' => 'list'])

<nav class="th-nav">
    <div class="th-nav-inner">
        <a href="{{ route('properties.index') }}" class="th-logo">
            <span class="th-logo-mark">
                <svg viewBox="0 0 32 32" width="26" height="26" fill="none">
                    <path d="M6 14L16 6l10 8v11a1 1 0 0 1-1 1h-6v-7h-6v7H7a1 1 0 0 1-1-1V14Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                </svg>
            </span>
            <span class="th-logo-wordmark">truehold</span>
        </a>
        <div class="th-nav-links">
            <a href="{{ route('properties.index') }}" class="th-nav-link {{ $current === 'list' ? 'is-active' : '' }}">Listings</a>
            <a href="{{ route('properties.map') }}" class="th-nav-link {{ $current === 'map' ? 'is-active' : '' }}">Map</a>
        </div>
        <div class="th-nav-cta">
            @auth
                <a href="{{ route('admin.dashboard') }}" class="th-btn th-btn-primary">Dashboard</a>
            @else
                <a href="{{ route('login', ['redirect' => url()->current()]) }}" class="th-btn th-btn-primary">Agent sign in</a>
            @endauth
        </div>
    </div>
</nav>
