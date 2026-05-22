@props(['property'])

@php
    $p = $property;
    $type = trim((string) ($p->property_type ?? ''));
    $isStudio = stripos($type, 'studio') !== false;
    $rooms = (int) ($p->total_rooms ?? 0);
    $bedsLabel = $isStudio
        ? 'Studio'
        : ($rooms > 0 ? $rooms . ' ' . str($type ?: 'room')->lower() . ($rooms === 1 ? '' : 's') : ($type ?: 'Room'));

    $billsRaw = strtolower(trim((string) ($p->bills_included ?? '')));
    $billsIncluded = in_array($billsRaw, ['yes', 'true', '1', 'included'], true);

    $available = trim((string) ($p->available_date ?? ''));
    if ($available === '' || strtolower($available) === 'now' || stripos($available, 'avail') === 0) {
        $availableLabel = 'Available now';
    } else {
        $availableLabel = $available;
    }
@endphp

<div class="th-facts">
    <span class="th-fact">
        <x-th.icon name="bed" size="14"/>
        {{ $bedsLabel }}
    </span>
    <span class="th-fact">
        <x-th.icon name="calendar" size="14"/>
        {{ $availableLabel }}
    </span>
    @if($billsIncluded)
        <span class="th-fact th-fact-accent">Bills incl.</span>
    @endif
</div>
