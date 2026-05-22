<footer class="th-foot">
    <div class="th-foot-inner">
        <a href="{{ route('properties.index') }}" class="th-logo">
            <span class="th-logo-mark">
                <svg viewBox="0 0 32 32" width="22" height="22" fill="none">
                    <path d="M6 14L16 6l10 8v11a1 1 0 0 1-1 1h-6v-7h-6v7H7a1 1 0 0 1-1-1V14Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                </svg>
            </span>
            <span class="th-logo-wordmark">truehold</span>
        </a>
        <div class="th-foot-cols">
            <div>
                <div class="th-foot-h">Find a home</div>
                <a href="{{ route('properties.index') }}">All listings</a>
                <a href="{{ route('properties.map') }}">Map view</a>
                <a href="{{ route('properties.index', ['property_type' => 'Studio']) }}">Studios</a>
                <a href="{{ route('properties.index', ['property_type' => 'Flat']) }}">Flats</a>
            </div>
            <div>
                <div class="th-foot-h">Truehold</div>
                <a href="{{ route('login', ['redirect' => url()->current()]) }}">Agent sign in</a>
                <a href="mailto:hello@truehold.co.uk">Contact</a>
                <a href="#">Privacy</a>
                <a href="#">Terms</a>
            </div>
        </div>
    </div>
    <div class="th-foot-mini">© {{ date('Y') }} TrueHold · London-made</div>
</footer>
