@props([
    'gifts' => [],
    'originalTotal' => 0,
])
@php($giftItems = collect($gifts)->filter(function ($gift) {
    return !empty($gift['name']);
})->values())
@php($totalValue = (int)$giftItems->sum('price'))
@php($totalFormatted = number_format($totalValue, 0, '.', ' ') . ' ₽')
@php($zeroFormatted = '0 ₽')
@php($fallbackImage = '/img/cat-bag/animation-sack/product.svg')

@if($giftItems->isNotEmpty())
  <div class="relative flex flex-col gap-[20px] p-5 bg-gradient-to-l from-[#9BACA1] to-[#D7DACD]">
      <div class="absolute -top-10 -left-16">
          <img src="{{ asset('img/cat-bag/cat-single-big.png') }}" alt="single">
      </div>

      <p class="pl-[88px] text-5xl">Подарки</p>

      <div class="flex flex-col gap-[8px]">
          @foreach($giftItems as $gift)
              @php($giftImage = $gift['image'] ?? $fallbackImage)
              @php($giftPrice = (int)($gift['price'] ?? 0))
              @php($giftPriceFormatted = number_format($giftPrice, 0, '.', ' ') . ' ₽')
              <div class="p-2 flex gap-[12px] bg-[#FFFFFF] items-start">
                  <div class="w-[72px] h-[72px] shrink-0 flex items-center justify-center overflow-hidden">
                      <img class="w-full h-full object-contain" src="{{ asset($giftImage) }}" alt="pr">
                  </div>

                  <div class="flex flex-col gap-[12px] w-full">
                      <p class="text-[20px] pr-0 md:pr-[70px]">{{ $gift['name'] }}</p>
                      <p class="text-[#B1908E] text-[20px] self-end line-through font-inter_font">{{ $giftPriceFormatted }}</p>
                  </div>
              </div>
          @endforeach

          <div class="flex justify-between mt-3">
              <p class="text-[24px] font-[600] font_main-font italic">Сумма:</p>
              <div class="flex gap-[12px] items-center font-inter_font">
                  <p class="text-[20px] leading-[28px]">{{ $zeroFormatted }}</p>
                  <p class="text-[20px] line-through leading-[28px]">{{ $totalFormatted }}</p>
              </div>
          </div>
      </div>
  </div>
@endif
