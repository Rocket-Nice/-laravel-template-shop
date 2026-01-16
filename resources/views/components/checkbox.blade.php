@props(['disabled' => false])
@props(['type' => 'checkbox'])

<input type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-violet-700 focus:ring-violet-700 rounded-md shadow-sm']) !!}>
