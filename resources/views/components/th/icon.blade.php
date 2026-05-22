@props(['name', 'size' => 16])

@php
    $w = $attributes->get('width', $size);
    $h = $attributes->get('height', $size);
@endphp

@switch($name)
    @case('home')
        <svg viewBox="0 0 24 24" width="{{ $w }}" height="{{ $h }}" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6h-6v6H4a1 1 0 0 1-1-1V10.5Z"/></svg>
        @break
    @case('pin')
        <svg viewBox="0 0 24 24" width="{{ $w }}" height="{{ $h }}" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s7-6.5 7-12a7 7 0 1 0-14 0c0 5.5 7 12 7 12Z"/><circle cx="12" cy="9" r="2.5"/></svg>
        @break
    @case('bed')
        <svg viewBox="0 0 24 24" width="{{ $w }}" height="{{ $h }}" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 18v-7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v7M3 14h18M7 11V8.5A1.5 1.5 0 0 1 8.5 7h3A1.5 1.5 0 0 1 13 8.5V11"/></svg>
        @break
    @case('bath')
        <svg viewBox="0 0 24 24" width="{{ $w }}" height="{{ $h }}" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12h16v3a4 4 0 0 1-4 4H8a4 4 0 0 1-4-4v-3ZM6 12V6a2 2 0 0 1 2-2h1M6 19l-1 2M18 19l1 2"/></svg>
        @break
    @case('heart')
        <svg viewBox="0 0 24 24" width="{{ $w }}" height="{{ $h }}" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20s-7-4.5-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.5-7 10-7 10Z"/></svg>
        @break
    @case('heart-fill')
        <svg viewBox="0 0 24 24" width="{{ $w }}" height="{{ $h }}" fill="currentColor"><path d="M12 20s-7-4.5-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.5-7 10-7 10Z"/></svg>
        @break
    @case('search')
        <svg viewBox="0 0 24 24" width="{{ $w }}" height="{{ $h }}" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
        @break
    @case('filter')
        <svg viewBox="0 0 24 24" width="{{ $w }}" height="{{ $h }}" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 5h18M6 12h12M10 19h4"/></svg>
        @break
    @case('grid')
        <svg viewBox="0 0 24 24" width="{{ $w }}" height="{{ $h }}" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
        @break
    @case('list')
        <svg viewBox="0 0 24 24" width="{{ $w }}" height="{{ $h }}" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
        @break
    @case('map')
        <svg viewBox="0 0 24 24" width="{{ $w }}" height="{{ $h }}" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="m3 6 6-2 6 2 6-2v14l-6 2-6-2-6 2V6Z"/><path d="M9 4v16M15 6v16"/></svg>
        @break
    @case('chevron-left')
        <svg viewBox="0 0 24 24" width="{{ $w }}" height="{{ $h }}" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        @break
    @case('chevron-right')
        <svg viewBox="0 0 24 24" width="{{ $w }}" height="{{ $h }}" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        @break
    @case('chevron-down')
        <svg viewBox="0 0 24 24" width="{{ $w }}" height="{{ $h }}" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
        @break
    @case('close')
        <svg viewBox="0 0 24 24" width="{{ $w }}" height="{{ $h }}" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 6l12 12M18 6 6 18"/></svg>
        @break
    @case('check')
        <svg viewBox="0 0 24 24" width="{{ $w }}" height="{{ $h }}" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg>
        @break
    @case('share')
        <svg viewBox="0 0 24 24" width="{{ $w }}" height="{{ $h }}" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12v7a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-7M16 6l-4-4-4 4M12 2v13"/></svg>
        @break
    @case('calendar')
        <svg viewBox="0 0 24 24" width="{{ $w }}" height="{{ $h }}" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/></svg>
        @break
    @case('furnish')
        <svg viewBox="0 0 24 24" width="{{ $w }}" height="{{ $h }}" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 11V8a3 3 0 0 1 3-3h10a3 3 0 0 1 3 3v3M3 11h18v6H3zM6 17v3M18 17v3"/></svg>
        @break
    @case('walk')
        <svg viewBox="0 0 24 24" width="{{ $w }}" height="{{ $h }}" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="13" cy="4" r="1.5"/><path d="M9 21l2-6-2-3 3-4 4 3 3 1M9 12l-3 1"/></svg>
        @break
    @case('pound')
        <svg viewBox="0 0 24 24" width="{{ $w }}" height="{{ $h }}" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 7c-.5-2-2-3-4-3-3 0-4 2-4 4 0 5-1 6-2 7h10M8 13h6"/></svg>
        @break
    @case('sparkle')
        <svg viewBox="0 0 24 24" width="{{ $w }}" height="{{ $h }}" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v4M12 17v4M3 12h4M17 12h4M5.5 5.5l2.8 2.8M15.7 15.7l2.8 2.8M5.5 18.5l2.8-2.8M15.7 8.3l2.8-2.8"/></svg>
        @break
@endswitch
