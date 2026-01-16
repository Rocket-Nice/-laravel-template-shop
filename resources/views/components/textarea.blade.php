@props(['disabled' => false])

<textarea {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-lemousseColor focus:ring-lemousseColor rounded-md shadow-sm resize-none']) !!}>{{ $slot }}</textarea>

