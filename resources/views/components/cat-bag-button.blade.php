@props([
    'variant' => 'primary', // primary, outline
    'disabled' => false,
    'counter' => null, // тут пока счетчик по типу "1 из 3"
])

@php
    $baseClasses =
        'w-full h-[48px] inline-flex items-center justify-center font-normal text-base leading-5 transition-all duration-300 p-[14px_24px] font-inter_font';

    // варианты
    $variantClasses = match ($variant) {
        'outline' => 'border border-[#C5C5C5] text-[#545454] hover:border-[#545454] focus:ring-[#545454]',
        default => 'bg-[#545454] text-white hover:bg-[#414141] focus:ring-[#414141]',
    };

    // disabled
    $disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed' : '';

    // классы
    $classes = implode(' ', [$baseClasses, $variantClasses, $disabledClasses]);
@endphp

<button {{ $attributes->merge(['class' => $classes]) }} @disabled($disabled)>
    @if (!$classes)
        <p>Нету данных</p>
    @endif

    {{ $slot }}

    @if ($counter)
        <span class="ml-2 text-xs">{{ $counter }}</span>
    @endif
</button>
