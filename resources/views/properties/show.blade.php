<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $property->title }} — {{ $property->location }}. TrueHold.">
    <meta name="theme-color" content="#FAF7F0">
    <title>{{ $property->title }} — TrueHold</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/jpeg" href="{{ asset('images/truehold-logo.jpg') }}">

    <x-th.fonts/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

@php
    $p = $property;

    // Photos — call the accessor directly. Don't use `??` here because
    // PropertyFromSheet::__isset() only reports raw attributes, so `?? []`
    // would short-circuit to [] before __get even runs.
    $gallery = $p->high_quality_photos_array;
    if (!is_array($gallery)) {
        $gallery = [];
    }
    if (empty($gallery) && !empty($p->first_photo_url)) {
        $gallery = [$p->first_photo_url];
    }
    $totalPhotos = (int) ($p->photo_count ?? count($gallery));
    if ($totalPhotos < count($gallery)) {
        $totalPhotos = count($gallery);
    }

    // Location helpers
    $location = (string) ($p->location ?? '');
    $area = $location !== '' ? trim(explode(',', $location)[0]) : 'London';

    // Booleans derived from string fields
    $billsRaw = strtolower(trim((string) ($p->bills_included ?? '')));
    $billsIncluded = in_array($billsRaw, ['yes', 'true', '1', 'included'], true);

    $furnishingsRaw = (string) ($p->furnishings ?? '');
    $furnishingsLower = strtolower(trim($furnishingsRaw));
    $isFurnished = $furnishingsRaw !== '' &&
                   !in_array($furnishingsLower, ['no', 'unfurnished', 'none', 'n/a', 'na'], true);
    $furnishingsLabel = $furnishingsRaw !== '' ? $furnishingsRaw : ($isFurnished ? 'Furnished' : 'Unfurnished');

    // Single canonical availability pill — fixes the "Available Available now" duplication
    $availableRaw = trim((string) ($p->available_date ?? ''));
    if ($availableRaw === '' || strtolower($availableRaw) === 'now') {
        $availablePill = 'Available now';
    } elseif (stripos($availableRaw, 'available') === 0) {
        $availablePill = $availableRaw;
    } else {
        $availablePill = 'Available ' . $availableRaw;
    }

    // Bathroom label
    $type = (string) ($p->property_type ?? '');
    $isEnsuite = stripos($type, 'ensuite') !== false || stripos($type, 'en-suite') !== false;
    $isStudio  = stripos($type, 'studio') !== false;
    $bathroomLabel = $isEnsuite ? 'Private ensuite' : ($isStudio ? 'Private' : 'Shared bathroom');

    // Rooms — a single post can advertise several rooms (room1..room4),
    // each with its own type / price / deposit.
    $fmtMoney = fn($v) => (is_numeric($v) && (float) $v > 0) ? '£' . number_format((float) $v, 0) : null;
    $rooms = [];
    for ($i = 1; $i <= 4; $i++) {
        $rType    = trim((string) ($p->{"room{$i}_type"} ?? ''));
        $rPrice   = $p->{"room{$i}_price_pcm"} ?? null;
        $rDeposit = $p->{"room{$i}_deposit"} ?? null;
        $rPriceFmt = $fmtMoney($rPrice);
        if ($rType !== '' || $rPriceFmt !== null) {
            $rooms[] = [
                'type'      => $rType !== '' ? ucfirst(strtolower($rType)) : 'Room',
                'price'     => $rPriceFmt,
                'price_raw' => (is_numeric($rPrice) && (float) $rPrice > 0) ? (float) $rPrice : null,
                'deposit'   => $fmtMoney($rDeposit),
            ];
        }
    }
    $roomsAvailable = count($rooms);
    $isMultiRoom = $roomsAvailable > 1;

    // Bedroom label
    $bedroomLabel = $isStudio
        ? 'Studio'
        : ($isMultiRoom ? $roomsAvailable . ' rooms available' : 'Room available');

    // Price
    $price = $p->price ?? null;
    $priceFormatted = is_numeric($price) ? '£' . number_format((float) $price, 0) : (string) ($p->formatted_price ?? '£' . $price);
    $deposit = $p->deposit ?? null;
    $depositFormatted = is_numeric($deposit) ? '£' . number_format((float) $deposit, 0) : ($deposit ? (string) $deposit : null);

    // Price display — for multi-room posts show a per-room range (from the room
    // prices, falling back to the min/max_room_price_pcm summary columns).
    $roomPriceVals = array_values(array_filter(array_column($rooms, 'price_raw')));
    $minRoom = $roomPriceVals ? min($roomPriceVals) : ($p->min_room_price_pcm ?? null);
    $maxRoom = $roomPriceVals ? max($roomPriceVals) : ($p->max_room_price_pcm ?? null);
    $hasRoomRange = $isMultiRoom
        && is_numeric($minRoom) && is_numeric($maxRoom)
        && (float) $minRoom > 0 && (float) $maxRoom > 0
        && (float) $minRoom != (float) $maxRoom;
    $priceDisplay = $hasRoomRange
        ? '£' . number_format((float) $minRoom, 0) . '–£' . number_format((float) $maxRoom, 0)
        : $priceFormatted;
    $pricePeriod = $hasRoomRange ? 'per room / month' : 'per month';

    // Agent
    $agentName = (string) ($p->agent_name ?? '');
    $agentInitials = collect(explode(' ', $agentName))
        ->take(2)->map(fn($w) => mb_substr($w, 0, 1))->implode('');
    $agentInitials = $agentInitials !== '' ? strtoupper($agentInitials) : 'TH';

    $payingRaw = $p->paying ?? null;
    $isPayingAgent = $payingRaw !== null && in_array(strtolower(trim((string) $payingRaw)), ['yes', 'true', '1'], true);

    // Login-gated deep link into the landlord's profile (Harbor Ops)
    $landlordUrl = trim((string) ($p->landlord_profile_url ?? ''));
    $hasLandlord = $landlordUrl !== '' && strtolower($landlordUrl) !== 'n/a';

    // Description
    $description = trim((string) ($p->description ?? ''));

    // Spec table fields (real model fields, replaces the 20+ green-checkmark lists)
    $specCosts = [
        'Rent'           => $priceFormatted . ' / month',
        'Deposit'        => $depositFormatted ?: '—',
        'Bills included' => $billsIncluded ? 'Yes' : 'No',
        'Min term'       => $p->min_term ?? '—',
        'Max term'       => $p->max_term ?? '—',
    ];
    $specProperty = [
        'Type'      => $type ?: '—',
        'Furnishing'=> $furnishingsLabel ?: '—',
        'Parking'   => $p->parking ?? '—',
        'Garden'    => $p->garden ?? '—',
        'Broadband' => $p->broadband ?? '—',
    ];
    $specHouse = [
        'Total rooms'    => $p->total_rooms ?? '—',
        'Housemates'     => $p->housemates ?? '—',
        'Couples accepted' => $p->couples_ok ?? '—',
        'Smoker ok'      => $p->smoking_ok ?? '—',
        'Pets ok'        => $p->pets_ok ?? '—',
    ];

    $cleanSpec = function($items) {
        $out = [];
        foreach ($items as $k => $v) {
            $s = trim((string) $v);
            if ($s !== '' && strtolower($s) !== 'n/a' && strtolower($s) !== 'na') {
                $out[$k] = $s;
            }
        }
        return $out;
    };

    $specCosts = $cleanSpec($specCosts);
    $specProperty = $cleanSpec($specProperty);
    $specHouse = $cleanSpec($specHouse);

    $hasCoords = !empty($p->latitude) && !empty($p->longitude)
                 && $p->latitude !== 'N/A' && $p->longitude !== 'N/A';
@endphp

<div class="th-page"
     x-data="{ active: 0, photos: @js(array_values($gallery)), expanded: false }"
     x-on:keydown.escape.window="expanded = false"
     x-on:keydown.arrow-left.window="if (expanded) active = (active - 1 + photos.length) % photos.length"
     x-on:keydown.arrow-right.window="if (expanded) active = (active + 1) % photos.length"
     x-effect="document.body.style.overflow = expanded ? 'hidden' : ''">

    <x-th.nav current="list"/>

    <div class="th-crumb">
        <div class="th-crumb-inner">
            <a href="{{ route('properties.index') }}">
                <x-th.icon name="chevron-left" size="14"/> All listings
            </a>
            <span class="th-crumb-sep">/</span>
            <a href="{{ route('properties.index', ['location' => $area]) }}">{{ $area }}</a>
            <span class="th-crumb-sep">/</span>
            <span class="th-crumb-curr">{{ str($p->title)->limit(60) }}</span>
        </div>
    </div>

    @if(count($gallery) > 0)
        <section class="th-gallery">
            <div class="th-gallery-inner">
                <div class="th-gal-main"
                     role="button"
                     tabindex="0"
                     x-on:click="expanded = true"
                     x-on:keydown.enter="expanded = true"
                     aria-label="Open photo viewer">
                    <img class="th-gal-main-img"
                         :src="photos[active]"
                         alt="{{ e($p->title) }}"
                         loading="eager"
                         fetchpriority="high"
                         draggable="false">
                    @if(count($gallery) > 1)
                        <button class="th-gal-arrow th-gal-arrow-l"
                                type="button"
                                x-on:click.stop="active = (active - 1 + photos.length) % photos.length"
                                aria-label="Previous photo">
                            <x-th.icon name="chevron-left" size="20"/>
                        </button>
                        <button class="th-gal-arrow th-gal-arrow-r"
                                type="button"
                                x-on:click.stop="active = (active + 1) % photos.length"
                                aria-label="Next photo">
                            <x-th.icon name="chevron-right" size="20"/>
                        </button>
                    @endif
                    <span class="th-gal-counter">
                        <span x-text="active + 1"></span> / {{ count($gallery) }}
                    </span>
                    @if($totalPhotos > 0)
                        <button type="button" class="th-gal-allphotos" x-on:click.stop="expanded = true">
                            <x-th.icon name="grid" size="14"/> All {{ $totalPhotos }} photos
                        </button>
                    @endif
                </div>
                <div class="th-gal-side">
                    @foreach(array_slice($gallery, 0, 4) as $i => $thumb)
                        <div class="th-gal-thumb"
                             :class="{ 'is-active': active === {{ $i }} }"
                             style="background-image: url('{{ e($thumb) }}');"
                             x-on:click="active = {{ $i }}">
                            @if($i === 3 && count($gallery) > 4)
                                <span class="th-gal-more">+{{ count($gallery) - 4 }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <div class="th-lightbox"
             x-show="expanded"
             x-transition.opacity
             x-cloak
             style="display:none;"
             x-on:click.self="expanded = false">
            <button class="th-lightbox-close" type="button" x-on:click="expanded = false" aria-label="Close photo viewer">
                <x-th.icon name="close" size="22"/>
            </button>
            <img class="th-lightbox-img" :src="photos[active]" alt="{{ e($p->title) }}" draggable="false">
            @if(count($gallery) > 1)
                <button class="th-lightbox-arrow th-lightbox-arrow-l"
                        type="button"
                        x-on:click.stop="active = (active - 1 + photos.length) % photos.length"
                        aria-label="Previous photo">
                    <x-th.icon name="chevron-left" size="24"/>
                </button>
                <button class="th-lightbox-arrow th-lightbox-arrow-r"
                        type="button"
                        x-on:click.stop="active = (active + 1) % photos.length"
                        aria-label="Next photo">
                    <x-th.icon name="chevron-right" size="24"/>
                </button>
            @endif
            <span class="th-lightbox-counter">
                <span x-text="active + 1"></span> / {{ count($gallery) }}
            </span>
        </div>
    @endif

    <section class="th-detail">
        <div class="th-detail-inner">

            <div class="th-detail-main">

                <header class="th-detail-head">
                    <div>
                        <div class="th-area">
                            <x-th.icon name="pin" size="14"/> {{ $area }}
                        </div>
                        <h1 class="th-detail-title">{{ $p->title }}</h1>
                        <div class="th-detail-meta">
                            @if($type)
                                <span><x-th.icon name="bed" size="14"/> {{ $type }}</span>
                            @endif
                            @if($p->total_rooms)
                                <span><x-th.icon name="home" size="14"/> {{ $p->total_rooms }} rooms total</span>
                            @endif
                        </div>
                    </div>
                    <div class="th-detail-price">
                        <span class="th-price-amount-lg">{{ $priceDisplay }}</span>
                        <span class="th-price-period">{{ $pricePeriod }}</span>
                        @if($depositFormatted || $billsIncluded)
                            <div class="th-detail-price-foot">
                                @if($depositFormatted) Deposit {{ $depositFormatted }} @endif
                                @if($depositFormatted && $billsIncluded) · @endif
                                @if($billsIncluded) Bills included @endif
                            </div>
                        @endif
                    </div>
                </header>

                <div class="th-pills">
                    <span class="th-pill th-pill-positive">
                        <span class="th-pill-dot"></span> {{ $availablePill }}
                    </span>
                    @if($type)
                        <span class="th-pill">{{ $type }}</span>
                    @endif
                    @if($isFurnished)
                        <span class="th-pill">Furnished</span>
                    @endif
                    @if($billsIncluded)
                        <span class="th-pill th-pill-accent">Bills included</span>
                    @endif
                </div>

                <div class="th-keyfacts">
                    <div class="th-keyfact">
                        <x-th.icon name="bed" size="18"/>
                        <div>
                            <div class="th-keyfact-l">Bedroom</div>
                            <div class="th-keyfact-v">{{ $bedroomLabel }}</div>
                        </div>
                    </div>
                    <div class="th-keyfact">
                        <x-th.icon name="bath" size="18"/>
                        <div>
                            <div class="th-keyfact-l">Bathroom</div>
                            <div class="th-keyfact-v">{{ $bathroomLabel }}</div>
                        </div>
                    </div>
                    <div class="th-keyfact">
                        <x-th.icon name="furnish" size="18"/>
                        <div>
                            <div class="th-keyfact-l">Furnishing</div>
                            <div class="th-keyfact-v">{{ $isFurnished ? 'Furnished' : 'Unfurnished' }}</div>
                        </div>
                    </div>
                    <div class="th-keyfact">
                        <x-th.icon name="calendar" size="18"/>
                        <div>
                            <div class="th-keyfact-l">Available</div>
                            <div class="th-keyfact-v">{{ $availableRaw !== '' ? $availableRaw : 'Now' }}</div>
                        </div>
                    </div>
                </div>

                @if($isMultiRoom)
                    <section class="th-section">
                        <h2 class="th-section-h">
                            Rooms available
                            <span class="th-rooms-count">{{ $roomsAvailable }}</span>
                        </h2>
                        <div class="th-rooms">
                            @foreach($rooms as $room)
                                <div class="th-room">
                                    <div class="th-room-mark"><x-th.icon name="bed" size="18"/></div>
                                    <div class="th-room-info">
                                        <div class="th-room-type">{{ $room['type'] }} room</div>
                                        @if($room['deposit'])
                                            <div class="th-room-sub">Deposit {{ $room['deposit'] }}</div>
                                        @endif
                                    </div>
                                    <div class="th-room-price">
                                        @if($room['price'])
                                            <span class="th-room-price-amt">{{ $room['price'] }}</span>
                                            <span class="th-price-period">/mo</span>
                                        @else
                                            <span class="th-room-poa">Price on request</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                @php
                    $hasDescription = $description !== '';
                    $hasDetails = count($specCosts) || count($specProperty) || count($specHouse);
                @endphp

                @if($hasDescription || $hasDetails)
                    <div class="th-about-grid {{ $hasDescription && $hasDetails ? '' : 'is-single' }}">
                        @if($hasDescription)
                            <section class="th-section th-about-desc">
                                <h2 class="th-section-h">About this place</h2>
                                <p class="th-section-body" style="white-space: pre-line;">{{ $description }}</p>
                            </section>
                        @endif

                        @if($hasDetails)
                            <aside class="th-section th-about-spec">
                                <h2 class="th-section-h">The details</h2>
                                <div class="th-spec th-spec-stacked">
                                    @if(count($specCosts))
                                        <div class="th-spec-col">
                                            <div class="th-spec-h">Costs</div>
                                            @foreach($specCosts as $k => $v)
                                                <dl><dt>{{ $k }}</dt><dd>{{ $v }}</dd></dl>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if(count($specProperty))
                                        <div class="th-spec-col">
                                            <div class="th-spec-h">Property</div>
                                            @foreach($specProperty as $k => $v)
                                                <dl><dt>{{ $k }}</dt><dd>{{ $v }}</dd></dl>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if(count($specHouse))
                                        <div class="th-spec-col">
                                            <div class="th-spec-h">Housemates</div>
                                            @foreach($specHouse as $k => $v)
                                                <dl><dt>{{ $k }}</dt><dd>{{ $v }}</dd></dl>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </aside>
                        @endif
                    </div>
                @endif

                <section class="th-section">
                    <h2 class="th-section-h">Location</h2>
                    <div class="th-fakemap">
                        @if($hasCoords)
                            <iframe
                                src="https://www.google.com/maps?q={{ $p->latitude }},{{ $p->longitude }}&z=15&output=embed"
                                style="border:0; width:100%; height:100%;"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                title="Map of {{ $area }}"></iframe>
                        @else
                            <svg viewBox="0 0 600 240" preserveAspectRatio="none" class="th-fakemap-svg">
                                <defs>
                                    <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                                        <path d="M40 0H0V40" stroke="rgba(14,27,45,.06)" fill="none" stroke-width="1"/>
                                    </pattern>
                                </defs>
                                <rect width="600" height="240" fill="var(--th-bg-soft)"/>
                                <rect width="600" height="240" fill="url(#grid)"/>
                                <circle cx="300" cy="120" r="22" fill="var(--th-accent)" fill-opacity=".18"/>
                                <circle cx="300" cy="120" r="10" fill="var(--th-accent)"/>
                            </svg>
                        @endif
                        <div class="th-fakemap-pin">
                            <x-th.icon name="pin" size="16"/> {{ $area }}
                        </div>
                    </div>
                    @if($hasCoords)
                        <div class="th-loc-row">
                            <div class="th-loc-stat">
                                <div class="th-loc-stat-v">{{ $area }}</div>
                                <div class="th-loc-stat-l">Neighbourhood</div>
                            </div>
                            <div class="th-loc-stat">
                                <div class="th-loc-stat-v"><a href="https://www.google.com/maps?q={{ $p->latitude }},{{ $p->longitude }}" target="_blank" rel="noopener" style="text-decoration: underline;">Open</a></div>
                                <div class="th-loc-stat-l">in Google Maps</div>
                            </div>
                        </div>
                    @endif
                </section>
            </div>

            <aside class="th-detail-aside">
                <div class="th-actioncard">
                    <div class="th-actioncard-price">
                        <span class="th-price-amount-lg">{{ $priceDisplay }}</span>
                        <span class="th-price-period">{{ $hasRoomRange ? '/ room / mo' : '/ month' }}</span>
                    </div>
                    <div class="th-actioncard-sub">
                        @if($isMultiRoom) {{ $roomsAvailable }} rooms available · @endif @if($billsIncluded) Includes bills · @endif No fees · {{ $availablePill }}
                    </div>

                    @php
                        $mapsQuery = $hasCoords
                            ? $p->latitude . ',' . $p->longitude
                            : trim(($location !== '' ? $location : $area));
                        $mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($mapsQuery);
                    @endphp
                    <a href="{{ $mapsUrl }}" target="_blank" rel="noopener" class="th-btn th-btn-primary th-btn-block">
                        <x-th.icon name="map" size="16"/> Open location in Google Maps
                    </a>

                    @auth
                        @php $hasAgent = $agentName !== '' && $agentName !== 'N/A'; @endphp
                        @if($hasAgent || $hasLandlord)
                            <div class="th-actioncard-meta">
                                @if($hasAgent)
                                    <div class="th-actioncard-agent">
                                        <div class="th-agent-mark">{{ $agentInitials }}</div>
                                        <div>
                                            <div class="th-agent-name">
                                                {{ $agentName }}
                                                @if($isPayingAgent)
                                                    <span class="th-bolt" title="Paying agent — verified, faster replies" aria-label="Paying agent">⚡</span>
                                                @endif
                                            </div>
                                            <div class="th-agent-trust">
                                                @if($isPayingAgent)
                                                    <x-th.icon name="check" size="12"/> Paying agent · faster replies
                                                @else
                                                    <x-th.icon name="check" size="12"/> Verified
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if($hasLandlord)
                                    <a href="{{ $landlordUrl }}" target="_blank" rel="noopener"
                                       class="th-landlord-link {{ $hasAgent ? 'has-agent' : '' }}">
                                        <span class="th-landlord-link-txt">
                                            <x-th.icon name="home" size="14"/> View landlord profile
                                        </span>
                                        <x-th.icon name="chevron-right" size="16"/>
                                    </a>
                                @endif
                            </div>
                        @endif
                    @endauth

                    <div class="th-actioncard-quick">
                        <button type="button" x-on:click="navigator.share ? navigator.share({title: document.title, url: location.href}) : navigator.clipboard.writeText(location.href).then(() => alert('Link copied'))">
                            <x-th.icon name="share" size="14"/> Share
                        </button>
                        @if($hasCoords)
                            <a href="https://www.google.com/maps?q={{ $p->latitude }},{{ $p->longitude }}" target="_blank" rel="noopener">
                                <x-th.icon name="map" size="14"/> Maps
                            </a>
                        @endif
                        @auth
                            @if(!empty($p->link) && $p->link !== 'N/A')
                                <a href="{{ $p->link }}" target="_blank" rel="noopener">
                                    <x-th.icon name="share" size="14"/> Original
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </aside>

        </div>
    </section>

    <x-th.footer/>

</div>

@stack('scripts')

</body>
</html>
