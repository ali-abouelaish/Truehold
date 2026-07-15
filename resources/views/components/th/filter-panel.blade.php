@props([
    'locations' => [],
    'propertyTypes' => [],
    'agentNames' => [],
    'agentsWithPaying' => [],
    'action' => null,
])

@php
    $req = request();
    $formAction = $action ?: route('properties.index');
    $defaults = [
        'min_price' => $req->input('min_price', 400),
        'max_price' => $req->input('max_price', 2500),
        'location'  => (string) $req->input('location', ''),
        'property_type' => $req->input('property_type', ''),
        'couples_allowed' => $req->input('couples_allowed', ''),
        'ensuite'   => $req->input('ensuite', '') === 'yes',
        'paying_only' => $req->boolean('paying_only'),
        'available_date' => $req->input('available_date', ''),
    ];

    // Landlord/agent select data. The feed returns `agents_with_paying` as a
    // flat list of paying agent names; the DB fallback returns a name => bool
    // map. Normalise both into a set of paying agent names.
    $awp = collect($agentsWithPaying ?? []);
    $payingNames = $awp->contains(fn($v) => is_bool($v))
        ? $awp->filter()->keys()
        : $awp->values();
    $payingSet = $payingNames->map(fn($n) => (string) $n)->filter()->unique()->all();

    $agentList = collect($agentNames ?? [])
        ->map(fn($n) => (string) $n)
        ->filter()
        ->unique()
        ->sort()
        ->values();
    $selectedAgent = (string) $req->input('agent_name', '');
@endphp

{{-- Open/close is plain JS (vanilla class toggle) so the drawer works even if Alpine breaks elsewhere on the page. Alpine still drives the form state inside. --}}
<div class="th-filter-scrim" id="thFilterScrim" onclick="thFilterClose()"></div>

<aside class="th-filter" id="thFilter">
    <div x-data="thFilterPanel(@js($defaults))" style="display:contents;">
        <form method="GET" action="{{ $formAction }}" x-ref="form">
            {{-- Mark this as an authoritative in-app filter submit + carry filters
                 that have no visible control here so they aren't dropped. --}}
            <input type="hidden" name="qf" value="1">
            @foreach(['available_date', 'management_company', 'room_count'] as $pk)
                @if($req->filled($pk))
                    <input type="hidden" name="{{ $pk }}" value="{{ $req->input($pk) }}">
                @endif
            @endforeach
            <header class="th-filter-head">
                <div>
                    <div class="th-filter-eyebrow">Refine</div>
                    <h2 class="th-filter-title">Search filters</h2>
                </div>
                <button type="button" class="th-filter-close" onclick="thFilterClose()" aria-label="Close filters">
                    <x-th.icon name="close" size="18"/>
                </button>
            </header>

            <div class="th-filter-body">

                @auth
                    <section class="th-filter-section">
                        <div class="th-filter-row-toggle">
                            <div>
                                <div class="th-filter-h">Paying agents only</div>
                                <div class="th-filter-sub">Verified, faster replies, no spam listings</div>
                            </div>
                            <button type="button" class="th-switch" :class="{ 'is-on': paying }" x-on:click="paying = !paying">
                                <span></span>
                            </button>
                            <input type="hidden" name="paying_only" :value="paying ? '1' : ''">
                        </div>
                    </section>

                    @if($agentList->isNotEmpty())
                        <section class="th-filter-section">
                            <div class="th-filter-h">Landlord / agent</div>
                            <div class="th-filter-sub" style="margin-bottom: 10px;">
                                <span class="th-bolt">⚡</span> marks paying agents — verified, faster replies
                            </div>
                            <select name="agent_name" class="th-filter-select">
                                <option value="">All landlords</option>
                                @foreach($agentList as $name)
                                    <option value="{{ $name }}" @selected($selectedAgent === $name)>{{ in_array($name, $payingSet, true) ? '⚡ ' : '' }}{{ $name }}</option>
                                @endforeach
                            </select>
                        </section>
                    @endif
                @endauth

                {{-- Preserve the location chosen in the hero "Where" select on submit --}}
                <input type="hidden" name="location" value="{{ $req->input('location', '') }}">

                <section class="th-filter-section">
                    <div class="th-filter-h">Monthly budget</div>
                    <div class="th-filter-bnums">
                        <span x-text="'£' + Number(budget[0]).toLocaleString()"></span>
                        <span x-text="'£' + Number(budget[1]).toLocaleString() + (budget[1] >= 2500 ? '+' : '')"></span>
                    </div>
                    <div class="th-range" x-ref="range">
                        <div class="th-range-track"></div>
                        <div class="th-range-fill"
                             :style="`left:${pct(budget[0])}%; right:${100 - pct(budget[1])}%`"></div>
                        <button type="button" class="th-range-thumb"
                                :style="`left:${pct(budget[0])}%`"
                                x-on:mousedown="startDrag('lo', $event)"
                                x-on:touchstart="startDrag('lo', $event)"
                                aria-label="Minimum price"></button>
                        <button type="button" class="th-range-thumb"
                                :style="`left:${pct(budget[1])}%`"
                                x-on:mousedown="startDrag('hi', $event)"
                                x-on:touchstart="startDrag('hi', $event)"
                                aria-label="Maximum price"></button>
                    </div>
                    <div class="th-filter-bticks">
                        <span>£400</span><span>£1,000</span><span>£1,500</span><span>£2,500+</span>
                    </div>
                    <input type="hidden" name="min_price" :value="budget[0]">
                    <input type="hidden" name="max_price" :value="budget[1] >= 2500 ? '' : budget[1]">
                </section>

                @if(count($propertyTypes) > 0)
                    <section class="th-filter-section">
                        <div class="th-filter-h">Property type</div>
                        <div class="th-filter-grid">
                            <button type="button"
                                    class="th-chip"
                                    :class="{ 'is-on': type === '' }"
                                    x-on:click="type = ''">Any</button>
                            @foreach($propertyTypes as $t)
                                @php $tStr = (string) $t; @endphp
                                @if($tStr !== '')
                                    <button type="button"
                                            class="th-chip"
                                            :class="{ 'is-on': type === @js($tStr) }"
                                            x-on:click="type = @js($tStr)">
                                        {{ $tStr }}
                                    </button>
                                @endif
                            @endforeach
                        </div>
                        <input type="hidden" name="property_type" :value="type">
                    </section>
                @endif

                <section class="th-filter-section">
                    <div class="th-filter-h">Household</div>
                    <div class="th-seg">
                        <button type="button" :class="{ 'is-on': couples === '' }"     x-on:click="couples = ''">Any</button>
                        <button type="button" :class="{ 'is-on': couples === 'yes' }"  x-on:click="couples = 'yes'">Couples accepted</button>
                        <button type="button" :class="{ 'is-on': couples === 'no' }"   x-on:click="couples = 'no'">Singles only</button>
                    </div>
                    <input type="hidden" name="couples_allowed" :value="couples">
                </section>

                <section class="th-filter-section">
                    <div class="th-filter-h">Must have</div>
                    <div class="th-filter-musts">
                        <label class="th-check">
                            <input type="checkbox" name="ensuite" value="yes" x-model="ensuite">
                            <span></span> Ensuite bathroom
                        </label>
                    </div>
                </section>

            </div>

            <footer class="th-filter-foot">
                <a href="{{ $formAction }}?reset=1" class="th-link-soft">Reset all</a>
                <div style="display:flex; gap:8px;">
                    <button type="button" class="th-btn th-btn-ghost" onclick="thFilterClose()">Cancel</button>
                    <button type="submit" class="th-btn th-btn-primary">Show results</button>
                </div>
            </footer>
        </form>
    </div>
</aside>

@push('scripts')
<script>
    function thFilterOpen() {
        document.getElementById('thFilter').classList.add('is-open');
        document.getElementById('thFilterScrim').classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }
    function thFilterClose() {
        document.getElementById('thFilter').classList.remove('is-open');
        document.getElementById('thFilterScrim').classList.remove('is-open');
        document.body.style.overflow = '';
    }
    window.thFilterOpen = thFilterOpen;
    window.thFilterClose = thFilterClose;
    window.addEventListener('th-filter-open', thFilterOpen);
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') thFilterClose();
    });

    function thFilterPanel(defaults) {
        return {
            paying: !!defaults.paying_only,
            type: defaults.property_type || '',
            couples: defaults.couples_allowed || '',
            ensuite: !!defaults.ensuite,
            budget: [Number(defaults.min_price) || 400, Number(defaults.max_price) || 2500],
            min: 400,
            max: 2500,
            step: 50,
            _drag: null,
            pct(v) { return ((v - this.min) / (this.max - this.min)) * 100; },
            startDrag(which, ev) {
                ev.preventDefault();
                this._drag = which;
                const move = (e) => {
                    const r = this.$refs.range.getBoundingClientRect();
                    const px = (e.touches ? e.touches[0].clientX : e.clientX) - r.left;
                    let v = this.min + (px / r.width) * (this.max - this.min);
                    v = Math.round(v / this.step) * this.step;
                    v = Math.max(this.min, Math.min(this.max, v));
                    if (this._drag === 'lo') this.budget = [Math.min(v, this.budget[1] - this.step), this.budget[1]];
                    else this.budget = [this.budget[0], Math.max(v, this.budget[0] + this.step)];
                };
                const up = () => {
                    document.removeEventListener('mousemove', move);
                    document.removeEventListener('touchmove', move);
                    document.removeEventListener('mouseup', up);
                    document.removeEventListener('touchend', up);
                    this._drag = null;
                };
                document.addEventListener('mousemove', move);
                document.addEventListener('touchmove', move, { passive: false });
                document.addEventListener('mouseup', up);
                document.addEventListener('touchend', up);
            },
        };
    }
    window.thFilterPanel = thFilterPanel;
</script>
@endpush
