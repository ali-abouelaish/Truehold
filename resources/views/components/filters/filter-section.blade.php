{{--
  Reusable filter group: title + optional description + slot (fields).
  Usage: <x-filters.filter-section title="Where" description="Optional">...</x-filters.filter-section>
--}}
@props([
    'title',
    'description' => null,
])
<section class="thf-section" {{ $attributes }}>
    <header class="thf-section__head">
        <h4 class="thf-section__title">{{ $title }}</h4>
        @if($description)
            <p class="thf-section__desc">{{ $description }}</p>
        @endif
    </header>
    <div class="thf-section__body">
        {{ $slot }}
    </div>
</section>
