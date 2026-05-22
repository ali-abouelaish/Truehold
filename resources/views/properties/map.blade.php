<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Explore {{ $properties->count() }} TrueHold listings on the map.">
    <meta name="theme-color" content="#FAF7F0">
    <title>Map view — TrueHold</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/jpeg" href="{{ asset('images/truehold-logo.jpg') }}">

    <x-th.fonts/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html, body { height: 100%; margin: 0; }
        /* Strip Google Maps' default Roboto override on info-window content so design fonts apply */
        .gm-style .gm-style-iw-c {
            padding: 0 !important;
            border-radius: var(--th-radius) !important;
            box-shadow: var(--th-shadow-lg) !important;
            max-width: 320px !important;
        }
        .gm-style .gm-style-iw-d { overflow: visible !important; padding: 0 !important; }
        .gm-style .gm-style-iw-c button.gm-ui-hover-effect {
            top: 6px !important; right: 6px !important;
            opacity: .85; background: white !important; border-radius: 50% !important;
        }
        .gm-style .gm-style-iw-tc::after { background: var(--th-surface) !important; }
        .th-mappin-wrap { transform: translate(-50%, -100%); }
    </style>
</head>
<body>

@php
    $req = request();
    $selectedArea = (string) $req->input('location', '');
    $hasMapsKey = !empty(config('services.google.maps_api_key'));
@endphp

<div class="th-page th-page-map">

    <x-th.nav current="map"/>

    <div class="th-map-bar">
        <div class="th-map-bar-inner">
            <div class="th-map-title">
                <h1 class="th-map-eyebrow">Map view</h1>
                <span class="th-map-count">
                    <strong>{{ $properties->count() }}</strong> properties · drag to explore
                </span>
            </div>
            <div class="th-map-bar-controls">
                <button type="button" class="th-chip" onclick="thFilterOpen()">
                    <x-th.icon name="filter" size="12"/> Refine
                </button>
                <form method="GET" action="{{ route('properties.map') }}" style="display:inline-flex; align-items:center; gap:8px;">
                    @foreach($req->except(['location', 'page']) as $k => $v)
                        @if(is_array($v))
                            @foreach($v as $vv)
                                <input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                        @endif
                    @endforeach
                    <select name="location" class="th-map-select" onchange="this.form.submit()">
                        <option value="">All areas</option>
                        @foreach($locations as $loc)
                            @php $locStr = (string) $loc; @endphp
                            @if($locStr !== '')
                                <option value="{{ $locStr }}" @selected($selectedArea === $locStr)>{{ $locStr }}</option>
                            @endif
                        @endforeach
                    </select>
                </form>
                <a class="th-link-soft" href="{{ route('properties.index', $req->except(['page'])) }}">
                    <x-th.icon name="grid" size="14"/> List view
                </a>
            </div>
        </div>
    </div>

    <div class="th-mapsplit">

        <aside class="th-mapside" id="th-mapside">
            @forelse($propertiesForJson as $p)
                @php
                    $photos = $p['high_quality_photos_array'] ?? [];
                    $img = $photos[0] ?? ($p['first_photo_url'] ?? null);
                    $loc = (string) ($p['location'] ?? '');
                    $area = $loc !== '' ? trim(explode(',', $loc)[0]) : 'London';
                    $price = $p['price'] ?? null;
                    $priceLabel = is_numeric($price) ? '£' . number_format((float)$price, 0) : ($price ?: '—');
                    $billsRaw = strtolower(trim((string) ($p['bills_included'] ?? '')));
                    $billsIncluded = in_array($billsRaw, ['yes','true','1','included'], true);
                @endphp
                <a href="{{ route('properties.show', $p['id']) }}"
                   class="th-mapcard"
                   data-id="{{ $p['id'] }}"
                   data-lat="{{ $p['latitude'] }}"
                   data-lng="{{ $p['longitude'] }}"
                   onmouseenter="thMapHover('{{ $p['id'] }}', true)"
                   onmouseleave="thMapHover('{{ $p['id'] }}', false)"
                   onclick="thMapSelect('{{ $p['id'] }}', event)">
                    <div class="th-mapcard-img"
                         @if($img) style="background-image: url('{{ e($img) }}');" @endif></div>
                    <div class="th-mapcard-body">
                        <div class="th-area">{{ $area }}</div>
                        <div class="th-mapcard-title">{{ $p['title'] ?? 'Untitled' }}</div>
                        <div class="th-mapcard-foot">
                            <span class="th-mapcard-price">{{ $priceLabel }}<span class="th-price-period">&nbsp;pcm</span></span>
                            @if($billsIncluded)
                                <span class="th-fact th-fact-accent">Bills incl.</span>
                            @endif
                        </div>
                        @if(!empty($p['property_type']))
                            <div class="th-mapcard-mini">{{ $p['property_type'] }}</div>
                        @endif
                    </div>
                </a>
            @empty
                <div style="padding: 32px 16px; text-align: center; color: var(--th-ink-soft);">
                    No properties match the current filters.
                    <div style="margin-top: 16px;">
                        <a href="{{ route('properties.map') }}" class="th-btn th-btn-primary">Reset filters</a>
                    </div>
                </div>
            @endforelse
        </aside>

        <div class="th-mapcanvas">
            @if($hasMapsKey)
                <div id="map"></div>
            @else
                <div style="display:flex; align-items:center; justify-content:center; height:100%; padding:32px; text-align:center; color:var(--th-ink-soft);">
                    Map unavailable — set <code>services.google.maps_api_key</code> in your config to enable Google Maps.
                </div>
            @endif
        </div>

    </div>

    <x-th.filter-panel :locations="$locations" :propertyTypes="$propertyTypes ?? collect()"/>

</div>

<script id="th-properties-data" type="application/json">
    @json($propertiesForJson)
</script>

@if($hasMapsKey)
<script>
    // -- Light/desaturated style matching the design canvas --
    const THMAP_STYLE = [
        { elementType: 'geometry', stylers: [{ color: '#EFEAE0' }] },
        { elementType: 'labels.text.fill', stylers: [{ color: '#6B6F77' }] },
        { elementType: 'labels.text.stroke', stylers: [{ color: '#EFEAE0' }] },
        { featureType: 'administrative', elementType: 'geometry.stroke', stylers: [{ color: '#d9d3c4' }] },
        { featureType: 'landscape.natural', elementType: 'geometry', stylers: [{ color: '#EAE5D7' }] },
        { featureType: 'poi', stylers: [{ visibility: 'simplified' }] },
        { featureType: 'poi.park', elementType: 'geometry', stylers: [{ color: '#CFDDB8' }] },
        { featureType: 'poi.park', elementType: 'labels.text.fill', stylers: [{ color: '#647864' }] },
        { featureType: 'road', elementType: 'geometry', stylers: [{ color: '#ffffff' }] },
        { featureType: 'road', elementType: 'geometry.stroke', stylers: [{ visibility: 'off' }] },
        { featureType: 'road.highway', elementType: 'geometry', stylers: [{ color: '#ffffff' }] },
        { featureType: 'transit', stylers: [{ visibility: 'off' }] },
        { featureType: 'water', elementType: 'geometry', stylers: [{ color: '#B8CCD9' }] },
        { featureType: 'water', elementType: 'labels.text.fill', stylers: [{ color: '#6E8294' }] },
    ];

    let thMap, thInfoWindow;
    const thOverlays = new Map();   // id -> OverlayView instance
    let thSelectedId = null, thHoverId = null;
    let thProperties = [];

    function initMap() {
        const data = document.getElementById('th-properties-data');
        thProperties = JSON.parse(data.textContent || '[]').filter(p => {
            const lat = parseFloat(p.latitude), lng = parseFloat(p.longitude);
            return !isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180;
        });

        thMap = new google.maps.Map(document.getElementById('map'), {
            center: { lat: 51.5074, lng: -0.1278 },
            zoom: 12,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            mapTypeControl: false,
            fullscreenControl: false,
            streetViewControl: false,
            zoomControl: true,
            zoomControlOptions: { position: google.maps.ControlPosition.RIGHT_TOP },
            styles: THMAP_STYLE,
            backgroundColor: '#EFEAE0',
            clickableIcons: false,
        });

        thInfoWindow = new google.maps.InfoWindow({ disableAutoPan: false });
        thMap.addListener('click', () => { thInfoWindow.close(); thMapSelect(null); });

        // Build a custom HTML overlay class once google.maps is available
        class PricePin extends google.maps.OverlayView {
            constructor(property) {
                super();
                this.property = property;
                this.position = new google.maps.LatLng(+property.latitude, +property.longitude);
                this.div = null;
            }
            onAdd() {
                const d = document.createElement('div');
                d.className = 'th-mappin th-mappin-wrap';
                d.style.position = 'absolute';
                d.textContent = thFormatPricePin(this.property.price);
                d.addEventListener('click', (e) => { e.stopPropagation(); thMapSelect(this.property.id); });
                d.addEventListener('mouseenter', () => thMapHover(this.property.id, true));
                d.addEventListener('mouseleave', () => thMapHover(this.property.id, false));
                this.div = d;
                this.getPanes().floatPane.appendChild(d);
            }
            draw() {
                const proj = this.getProjection();
                if (!proj || !this.div) return;
                const pt = proj.fromLatLngToDivPixel(this.position);
                if (!pt) return;
                this.div.style.left = pt.x + 'px';
                this.div.style.top  = pt.y + 'px';
            }
            onRemove() {
                if (this.div && this.div.parentNode) this.div.parentNode.removeChild(this.div);
                this.div = null;
            }
            setState({ hover, selected }) {
                if (!this.div) return;
                this.div.classList.toggle('is-hover', !!hover);
                this.div.classList.toggle('is-selected', !!selected);
            }
        }

        // Drop a pin per property + fit bounds
        const bounds = new google.maps.LatLngBounds();
        thProperties.forEach(p => {
            const pin = new PricePin(p);
            pin.setMap(thMap);
            thOverlays.set(String(p.id), pin);
            bounds.extend(pin.position);
        });
        if (thProperties.length > 0) {
            thMap.fitBounds(bounds, { top: 60, right: 60, bottom: 60, left: 60 });
            if (thProperties.length === 1) thMap.setZoom(15);
        }
    }

    function thFormatPricePin(price) {
        const n = parseFloat(price);
        if (isNaN(n) || n <= 0) return '£?';
        if (n >= 1000) {
            const k = n / 1000;
            return '£' + (k % 1 === 0 ? k.toFixed(0) : k.toFixed(1)) + 'k';
        }
        return '£' + Math.round(n);
    }

    function thMapHover(id, on) {
        const old = thHoverId;
        thHoverId = on ? id : null;
        [old, id].forEach(x => {
            if (!x) return;
            const card = document.querySelector(`.th-mapcard[data-id="${x}"]`);
            if (card) card.classList.toggle('is-hover', thHoverId === x);
            const pin = thOverlays.get(String(x));
            if (pin) pin.setState({ hover: thHoverId === x, selected: thSelectedId === x });
        });
    }

    function thMapSelect(id, event) {
        if (event) event.preventDefault();
        const old = thSelectedId;
        thSelectedId = id;

        // Update pin/card visual state
        [old, id].forEach(x => {
            if (!x) return;
            const card = document.querySelector(`.th-mapcard[data-id="${x}"]`);
            if (card) card.classList.toggle('is-selected', thSelectedId === x);
            const pin = thOverlays.get(String(x));
            if (pin) pin.setState({ hover: thHoverId === x, selected: thSelectedId === x });
        });

        if (!id) { thInfoWindow.close(); return; }
        const p = thProperties.find(x => String(x.id) === String(id));
        if (!p) return;

        // Pan to it (and scroll the card into view)
        const ll = new google.maps.LatLng(+p.latitude, +p.longitude);
        thMap.panTo(ll);

        const card = document.querySelector(`.th-mapcard[data-id="${id}"]`);
        if (card) card.scrollIntoView({ block: 'nearest', behavior: 'smooth' });

        // Popup
        const photos = p.high_quality_photos_array || [];
        const img = photos[0] || p.first_photo_url || '';
        const loc = (p.location || '').split(',')[0].trim() || 'London';
        const priceLabel = (() => {
            const n = parseFloat(p.price);
            return isNaN(n) ? (p.price || '—') : '£' + n.toLocaleString();
        })();
        const billsRaw = String(p.bills_included || '').toLowerCase().trim();
        const billsIncluded = ['yes','true','1','included'].includes(billsRaw);
        const detailUrl = `{{ url('/properties') }}/${encodeURIComponent(p.id)}`;

        const html = `
            <div class="th-pinpop-inner">
                <div class="th-pinpop-img" style="background-image:url('${img.replace(/'/g, '%27')}');"></div>
                <div class="th-pinpop-body">
                    <div class="th-area" style="font-size:11px;">${loc}</div>
                    <div class="th-pinpop-title">${thEscape(p.title || 'Untitled')}</div>
                    <div class="th-pinpop-row">
                        <span class="th-mapcard-price" style="font-size:22px;">${priceLabel}<span class="th-price-period">&nbsp;pcm</span></span>
                        ${billsIncluded ? '<span class="th-fact th-fact-accent" style="margin-left:auto;">Bills incl.</span>' : ''}
                    </div>
                    <a href="${detailUrl}" class="th-btn th-btn-primary th-btn-block th-btn-sm">See full details</a>
                </div>
            </div>`;

        thInfoWindow.setContent(html);
        thInfoWindow.setPosition(ll);
        thInfoWindow.open(thMap);
    }

    function thEscape(s) {
        return String(s).replace(/[&<>"']/g, c => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[c]));
    }
</script>
<script async defer
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&callback=initMap&v=weekly&loading=async"></script>
@endif

@stack('scripts')

</body>
</html>
