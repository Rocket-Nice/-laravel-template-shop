<div id="instaPromo-success" class="relative border-none !px-3 !py-3 !sm:px-5 !sm:py-5 w-full max-w-[340px] sm:max-w-[547px] !rounded-4xl bg-myGreen " style="display: none;">
  <div class="mb-4 flex items-start justify-between">
    <div class="w-7"></div>
    <div>
      <div class="text-lg text-center mt-2" style="max-width: 471px">
        Расскажите о своих подарках <br class="sm:hidden"/>в сторис,<br class="hidden sm:inline"/>
        отметив @le__mousse<br class="sm:hidden"/> и @nechaeva__proekt,<br class="hidden sm:inline"/>
        и получите <br class="sm:hidden"/>возможность <span class="font-bold">забрать iPhone 16!</span>
      </div>
    </div>
    <button class="outline-none" onclick="Fancybox.close()" tabindex="-1">
      <svg width="28" height="28" viewBox="0 0 28 28" fill="none" stroke="currentColor" xmlns="http://www.w3.org/2000/svg">
        <path d="M14 25.6667C20.4167 25.6667 25.6667 20.4167 25.6667 14C25.6667 7.58333 20.4167 2.33333 14 2.33333C7.58333 2.33333 2.33333 7.58333 2.33333 14C2.33333 20.4167 7.58333 25.6667 14 25.6667Z" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M10.6983 17.3017L17.3017 10.6983" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M17.3017 17.3017L10.6983 10.6983" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </button>
  </div>

  <div class="relative z-10">

    <div class="text-center">
      <img src="{{ asset('img/happy_coupon/iphone-popup.png?1') }}" alt="" class="block mx-auto" style="max-width: 92px;">
    </div>
  </div>
  <div class="text-center mt-4">
    <a href="{{ route('cabinet.order.show', $order->slug) }}#prizes" class="h-9 inline-flex items-center justify-center px-6 border border-black text-xl leading-none font-medium uppercase">мои подарки</a>
{{--    <x-public.primary-button href="{{ route('cabinet.order.show', $order->slug) }}#prizes" class="uppercase">мои подарки</x-public.primary-button>--}}
  </div>
  <div class="text-center mt-3 text-sm">
    <a href="{{ route('cabinet.order.index') }}" class="underline">Подробные условия — в личном кабинете</a>
  </div>
</div>
