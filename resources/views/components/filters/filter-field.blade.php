{{--
  Label + control wrapper for accessibility.
  Usage: <x-filters.filter-field label="Location" for="thf_location">...</x-filters.filter-field>
--}}
@props([
    'label',
    'for' => null,
    'optional' => false,
])
<div class="thf-field" {{ $attributes }}>
    <label class="thf-label" @if($for) for="{{ $for }}" @endif>
        {{ $label }}
        @if($optional)
            <span class="thf-label__optional">(optional)</span>
        @endif
    </label>
    <div class="thf-field__control">
        {{ $slot }}
    </div>
</div>
