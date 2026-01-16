<x-light-layout>
  <style>
    {{--body, html {--}}
    {{--  background: url({{ asset('img/happy_coupon/hc_bg.jpg') }});--}}
    {{--  color: #fff;--}}
    {{--}--}}

  </style>
  <x-slot name="custom_vite">
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/happy_coupon/script.js'])
  </x-slot>
  <div id="happy-coupon-page">
    <div id="loader" style="display: none;" class="fixed top-0 right-0 w-screen h-screen z-50 flex items-center justify-center min-h-screen bg-gray-100">
      <div class="w-full max-w-md bg-gray-300 rounded-full h-4 overflow-hidden">
        <div id="loading-bar" class="h-full" style="width: 0;"></div>
      </div>
    </div>
  </div>
  {{--  style="background: #F6F6F6;"--}}
  <div class="overflow-hidden w-full max-w-[500px] mx-auto min-h-screen py-12" >
    <div class="relative">
      <div class="text-center uppercase text-xl title-img">
        ВЫБЕРИ СВОЙ СЧАСТЛИВЫЙ КОНВЕРТ<br class="hidden sm:inline"/>
        LE&nbsp;MOUSSE
      </div>
      {{--      <div class="text-center title-img">--}}
      {{--        <img src="{{ asset('img/happy_coupon/hc_title.png') }}" alt="">--}}
      {{--      </div>--}}
      <div class="coupones-grid">
        @foreach($prizes_grid as $rows)
          <div class="flex coupones-row justify-center">
            @foreach($rows as $item)
              <div class="coupone-item img">
                <img src="{!! $item !!}" alt="" style="transform: scale(1.4)">
              </div>
            @endforeach
          </div>
        @endforeach
      </div>
      <div class="text-center mb-4" style="position:relative;z-index: 10;">
        <a class="h-11 text-center inline-flex items-center justify-center px-7 bg-winterGreen text-white uppercase text-sm sm:text-base md:text-lg lg:text-xl !leading-none font-medium no-underline" id="show-coupone-btn" @if(!$attempts_left){!! 'href="'.route('cabinet.home.index').'"' !!}@else{!! 'style="display: none;"' !!}@endif><span class="text">@if(!$attempts_left){!! 'Личный кабинет' !!}@else{!! 'Далее' !!}@endif</span></a>
        <div class="message-coupones-left mt-3">У вас осталось <span id="attempts">{{ $attempts_left }}</span> из {{ $order->giftCoupons()->count() }} попыток</div>
      </div>
      <div class="flex flex-wrap" id="result-coupones">
        <div class="w-1/2 coupon-item"><div class="img" data-item="1"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="2"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="3"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="4"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="5"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="6"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="7"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="8"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="9"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="10"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="11"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="12"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="13"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="14"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="15"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="16"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="17"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="18"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="19"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="20"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="20"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="21"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="22"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="23"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="24"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="25"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="26"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="27"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="28"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="29"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="30"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="31"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="32"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="33"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="34"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="35"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="36"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="37"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="38"></div></div>
        <div class="w-1/2 coupon-item"><div class="img" data-item="39"></div></div>
      </div>
    </div>
  </div>
  <div style="display: none;" id="preload-items">
  </div>

  @if(getSettings('happyCoupon'))
    @include('template.cabinet.instaPromoPopup')
  @endif

  <script>
    @if(auth()->check() && auth()->id()==1)
    if(document.getElementById('instaPromo-success')){
      Fancybox.show(
        [
          {
            src: '#instaPromo-success'
          },
        ],
        {
          closeButton: false,
          loop: false,
          touch: false,
          contentClick: false,
          dragToClose: false,
        }
      );
    }
    @endif
    window.hpRoutes = {
      opened: @json(route('happy_coupon.opened', $order->slug)),
      open: @json(route('happy_coupon.open', $order->slug)),
      cabinet: @json(route('cabinet.order.index')),
    }
    window.animation = @json($animation);
  </script>
</x-light-layout>
