{{--
  Segmented radio group (e.g. household: any / couples / singles).
  Expects slot content to be radio <label> items with class thf-segment__opt.
--}}
@props([
    'label',
])
<fieldset class="thf-field thf-field--radios" {{ $attributes }}>
    <legend class="thf-label">{{ $label }}</legend>
    <div class="thf-segment" role="radiogroup" aria-label="{{ $label }}">
        {{ $slot }}
    </div>
</fieldset>
