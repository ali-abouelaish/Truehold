<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Find your next room or flat in London. {{ $properties->total() }} verified listings on TrueHold.">
    <meta name="theme-color" content="#FAF7F0">
    <title>TrueHold — Rooms & flats in London</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/jpeg" href="{{ asset('images/truehold-logo.jpg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/truehold-logo.jpg') }}">

    <x-th.fonts/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

@php
    $req = request();
    $layout = $req->input('layout') === 'list' ? 'list' : 'grid';
    $ensuiteOn = $req->input('ensuite') === 'yes';
    $couplesOn = $req->input('couples_allowed') === 'yes';
    $payingOn  = $req->boolean('paying_only');

    $toggle = fn($key, $value, $on) => $req->fullUrlWithQuery([$key => $on ? null : $value, 'page' => null]);
    $setLayout = fn($v) => $req->fullUrlWithQuery(['layout' => $v]);
@endphp

<div class="th-page">

    <x-th.nav current="list"/>

    <header class="th-hero is-compact">
        <div class="th-hero-inner">
            <div class="th-hero-eyebrow">
                <span class="th-hero-dot"></span>
                {{ $properties->total() }} rooms &amp; flats live in London
            </div>
            <h1 class="th-hero-title">
                A calmer way to find <em>your next room</em> in London.
            </h1>

            <form method="GET" action="{{ route('properties.index') }}" class="th-search-bar">
                <div class="th-search-field">
                    <label for="th-where">Where</label>
                    <select name="location" id="th-where" onchange="this.form.submit()">
                        <option value="">All areas</option>
                        @foreach($locations as $loc)
                            @php $locStr = (string) $loc; @endphp
                            @if($locStr !== '')
                                <option value="{{ $locStr }}" @selected($req->input('location') === $locStr)>{{ $locStr }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="th-search-field">
                    <label for="th-type">Type</label>
                    <select name="property_type" id="th-type" onchange="this.form.submit()">
                        <option value="">Any type</option>
                        @foreach($propertyTypes as $t)
                            @php $tStr = (string) $t; @endphp
                            @if($tStr !== '')
                                <option value="{{ $tStr }}" @selected($req->input('property_type') === $tStr)>{{ $tStr }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="th-search-field">
                    <label for="th-budget">Budget</label>
                    <select name="max_price" id="th-budget" onchange="this.form.submit()">
                        <option value="">Any</option>
                        <option value="800"  @selected($req->input('max_price') === '800')>Up to £800</option>
                        <option value="1200" @selected($req->input('max_price') === '1200')>Up to £1,200</option>
                        <option value="1600" @selected($req->input('max_price') === '1600')>Up to £1,600</option>
                        <option value="2500" @selected($req->input('max_price') === '2500')>Up to £2,500</option>
                    </select>
                </div>
                <button type="button" class="th-search-go" onclick="thFilterOpen()">
                    <x-th.icon name="filter" size="16"/> More filters
                </button>
                <button type="submit" class="th-search-primary">
                    <x-th.icon name="search" size="16"/> Search
                </button>
            </form>
        </div>
    </header>

    <div class="th-controls">
        <div class="th-controls-inner">
            <div class="th-chips">
                <a href="{{ $toggle('ensuite', 'yes', $ensuiteOn) }}" class="th-chip {{ $ensuiteOn ? 'is-on' : '' }}">
                    @if($ensuiteOn) <x-th.icon name="check" size="12"/> @endif Ensuite
                </a>
                <a href="{{ $toggle('couples_allowed', 'yes', $couplesOn) }}" class="th-chip {{ $couplesOn ? 'is-on' : '' }}">
                    @if($couplesOn) <x-th.icon name="check" size="12"/> @endif Couples accepted
                </a>
                @auth
                    <a href="{{ $toggle('paying_only', '1', $payingOn) }}" class="th-chip {{ $payingOn ? 'is-on' : '' }}">
                        @if($payingOn) <x-th.icon name="check" size="12"/> @endif Paying agents
                    </a>
                @endauth
                <button type="button" class="th-chip" onclick="thFilterOpen()">
                    <x-th.icon name="filter" size="12"/> All filters
                </button>
            </div>

            <div class="th-controls-right">
                <a class="th-link-soft" href="{{ route('properties.map', $req->except(['layout', 'page'])) }}">
                    <x-th.icon name="map" size="14"/> Map view
                </a>
                <div class="th-toggle">
                    <a href="{{ $setLayout('grid') }}" class="{{ $layout === 'grid' ? 'is-on' : '' }}" aria-label="Grid view">
                        <x-th.icon name="grid" size="14"/>
                    </a>
                    <a href="{{ $setLayout('list') }}" class="{{ $layout === 'list' ? 'is-on' : '' }}" aria-label="List view">
                        <x-th.icon name="list" size="14"/>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <main class="th-results {{ $layout === 'list' ? 'th-grid-list' : 'th-grid-4col' }}">
        @forelse($properties as $property)
            <x-th.property-card :property="$property" :layout="$layout" :dense="$layout === 'grid'"/>
        @empty
            <div style="grid-column: 1 / -1; padding: 64px 16px; text-align: center; color: var(--th-ink-soft);">
                <h2 class="th-section-h" style="margin-bottom: 8px;">No listings match those filters.</h2>
                <p>Try widening your search — fewer areas, a higher budget, or remove some &ldquo;must haves&rdquo;.</p>
                <p style="margin-top: 16px;">
                    <a href="{{ route('properties.index') }}" class="th-btn th-btn-primary">Reset all filters</a>
                </p>
            </div>
        @endforelse
    </main>

    @if($properties->hasPages())
        <nav class="th-pagination" aria-label="Pagination">
            @if($properties->onFirstPage())
                <span class="th-page-disabled">‹ Prev</span>
            @else
                <a href="{{ $properties->previousPageUrl() }}" rel="prev">‹ Prev</a>
            @endif

            @foreach($properties->getUrlRange(max(1, $properties->currentPage() - 2), min($properties->lastPage(), $properties->currentPage() + 2)) as $page => $url)
                @if($page == $properties->currentPage())
                    <span class="th-page-current">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach

            @if($properties->hasMorePages())
                <a href="{{ $properties->nextPageUrl() }}" rel="next">Next ›</a>
            @else
                <span class="th-page-disabled">Next ›</span>
            @endif
        </nav>
    @endif

    <x-th.footer/>

</div>

<x-th.filter-panel :locations="$locations" :propertyTypes="$propertyTypes"/>

@stack('scripts')

</body>
</html>
