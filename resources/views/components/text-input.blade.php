@props(['disabled' => false])
@props(['readonly' => false])
@if($readonly)
  @endif
<input {{ $disabled ? 'disabled' : '' }} {{ $readonly ? 'readonly' : '' }} {!! $attributes->merge(['class' => 'block w-full border-gray-300 focus:border-lemousseColor focus:ring-lemousseColor rounded-md shadow-sm']) !!}>
