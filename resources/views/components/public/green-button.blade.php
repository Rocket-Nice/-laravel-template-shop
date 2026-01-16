@props(['href' => false])

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'h-14 bg-myGreen inline-flex items-center justify-center px-8 md:px-12 lg:px-16 text-xl leading-none font-medium rounded-2xl text-center']) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'submit', 'class' => 'h-14 bg-myGreen inline-flex items-center justify-center px-8 md:px-12 lg:px-16 text-xl leading-none font-medium rounded-2xl text-center']) }}>
        {{ $slot }}
    </button>
@endif
