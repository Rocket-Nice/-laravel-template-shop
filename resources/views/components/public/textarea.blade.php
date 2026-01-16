@props(['disabled' => false])

<textarea {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'block w-full border border-myGray bg-white placeholder-myGray m-text-body d-text-body py-1.5 px-3 focus:ring-0 focus:border-b-black h-[200px] resize-none']) !!}>{{ $slot }}</textarea>

