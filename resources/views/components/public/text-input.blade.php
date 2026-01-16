@props(['disabled' => false])
@props(['readonly' => false])
@if($readonly)
  @endif
<input {{ $disabled ? 'disabled' : '' }} {{ $readonly ? 'readonly' : '' }} {!! $attributes->merge(['class' => 'block w-full border-0 border-b border-b-myGray bg-transparent placeholder-myGray m-text-body d-text-body py-1.5 px-3 focus:ring-0 focus:border-b-black']) !!}>
