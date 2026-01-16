@props(['disabled' => false])
@props(['type' => 'checkbox'])

<input type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-black focus:border-black hover:border-black checked:border-black checked:focus:border-black checked:hover:border-black !bg-transparent w-6 h-6']) !!}>
