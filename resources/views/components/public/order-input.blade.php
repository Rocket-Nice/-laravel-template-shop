
@props(['disabled' => false])
@props(['readonly' => false])
@props(['label' => false])
@if($readonly)
@endif
@if($label)
  <label for="{{ $attributes['id'] ?? '' }}" class="block font-medium d-text-body m-text-body">{{ $label }}</label>
@endif
<input {{ $disabled ? 'disabled' : '' }} {{ $readonly ? 'readonly' : '' }} {!! $attributes->merge(['class' => 'block w-full border border-myGray bg-transparent placeholder-myGray m-subtitle-2 d-subtitle-2 py-2.5 sm:py-1.75 px-3 leading-none focus:ring-0 focus:border-black']) !!}>



