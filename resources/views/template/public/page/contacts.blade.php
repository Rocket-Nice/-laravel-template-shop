@section('title', $seo['title'] ?? config('app.name'))
<x-app-layout>

  @include('_parts.public.pageTopBlock')
  <x-slot name="script">
    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey=c7744407-82c1-4c00-a488-4ae90d1e64ef" type="text/javascript"></script>
  </x-slot>
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 pt-6 sm:py-10 md:py-14 lg:py-16 xl:py-[86px]">
    <div class="md:flex md:flex-row">
      <div class="md:max-w-[42.5304878%] w-full mx-auto flex flex-col">
        <div id="contactsSwiper" class="swiper flex-1 w-full h-full">
          <div class="swiper-wrapper">
            @if(isset($content->carousel_data['contactsSwiper']))
              @foreach($content->carousel_data['contactsSwiper'] as $key => $slide)
                @if(($slide['headline'] ?? '') == 'Социальные сети')
                  @continue
                @endif
                <div class="swiper-slide relative">
                  <input type="hidden" data-id="contactsSwiper-{{ $key }}" class="json-image" value="{{ e(json_encode($slide['image']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block w-full h-full object-cover">
                  @if(isset($slide['text'])&&$slide['text'])
                  <div class="absolute bottom-6 left-2 md:left-4 right-2 md:right-4 bg-white bg-opacity-40 backdrop-blur text-center py-[15px]">
                    <div class="mb-4 sm:mb-6 md:mb-9 text-center">
                      <h3 class="d-headline-4 m-headline-3 lh-outline-none leading-none">{{ $slide['headline'] ?? '' }}</h3>
                    </div>
                    <div class="d-text-body m-text-body p-4 space-y-2.5 lh-outline-none link-underline">
                      {!! $slide['text'] ? wrapNumbers($slide['text']) : '' !!}
                    </div>
                  </div>
                  @endif
                </div>
              @endforeach
            @endif
          </div>
          <div class="swiper-button-next backdrop-blur-sm"></div>
          <div class="swiper-button-prev backdrop-blur-sm"></div>
          <div class="swiper-pagination !bottom-2"></div>
        </div>
      </div>
      <div class="md:pl-14 lg:pl-16 xl:pl-[86px] flex-1">
        <div class="text-center md:text-left pt-12 md:pt-0">
          <h1 class="headline-1 text-myBrown">LE MOUSSE</h1>
          <div class="border-t border-myBrown my-6 w-[135px] mx-auto md:mx-0"></div>
          <div>
            <div class="text-center m-text-body d-text-body">
              @php($tg = \App\Models\Setting::where('key', 'tg_support')->first()->value)
              Техническая поддержка: <a href="https://{{ $tg }}">{{ $tg }}</a>
            </div>
{{--            <form action="">--}}
{{--              <div class="d-text-body m-text-body mb-6">--}}
{{--                Есть вопросы или предложения? С удовольствием Вам ответим--}}
{{--              </div>--}}
{{--              <div class="mb-6">--}}
{{--                <x-public.text-input type="text" placeholder="Ваше имя" />--}}
{{--              </div>--}}
{{--              <div class="mb-6">--}}
{{--                <x-public.text-input type="text" placeholder="E-mail адрес" />--}}
{{--              </div>--}}
{{--              <div class="mb-6">--}}
{{--                <x-public.text-input type="text" placeholder="Тема" />--}}
{{--              </div>--}}
{{--              <div class="mb-6">--}}
{{--                <x-public.textarea type="text"/>--}}
{{--              </div>--}}
{{--              <div class="text-center">--}}
{{--                <x-public.primary-button type="submit" class="md:h-14 md:w-full md:max-w-[285px] mx-auto">Отправить</x-public.primary-button>--}}
{{--              </div>--}}
{{--            </form>--}}
          </div>
        </div>
      </div>
    </div>

{{--    @if(isset($content->carousel_data['ourStores']))--}}
{{--    <div id="stores" class="toggle-wrapper mb-2 md:mb-4 bg-myGreen mt-12">--}}
{{--      <div class="toggle-button p-4 cursor-pointer flex justify-center items-center">--}}
{{--        <span class="d-headline-4 m-headline-3 lh-outline-none">Наши магазины</span>--}}
{{--        <svg width="17" height="11" class="ml-2" viewBox="0 0 17 11" fill="none" xmlns="http://www.w3.org/2000/svg">--}}
{{--          <g filter="url(#filter0_b_1943_23515)">--}}
{{--            <path d="M16.5 2.23962L8.5 10.166L0.5 2.23962L1.92 0.832683L8.5 7.35214L15.08 0.832682L16.5 2.23962Z"--}}
{{--                  fill="black"/>--}}
{{--          </g>--}}
{{--          <defs>--}}
{{--            <filter id="filter0_b_1943_23515" x="-31.5" y="-31.168" width="80" height="73.334"--}}
{{--                    filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">--}}
{{--              <feFlood flood-opacity="0" result="BackgroundImageFix"/>--}}
{{--              <feGaussianBlur in="BackgroundImageFix" stdDeviation="16"/>--}}
{{--              <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_1943_23515"/>--}}
{{--              <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_1943_23515" result="shape"/>--}}
{{--            </filter>--}}
{{--          </defs>--}}
{{--        </svg>--}}
{{--      </div>--}}
{{--      <div class="toggle-content overflow-hidden transition-max-height duration-500 h-0 mx-auto d-text-body m-text-body px-2 md:px-6">--}}
{{--        <div class="py-4 md:py-6 space-y-9 md:space-y-12">--}}
{{--          @foreach($content->carousel_data['ourStores'] as $key => $store)--}}
{{--            <div class="findNumbers">--}}
{{--              {!! $store['text'] ? wrapNumbers($store['text']) : '' !!}--}}
{{--              @php($latlon=explode(',', $store['latlon']))--}}
{{--              <p class="link-underline"><a href="#" data-map="" data-latitude="{{ $latlon[0] }}" data-longitude="{{ $latlon[1] }}">Найти на карте</a></p>--}}
{{--            </div>--}}
{{--          @endforeach--}}
{{--        </div>--}}
{{--      </div>--}}
{{--    </div>--}}
{{--    @endif--}}
  </div>

  <script>
    new Swiper('#contactsSwiper', {
      slidesPerView: 1,
      preloadImages: false,
      lazy: false,
      cssMode: true,
      mousewheel: true,
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
      pagination: {
        el: ".swiper-pagination",
      },
    });
  </script>
</x-app-layout>
