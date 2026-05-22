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

    // Photos — fall back gracefully across the various accessors
    $gallery = $p->high_quality_photos_array ?? [];
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

    // Bedroom label
    $bedroomLabel = $isStudio ? 'Studio' : 'Room available';

    // Price
    $price = $p->price ?? null;
    $priceFormatted = is_numeric($price) ? '£' . number_format((float) $price, 0) : (string) ($p->formatted_price ?? '£' . $price);
    $deposit = $p->deposit ?? null;
    $depositFormatted = is_numeric($deposit) ? '£' . number_format((float) $deposit, 0) : ($deposit ? (string) $deposit : null);

    // Agent
    $agentName = (string) ($p->agent_name ?? '');
    $agentInitials = collect(explode(' ', $agentName))
        ->take(2)->map(fn($w) => mb_substr($w, 0, 1))->implode('');
    $agentInitials = $agentInitials !== '' ? strtoupper($agentInitials) : 'TH';

    $payingRaw = $p->paying ?? null;
    $isPayingAgent = $payingRaw !== null && in_array(strtolower(trim((string) $payingRaw)), ['yes', 'true', '1'], true);

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

<div class="th-page" x-data="{ active: 0, photos: @js(array_values($gallery)) }">

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
                     :style="`background-image: url('${photos[active]}')`">
                    @if(count($gallery) > 1)
                        <button class="th-gal-arrow th-gal-arrow-l"
                                x-on:click="active = (active - 1 + photos.length) % photos.length"
                                aria-label="Previous photo">
                            <x-th.icon name="chevron-left" size="20"/>
                        </button>
                        <button class="th-gal-arrow th-gal-arrow-r"
                                x-on:click="active = (active + 1) % photos.length"
                                aria-label="Next photo">
                            <x-th.icon name="chevron-right" size="20"/>
                        </button>
                    @endif
                    <span class="th-gal-counter">
                        <span x-text="active + 1"></span> / {{ count($gallery) }}
                    </span>
                    @if($totalPhotos > 0)
                        <span class="th-gal-allphotos">
                            <x-th.icon name="grid" size="14"/> All {{ $totalPhotos }} photos
                        </span>
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
                        <span class="th-price-amount-lg">{{ $priceFormatted }}</span>
                        <span class="th-price-period">per month</span>
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

                @if($description !== '')
                    <section class="th-section">
                        <h2 class="th-section-h">About this place</h2>
                        <p class="th-section-body" style="white-space: pre-line;">{{ $description }}</p>
                    </section>
                @endif

                @if(count($specCosts) || count($specProperty) || count($specHouse))
                    <section class="th-section">
                        <h2 class="th-section-h">The details</h2>
                        <div class="th-spec">
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
                    </section>
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
                        <span class="th-price-amount-lg">{{ $priceFormatted }}</span>
                        <span class="th-price-period">/ month</span>
                    </div>
                    <div class="th-actioncard-sub">
                        @if($billsIncluded) Includes bills · @endif No fees · {{ $availablePill }}
                    </div>

                    @auth
                        <a href="mailto:?subject=Viewing request: {{ urlencode($p->title) }}" class="th-btn th-btn-primary th-btn-block">
                            Request a viewing
                        </a>
                        @if($agentName !== '' && $agentName !== 'N/A')
                            <a href="mailto:?subject=Question about {{ urlencode($p->title) }}" class="th-btn th-btn-outline th-btn-block">
                                Message the agent
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login', ['redirect' => url()->current()]) }}" class="th-btn th-btn-primary th-btn-block">
                            Contact your agent to request a viewing
                        </a>
                    @endauth

                    @auth
                        @if($agentName !== '' && $agentName !== 'N/A')
                            <div class="th-actioncard-meta">
                                <div class="th-actioncard-agent">
                                    <div class="th-agent-mark">{{ $agentInitials }}</div>
                                    <div>
                                        <div class="th-agent-name">{{ $agentName }}</div>
                                        <div class="th-agent-trust">
                                            @if($isPayingAgent)
                                                <x-th.icon name="check" size="12"/> Paying agent · faster replies
                                            @else
                                                <x-th.icon name="check" size="12"/> Verified
                                            @endif
                                        </div>
                                    </div>
                                </div>
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
