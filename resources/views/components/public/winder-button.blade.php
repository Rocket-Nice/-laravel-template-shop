@props(['href' => false])

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'h-11 inline-flex items-center justify-center px-3 md:px-4 px-7 bg-winterGreen text-white text-xl leading-none font-medium']) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'submit', 'class' => 'h-11 inline-flex items-center justify-center px-3 md:px-4 px-7 bg-winterGreen text-white text-xl leading-none font-medium']) }}>
        {{ $slot }}
    </button>
@endif
