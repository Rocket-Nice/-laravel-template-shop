@section('title', $seo['title'] ?? config('app.name'))
<x-app-layout>
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 !pt-0 py-6 sm:py-10 md:py-14 lg:py-16 xl:py-[86px]">
    <div class="lg:flex lg:flex-row-reverse">
      {{--      @if(isset($product->style_page['mainVideo']['mp4']))--}}
      {{--        <div class="-mx-2 sm:-mx-4 md:-mx-8 lg:mx-auto mb-6 lg:mb-0 lg:w-full sm:w-7/12 md:w-5/12 flex-1 relative" id="productSlider">--}}
      {{--          <video id="mainVideo" preload="none" autoplay loop muted playsinline class="lg:absolute lg:left-0 lg:top-0 w-full h-full block object-cover">--}}
      {{--                        <source src="{{ $product->style_page['mainVideo']['mp4'] }}" type="video/quicktime">--}}
      {{--            <source src="{{ $product->style_page['mainVideo']['mp4'] }}" type="video/mp4">--}}
      {{--            @if(isset($product->style_page['mainImage']['image']))--}}
      {{--              <div class="">--}}
      {{--                <input type="hidden" data-id="mainImage" class="json-image"--}}
      {{--                       value="{{ e(json_encode($product->style_page['mainImage']['image'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="lg:absolute lg:left-0 lg:top-0 w-full h-full block object-cover" data-img-class="block w-full">--}}
      {{--              </div>--}}
      {{--            @endif--}}
      {{--          </video>--}}
      {{--          <script>--}}
      {{--            document.addEventListener("DOMContentLoaded", function() {--}}
      {{--              const videoElement = document.getElementById('mainVideo');--}}
      {{--              videoElement.load();--}}
      {{--            });--}}
      {{--          </script>--}}
      {{--        </div>--}}
      {{--      @elseif(isset($product->style_page['mainImage']['image']))--}}
      {{--        <div class="mb-6 lg:mb-0 w-full mx-auto sm:w-7/12 md:w-5/12 flex-1 relative" id="productSlider">--}}
      {{--          @if(!$product->getStock())--}}
      {{--            <div class="text-lg uppercase bg-white absolute left-0 top-4 py-1 px-2 z-10">sold out</div>--}}
      {{--          @endif--}}
      {{--          <input type="hidden" data-id="mainImage" class="json-image"--}}
      {{--                 value="{{ e(json_encode($product->style_page['mainImage']['image'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="lg:absolute lg:left-0 lg:top-0 w-full h-full block object-cover" data-img-class="block w-full">--}}
      {{--        </div>--}}
      {{--      @endif--}}
      <div class="-mx-2 sm:-mx-4 md:-mx-8 lg:mx-auto mb-6 lg:mb-0 lg:w-full flex-1 relative lg:max-w-[600px]" id="productSlider">
        @if(!$product->getStock() && !($product->options['soon'] ?? ''))
          <div class="text-lg uppercase bg-white absolute left-0 top-4 py-1 px-2 z-10">sold out</div>
        @endif
        @if($product->gold_coupon)
          <div class="absolute top-2 right-2 z-10 flex-1 flex justify-end" >
            <img src="{{ asset('img/gold-ticket.png') }}" alt="" style="width: 27.37430168%;">
          </div>
        @endif
        @if(isset($product->style_page['mainVideo']['mp4']) && !($product->style_page['hide_video'] ?? false))
          <video id="mainVideo" preload="none" autoplay loop muted playsinline class="lg:absolute lg:left-0 lg:top-0 w-full h-full block object-cover">
            <source src="{{ $product->style_page['mainVideo']['mp4'] }}" type="video/quicktime">
            <source src="{{ $product->style_page['mainVideo']['mp4'] }}" type="video/mp4">
            @if(isset($product->style_page['mainImage']['image']))
              <div class="">
                <input type="hidden" data-id="mainImage" class="json-image"
                       value="{{ e(json_encode($product->style_page['mainImage']['image'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="lg:absolute lg:left-0 lg:top-0 w-full h-full block object-cover" data-img-class="block w-full">
              </div>
            @endif
          </video>
          <script>
            document.addEventListener("DOMContentLoaded", function() {
              const videoElement = document.getElementById('mainVideo');
              videoElement.load();
            });
          </script>
        @elseif(isset($product->style_page['mainImage']['image']))
          <input type="hidden" data-id="mainImage" class="json-image"
                 value="{{ e(json_encode($product->style_page['mainImage']['image'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="w-full block object-cover" data-img-class="block w-full">
{{--        <input type="hidden" data-id="mainImage" class="json-image"--}}
{{--                 value="{{ e(json_encode($product->style_page['mainImage']['image'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="lg:absolute lg:left-0 lg:top-0 w-full h-full block object-cover" data-img-class="block w-full">--}}
{{--        --}}
          @endif
      </div>
      <div class="md:pt-12 lg:pr-12 lg:w-7/12 flex-1">
        @if(getSettings('happyCoupon') && false && isset($product->options['viewers']) && $product->getStock())
          <div class="text-sm md:text-lg text-right cormorantInfant mb-2.5 sm:mb-0" style="color: #ABA7A7">Сейчас просматривает {{ denum($product->options['viewers'], ['%d человек','%d человека','%d человек']) }}</div>
        @endif
        <div class="flex justify-between items-start space-x-5">
          <div>
            <h1 class="headline-3b">{{ $product->name }}</h1>
            @if(isset($product->style_page['subtitle-page'])&&$product->style_page['subtitle-page']!='null')
              <div class="text-myBrown d-text-body mt-1">{!! $product->style_page['subtitle-page'] !!}</div>
            @endif
            @if(isset($product->options['only_pickup'])&&$product->options['only_pickup'])
              <p class="text-myRed text-sm md:text-md lg:text-lg mt-1 !leading-none">Доступен только для самовывоза г. Волгоград</p>
            @endif
          </div>

          <div data-da=".mobile-price,1,1024"
               class="text-xl sm:text-2xl md:text-3xl lg:text-4xl xl:text-5xl font-medium lh-base cormorantInfant whitespace-nowrap">
            @if($product->old_price)
              <div class="text-myBrown line-through">{!! formatPrice($product->old_price, true, '₽') !!}</div>
            @endif
            <div>{!! formatPrice($product->price, true, '₽') !!}</div>
          </div>
        </div>
        <div class="mt-5 md:mt-6">
          <div class="flex justify-between items-center">
            <div class="flex-1 w-full max-w-[54%] lg:max-w-full">
              {{--              @if(getSettings('puzzlesStatus') && ($product->options['puzzles'] ?? false) && isset($product->options['puzzles_count']) && is_numeric($product->options['puzzles_count']))--}}
              {{--                <div class="flex items-center text-myGreen2 text-md text-myBrown">--}}
              {{--                  <x-public.puzzle-svg style="width: 18px;"/>--}}
              {{--                  <span class="ml-1.5">{{ $product->options['puzzles_count'] }}</span>--}}
              {{--                </div>--}}
              {{--              @endif--}}
              @if(getSettings('happyCoupon') && false && $product->getStock() && $product->stockStatus)
                <div class="text-lg mt-2 mb-4" style="color: {{ $product->stockStatus['color'] }}">
                  <div class="cormorantInfant">{!! $product->stockStatus['text'] ?? "&nbsp;" !!}</div>
                  <div>
                    <svg style="width: 100%;" height="4" viewBox="0 0 100% 4" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <rect width="100%" height="4" fill="#B2B2B2" fill-opacity="0.25"/>
                      <rect width="{{ $product->stockStatus['percent'].'%' }}" height="4" fill="currentColor"/>
                    </svg>
                  </div>
                </div>
              @endif
              {{--              <div class="flex items-center">--}}
              {{--                <svg width="30" height="28" viewBox="0 0 30 28" fill="none" xmlns="http://www.w3.org/2000/svg">--}}
              {{--                  <path--}}
              {{--                    d="M15 0L18.3677 10.3647H29.2658L20.4491 16.7705L23.8168 27.1353L15 20.7295L6.18322 27.1353L9.55093 16.7705L0.734152 10.3647H11.6323L15 0Z"--}}
              {{--                    fill="#B1908E"/>--}}
              {{--                </svg>--}}
              {{--                <div class="ml-4 cormorantInfant font-medium text-2xl md:text-32 lg-none">{!! $product->getRating() > 0 ? $product->getRating() : '<span class="cormorantGaramond" style="opacity:0.5"></span>' !!}</div>--}}
              {{--              </div>--}}
              {{--              <div class="h-1.5 w-full mt-2 sm:mt-4 md:mt-6 relative"--}}
              {{--                   style="background-color: rgba(178, 178, 178, 0.25);">--}}
              {{--                <div class="absolute h-full left-0 top-0 bg-myBrown" style="width: {{ $product->getRating()/5*100 }}%"></div>--}}
              {{--              </div>--}}
            </div>
            <div class="mobile-price relative">

            </div>
          </div>

        </div>
        {{--        опции--}}
        <style>
          /* Дополнительные стили для скрытия checkbox и изменения вида label при выборе */
          .size-checkbox:checked + label {
            color: black; /* Черный цвет текста при выборе */
          }

          .size-checkbox + label {
            color: rgba(0, 0, 0, 0.5); /* Полупрозрачный текст по умолчанию */
            cursor: pointer;
          }

          .size-checkbox {
            display: none; /* Скрываем checkbox */
          }
        </style>
        @if(isset($product->product_options['productSize']))
          <div class="flex justify-between my-5 md:my-12">
            <div class="flex space-x-9 md:space-x-12 items-center">
              @foreach($product->product_options['productSize'] as $size)
                @php
                  $optionProduct = $product->optionProducts()->where('id', $size['product'])->first();
                @endphp
                @if($optionProduct->getStock())
                  <div>
                    <input type="radio" id="size-{{ $size['name'] }}" class="size-checkbox product-option" name="size" value="{{ $size['product'] }}" data-button=".toCart[data-id='{{ $product->id }}']"/>
                    <label for="size-{{ $size['name'] }}" class="font-medium text-2xl md:text-3xl">{{ $size['name'] }}</label>
                  </div>
                @else
                  <div style="opacity: 0.5;pointer-events: none">
                    <input type="radio" id="size-{{ $size['name'] }}" class="size-checkbox product-option" name="size" disabled/>
                    <label for="size-{{ $size['name'] }}" class="font-medium text-2xl md:text-3xl">{{ $size['name'] }}</label>
                  </div>
                @endif
              @endforeach
            </div>
          </div>
        @endif
        <div class="mobile-add-cart"></div>
        <div class="bg-myGreen mt-5 sm:mt-6 md:mt-8 lg:mt-12 p-2 sm:p-4 md:py-6 md:px-6 txt-body">
          @if(isset($product->style_page['age'])&&!empty($product->style_page['age']))
            <div class="flex justify-center">
              <div class="cormorantInfant border border-px border-black rounded-full flex justify-center items-center text-center text-xl mb-6" style="width: 42px;height: 42px;">
                {{ $product->style_page['age'].'+' }}
              </div>
            </div>
          @endif
          <div class="relative overflow-hidden transition-all duration-500 ease-in-out collapsibleBlock"
               data-button-id="toggleButton" data-symbols="360" id="collapsibleBlock">
            @if(isset($product->style_page['sostav-nabora']))
              <h4 class="d-headline-4 m-headline-3 mb-4 text-center">Состав набора:</h4>
              <div class="mb-8">
                {!! $product->style_page['sostav-nabora'] ?? '' !!}
              </div>
            @endif
            @if(isset($product->style_page['podarok']))
              <h4 class="d-headline-4 m-headline-3 mb-4 text-center">Подарок:</h4>
              <div class="mb-8">
                {!! $product->style_page['podarok'] ?? '' !!}
              </div>
            @endif
            {!! $product->style_page['description'] ?? '' !!}
            <div class="mt-8 sm:mt-12">
              @if(isset($product->style_page['features']))
                <h4 class="d-headline-4 m-headline-3 lh-none lh-outline-none text-center">Особенности продукта</h4>
                <div class="border-t-2 border-myGreen2 mt-5 mx-auto w-[200px] mb-8 sm:mb-12"></div>
                {!! $product->style_page['features'] ?? '' !!}
              @endif
            </div>
          </div>
          <div class="text-center mt-4">
            <button class="text-base md:text-lg lg:text-xl font-semibold flex items-center mx-auto" id="toggleButton" data-open-text="Развернуть" data-close-text="Свернуть">
              <span class="text">Развернуть</span>
              <svg width="16" height="16" class="ml-2" viewBox="0 0 16 16" fill="none"
                   xmlns="http://www.w3.org/2000/svg">
                <g filter="url(#filter0_b_1918_1487)">
                  <path d="M3 5.96202L8 10.916L13 5.96202L12.1125 5.08268L8 9.15735L3.8875 5.08268L3 5.96202Z"
                        fill="#000"/>
                </g>
                <defs>
                  <filter id="filter0_b_1918_1487" x="-32" y="-32" width="80" height="80" filterUnits="userSpaceOnUse"
                          color-interpolation-filters="sRGB">
                    <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                    <feGaussianBlur in="BackgroundImageFix" stdDeviation="16"/>
                    <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_1918_1487"/>
                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_1918_1487" result="shape"/>
                  </filter>
                </defs>
              </svg>
            </button>
          </div>
        </div>
        <div class="mt-5 sm:mt-8 md:mt-10 lg:mt-12 flex flex-wrap items-center justify-between" data-da=".mobile-add-cart,1,1024">
          @if($product->getStock())
            <div
              class="flex justify-between items-center border border-black border-1 w-[98px] sm:w-[128px] lg:w-[164px] h-11 md:h-14">
              <button id="subQty" class="bg-transparent border-0 outline-none p-3"  data-field="productQty">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M3.5 7H10.5" stroke="#2C2E35" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round"/>
                </svg>
              </button>
              <input type="text" id="productQty"
                     class="p-0 bg-transparent numeric-field border-0 outline-none max-w-full w-full flex-1 focus:ring-0 focus:border-none hover:border-none outline-none cormorantInfant text-xl md:text-2xl font-medium text-center"
                     placeholder="1">
              <button id="addQty" class="bg-transparent border-0 outline-none p-3"  data-field="productQty">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M3.5 7H10.5" stroke="#2C2E35" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round"/>
                  <path d="M7 10.5V3.5" stroke="#2C2E35" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round"/>
                </svg>
              </button>
            </div>
            @if($product->preorder)
              <x-public.primary-button href="javascript:;" data-fancybox data-src="#preorder-{{ $product->id }}" class="ml-2 w-full md:max-w-[276px] flex-1 h-11 md:h-14">Оформить предзаказ</x-public.primary-button>
              <div id="preorder-{{ $product->id }}" class="d-text-body m-text-body p-2 sm:p-4 max-w-3xl" style="display: none;">
                <div class="mb-4">
                  <h3 class="d-headline-4 m-headline-3">Внимание</h3>
                  <p>Ожидаемая отправка с 5 по 17 декабря<br/>После отгрузки товара, статус вашего заказа изменится в личном кабинете</p>
                </div>
                <x-public.primary-button href="#" class="ml-2 w-full md:max-w-[276px] flex-1 h-11 md:h-14 toCart" data-id="{{ $product->id }}" data-qty-id="productQty">Подтверждаю заказ</x-public.primary-button>
              </div>
            @else
              <x-public.primary-button href="#" class="ml-2 w-full md:max-w-[276px] flex-1 h-11 md:h-14 toCart" data-id="{{ $product->id }}" data-qty-id="productQty">В корзину</x-public.primary-button>
            @endif
          @else
            <div></div>
            <div class="ml-2 w-full md:max-w-[276px] text-center text-xl bg-gray-200 text-gray-500 h-11 md:h-14 flex justify-center items-center">
              @if(auth()->check())
                @if(!auth()->user()->isWaitingProduct($product->id))
                  <button onclick="window.productNotification(this, '{{ $product->slug }}', 'set')">Узнать о поступлении</button>
                @else
                  <button onclick="window.productNotification(this, '{{ $product->slug }}', 'remove')">Сообщим о поступлении</button>
                @endif
              @else
                <a href="javascript:;" class="outline-none" data-src="#authForm" onclick="window.productNotificationBeforeAuth('{{ $product->slug }}')" data-fancybox-no-close-btn>Узнать о поступлении</a>
              @endif
            </div>
          @endif
          @if($product->refill)
            <div x-data="{ open: false}" x-show="!open" @click="open = true;$dispatch('refill')" class="w-full md:max-w-[276px] mt-3 md:mt-0">
              <x-public.primary-button class="w-full flex-1 h-11 md:h-14">Добавить refill</x-public.primary-button>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
  @if($product->refill)

    @php
      $refill = $product->refill;
    @endphp
    <div x-data="{ open: false }" x-show="open" @refill.window="open = true" data-da=".mobile-add-cart,last,1024" class="lg:px-14 xl:px-16 py-6">
      <div class="w-full border border-myDark p-2">
        <div class="lg:flex lg:flex-row-reverse">
          <div class="mb-6 lg:mb-0 w-full mx-auto sm:w-7/12 md:w-5/12 flex-1 relative">
            @if(isset($refill->style_page['mainImage']['image']))
              <div class="item-square refill-img">
                <input type="hidden" data-id="mainImage" class="json-image"
                       value="{{ e(json_encode($refill->style_page['mainImage']['image'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="lg:absolute lg:left-0 lg:top-0 w-full h-full block object-cover" data-img-class="block w-full object-cover">
              </div>
            @endif
          </div>
          <div class="md:pt-12 lg:pr-12 lg:w-7/12 flex flex-col justify-between">
            <div>
              <div>
                <h1 class="headline-3b">{{ $refill->name }}</h1>
              </div>
              <div class="flex items-center justify-end md:justify-start space-x-2 sm:space-x-4 md:space-x-6 text-xl sm:text-2xl mt-4 lh-base whitespace-nowrap">
                @if($refill->volume)
                  <div class="italic font-infant_font mr-2" style="font-size: .75em;">{{ $refill->volume }}</div>
                @endif
                @if($refill->old_price)
                  <div class="text-myBrown line-through font-medium">{!! formatPrice($refill->old_price, true, '₽') !!}</div>
                @endif
                <div class="font-medium">{!! formatPrice($refill->price, true, '₽') !!}</div>
              </div>
            </div>
            <div x-data="{ count: 1 }" class="mt-5 sm:mt-8 md:mt-10 lg:mt-12 flex flex-wrap items-center justify-between">
              <div class="flex justify-between items-center border border-black border-1 w-[98px] sm:w-[128px] lg:w-[164px] h-11 md:h-14">
                <button @click="count > 1 ? count -= 1 : 1" class="bg-transparent border-0 outline-none p-3"  data-field="productQty">
                  <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3.5 7H10.5" stroke="#2C2E35" stroke-width="1.5" stroke-linecap="round"
                          stroke-linejoin="round"/>
                  </svg>
                </button>
                <input type="text" id="refillQty"
                       class="p-0 bg-transparent numeric-field border-0 outline-none max-w-full w-full flex-1 focus:ring-0 focus:border-none hover:border-none outline-none cormorantInfant text-xl md:text-2xl font-medium text-center"
                       placeholder="1" :value="count">
                <button @click="count += 1" class="bg-transparent border-0 outline-none p-3"  data-field="productQty">
                  <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3.5 7H10.5" stroke="#2C2E35" stroke-width="1.5" stroke-linecap="round"
                          stroke-linejoin="round"/>
                    <path d="M7 10.5V3.5" stroke="#2C2E35" stroke-width="1.5" stroke-linecap="round"
                          stroke-linejoin="round"/>
                  </svg>
                </button>
              </div>
              <x-public.primary-button href="#" class="ml-2 w-full md:max-w-[276px] flex-1 h-11 md:h-14 toCart" data-id="{{ $refill->id }}" data-qty-id="refillQty">В корзину</x-public.primary-button>
            </div>
          </div>
        </div>
      </div>
    </div>
  @endif
  @if($product->style_page['activeComponentsText'] ?? false)
    <div id="activeComponents-block" class="fixed right-0 top-0 left-0 bottom-0 transform translate-x-full transition-transform duration-300 w-screen h-screen flex justify-end items-start bg-myDark  bg-opacity-60 z-30">
      <button type="button" id="activeComponents-close" class="activeComponentsBtn mt-20 h-[48px] w-[52px] md:h-[80px] md:w-[71px] flex items-center justify-end pr-0 md:pr-2 translate-x-px">
        <svg class="hidden md:block rotate-180" width="41" height="26" viewBox="0 0 41 26" fill="none" xmlns="http://www.w3.org/2000/svg">
          <g filter="url(#filter0_b_1943_38321)">
            <path d="M33.2964 1.12604e-06L20.416 13L33.2964 26L35.5827 23.6925L24.9886 13L35.5827 2.3075L33.2964 1.12604e-06Z" fill="#6C715C"/>
          </g>
          <g opacity="0.48" filter="url(#filter1_b_1943_38321)">
            <path d="M18.2964 1.12604e-06L5.41602 13L18.2964 26L20.5827 23.6925L9.98856 13L20.5827 2.3075L18.2964 1.12604e-06Z" fill="#6C715C"/>
          </g>
          <defs>
            <filter id="filter0_b_1943_38321" x="-17" y="-32" width="90" height="90" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
              <feFlood flood-opacity="0" result="BackgroundImageFix"/>
              <feGaussianBlur in="BackgroundImageFix" stdDeviation="16"/>
              <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_1943_38321"/>
              <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_1943_38321" result="shape"/>
            </filter>
            <filter id="filter1_b_1943_38321" x="-32" y="-32" width="90" height="90" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
              <feFlood flood-opacity="0" result="BackgroundImageFix"/>
              <feGaussianBlur in="BackgroundImageFix" stdDeviation="16"/>
              <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_1943_38321"/>
              <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_1943_38321" result="shape"/>
            </filter>
          </defs>
        </svg>
        <svg class="md:hidden block rotate-180 mr-1.5" width="23" height="16" viewBox="0 0 23 16" fill="none" xmlns="http://www.w3.org/2000/svg">
          <g filter="url(#filter0_b_2768_5829)">
            <path d="M17.038 13L12.084 8L17.038 3L17.9173 3.8875L13.8427 8L17.9173 12.1125L17.038 13Z" fill="#6C715C"/>
          </g>
          <g opacity="0.48" filter="url(#filter1_b_2768_5829)">
            <path d="M10.038 13L5.08398 8L10.038 3L10.9173 3.8875L6.84265 8L10.9173 12.1125L10.038 13Z" fill="#6C715C"/>
          </g>
          <defs>
            <filter id="filter0_b_2768_5829" x="-25" y="-32" width="80" height="80" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
              <feFlood flood-opacity="0" result="BackgroundImageFix"/>
              <feGaussianBlur in="BackgroundImageFix" stdDeviation="16"/>
              <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_2768_5829"/>
              <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_2768_5829" result="shape"/>
            </filter>
            <filter id="filter1_b_2768_5829" x="-32" y="-32" width="80" height="80" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
              <feFlood flood-opacity="0" result="BackgroundImageFix"/>
              <feGaussianBlur in="BackgroundImageFix" stdDeviation="16"/>
              <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_2768_5829"/>
              <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_2768_5829" result="shape"/>
            </filter>
          </defs>
        </svg>
      </button>
      <div id="activeComponents-text" class="w-full max-w-[926px] min-h-screen bg-myGreen overflow-y-auto h-screen px-2 sm:px-4 md:px-6 py-6 sm:py-10 md:py-12 relative overflow-y-auto">
        <h3 class="d-headline-4 m-headline-3 lh-outline-none text-center">Активные компоненты</h3>
        <div class="border-t border-t-2 border-myGreen2 mt-5 mb-8 mx-auto w-[200px]"></div>
        <div class="space-y-5 text-17 sm:text-xl md:text-2xl">
          {!! $product->style_page['activeComponentsText'] ?? '' !!}
        </div>
      </div>
    </div>
  @endif

  <div class="py-6 lg:py-12 px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 relative">
    @if($product->style_page['activeComponentsText'])
      <button type="button" id="activeComponents" class="activeComponentsBtn absolute right-0 top-1/2 -translate-y-1/2 h-[48px] w-[52px] md:h-[80px] md:w-[71px] flex items-center justify-center">
        <svg class="hidden md:block" width="41" height="26" viewBox="0 0 41 26" fill="none" xmlns="http://www.w3.org/2000/svg">
          <g filter="url(#filter0_b_1943_38321)">
            <path d="M33.2964 1.12604e-06L20.416 13L33.2964 26L35.5827 23.6925L24.9886 13L35.5827 2.3075L33.2964 1.12604e-06Z" fill="#6C715C"/>
          </g>
          <g opacity="0.48" filter="url(#filter1_b_1943_38321)">
            <path d="M18.2964 1.12604e-06L5.41602 13L18.2964 26L20.5827 23.6925L9.98856 13L20.5827 2.3075L18.2964 1.12604e-06Z" fill="#6C715C"/>
          </g>
          <defs>
            <filter id="filter0_b_1943_38321" x="-17" y="-32" width="90" height="90" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
              <feFlood flood-opacity="0" result="BackgroundImageFix"/>
              <feGaussianBlur in="BackgroundImageFix" stdDeviation="16"/>
              <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_1943_38321"/>
              <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_1943_38321" result="shape"/>
            </filter>
            <filter id="filter1_b_1943_38321" x="-32" y="-32" width="90" height="90" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
              <feFlood flood-opacity="0" result="BackgroundImageFix"/>
              <feGaussianBlur in="BackgroundImageFix" stdDeviation="16"/>
              <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_1943_38321"/>
              <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_1943_38321" result="shape"/>
            </filter>
          </defs>
        </svg>
        <svg class="md:hidden block" width="23" height="16" viewBox="0 0 23 16" fill="none" xmlns="http://www.w3.org/2000/svg">
          <g filter="url(#filter0_b_2768_5829)">
            <path d="M17.038 13L12.084 8L17.038 3L17.9173 3.8875L13.8427 8L17.9173 12.1125L17.038 13Z" fill="#6C715C"/>
          </g>
          <g opacity="0.48" filter="url(#filter1_b_2768_5829)">
            <path d="M10.038 13L5.08398 8L10.038 3L10.9173 3.8875L6.84265 8L10.9173 12.1125L10.038 13Z" fill="#6C715C"/>
          </g>
          <defs>
            <filter id="filter0_b_2768_5829" x="-25" y="-32" width="80" height="80" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
              <feFlood flood-opacity="0" result="BackgroundImageFix"/>
              <feGaussianBlur in="BackgroundImageFix" stdDeviation="16"/>
              <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_2768_5829"/>
              <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_2768_5829" result="shape"/>
            </filter>
            <filter id="filter1_b_2768_5829" x="-32" y="-32" width="80" height="80" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
              <feFlood flood-opacity="0" result="BackgroundImageFix"/>
              <feGaussianBlur in="BackgroundImageFix" stdDeviation="16"/>
              <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_2768_5829"/>
              <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_2768_5829" result="shape"/>
            </filter>
          </defs>
        </svg>
      </button>
    @endif
    @if($product->style_page['activeComponentsPercentage'] ?? false)
      <div class="flex sm:justify-center">
        <div class="flex items-center justify-center">
          @php
            $svg = generateSVG($product->style_page['activeComponentsPercentage'], 151, 7, 'absolute left-0 top-0 w-[86px] h-[86px] lg:w-[151px] lg:h-[151px] observed', '#6C715C');
            $svg = '<div class="item-square w-[86px] lg:w-[151px]"><div class="flex justify-center items-center"><div class="headline-3 cormorantInfant">'.$product->style_page['activeComponentsPercentage'].'%</div>'.$svg.'</div></div>';
            echo $svg;
          @endphp
          <div class="ml-2 sm:ml-4 md:ml-8 lg:ml-12 sm:text-center text-myGreen2">
            <div class="lh-none font-semibold uppercase text-28 sm:text-3xl md:text-4xl lg:text-42">АКТИВНЫХ<br/>
              КОМПОНЕНТОВ
            </div>
            <div class="leading-1.6 text-lg md:text-xl lg:text-2xl">в составе</div>
          </div>
        </div>
      </div>
    @endif
  </div>
  @if(isset($product->style_page['sostavNaboraBlock']))
    <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 pb-6 sm:pb-10 md:pb-14 lg:pb-16 xl:pb-[86px]">
      <div class="py-6 md:py-9 lg:py-12 border-t border-b border-myGreen border-myGreen text-center">
        <div class="uppercase text-myBrown text-32 lg:text-42 lh-none font-semibold">{{ $product->style_page['sostavNaboraBlockTitle'] ?? 'ТИП КОЖИ' }}</div>
        @if(!isset($product->style_page['sostavNaboraBlockTitle'])||mb_strtoupper($product->style_page['sostavNaboraBlockTitle'])=='ТИП КОЖИ')
          <div class="text-lg md:text-xl lg:text-2xl font-medium text-myGreen2 mt-2 mb-6 md:mb-9 lg:mb-12">подходит для:</div>
        @else
          <div class="text-lg md:text-xl lg:text-2xl font-medium text-myGreen2 mt-2 mb-6 md:mb-9 lg:mb-12"></div>
        @endif
        <ul class="text-base md:text-lg lg:text-xl xl:text-2xl mb-6 md:mb-9 lg:mb-12">
          @foreach($product->style_page['sostavNaboraBlock'] as $item)
            <li>{!! $item['text'] !!}</li>
          @endforeach
        </ul>
        @if(isset($product->style_page['sostavNaboraBlockDescription']))
          <div class="text-base md:text-lg lg:text-xl xl:text-2xl">
            {!! $product->style_page['sostavNaboraBlockDescription'] !!}
          </div>
          {{--          <div class="lowercase italic font-medium md:font-normal text-base md:text-xl lg:text-2xl">{!! nl2br($product->style_page['sostavNaboraBlockItalic']) !!}</div>--}}
        @endif
      </div>
    </div>
  @endif
  @if($product->style_page['xenon'] ?? false)
    <div class="py-6 lg:py-12 px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 relative text-center">
      <img src="{{ asset('img/xenon.png') }}" alt="обогащено ксеноном" class="max-w-[148px] sm:max-w-[280px] block mx-auto mb-6">
      <a href="{{ route('page.xenon') }}" class="d-text-body m-text-body uppercase underline hover:no-underline">ОБОГАЩЕНО КСЕНОНОМ</a>
    </div>
  @endif
  @if($product->style_page['productEffect'] ?? false)
    <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-6 sm:py-10 md:py-14 lg:py-16 xl:py-[86px]">
      <div>
        <div class="flex justify-center items-center mb-8 md:mb-10 lg:mb-12">
          <div class="border-t border-b border-myBrown w-[124px]"></div>
          <h2 class="text-center product-headline text-myBrown mx-6 lh-base">Действие</h2>
          <div class="border-t border-b border-myBrown w-[124px]"></div>
        </div>
        <div class="relative">
          <div id="swiper-product-effect" class="swiper">
            <div class="swiper-wrapper">
              @foreach($product->style_page['productEffect'] as $item)
                <div class="swiper-slide max-w-[132px] md:max-w-[172px] lg:max-w-[210px]">
                  <div class="item-square rounded-full bg-myBeige w-[86px] mx-auto">
                    <div class="flex items-center justify-center">
                      <img src="{{ $item['icon'] }}" alt="" class="w-16">
                    </div>
                  </div>
                  <div class="mt-4 text-base md:text-xl lg:text-2xl lg-none font-semibold text-center">{{ $item['text'] }}
                  </div>
                </div>
              @endforeach
            </div>
            @if(count($product->style_page['productEffect']) > 3)
              <style>
                @keyframes move-left-right {
                  0%, 100% {
                    transform: translateX(0);
                  }
                  50% {
                    transform: translateX(4px);
                  }
                }
                .swiper-button-action-prev, .swiper-button-action-next {
                  animation: move-left-right 1.5s ease-in-out infinite;
                }
              </style>
              <div class="swiper-button-action-prev swiper-button-prev swiper-buttom-outside"></div>
              <div class="swiper-button-action-next swiper-button-next swiper-buttom-outside"></div>
            @endif


          </div>
        </div>
      </div>
    </div>
    <script>
      new Swiper('#swiper-product-effect', {
        slidesPerView: 'auto',
        spaceBetween: 8,
        mousewheel: {
          releaseOnEdges: true
        },
        freeMode: true,
        @if(count($product->style_page['productEffect']) > 3)
        navigation: {
          nextEl: ".swiper-button-action-next",
          prevEl: ".swiper-button-action-prev",
        },
        @endif
        breakpoints: {
          768: {
            spaceBetween: 16,
          },
          1024: {
            spaceBetween: 24,
          },
        },
      });

    </script>
  @endif


  @if(isset($product->style_page['celebrities']))
    <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16">
      <div class="uppercase text-myGray text-28 md:text-4xl lg:text-5xl md:text-center lh-none font-semibold mb-4">Бренд, который<br/>выбирают звезды</div>
      <div class="mx-auto max-w-[460px]">
        <div id="swiper-celebrities" class="swiper">
          <div class="swiper-wrapper">
            @foreach($product->style_page['celebrities'] as $image_key => $slide)
              @if(!isset($slide['image']['size']))
                @continue
              @endif
              <div class="swiper-slide">
                <div class="relative">
                  @isset($slide['name'])
                    <div class="absolute left-6 top-6 text-base z-10 font-semibold lh-none">{!! str_replace(' ', '<br/>', trim($slide['name'])) !!}</div>
                  @endisset
                  <div class="item-square square-1.30">
                    <input type="hidden" data-id="ourAwards-{{ $image_key }}-{{ $image_key }}" class="json-image"
                           value="{{ e(json_encode($slide['image']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="swiper-image block object-cover">
                  </div>
                </div>

              </div>
            @endforeach
          </div>
          <div class="swiper-pagination"></div>
        </div>
      </div>
    </div>
    <script>
      new Swiper('#swiper-celebrities', {
        // // Optional parameters
        // direction: 'vertical',
        // loop: true,
        slidesPerView: "auto",
        preloadImages: false,
        lazy: false,
        cssMode: true,
        mousewheel: true,
        pagination: {
          el: ".swiper-pagination",
          clickable: true,
        },
      });
    </script>
  @endif
  @if(isset($product->style_page['typeOfSkin']))
    <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-6 sm:py-10 md:py-14 lg:py-16 xl:py-[86px]">
      <div class="py-6 md:py-9 lg:py-12 border-t border-b border-myGreen border-myGreen text-center">
        <div class="uppercase text-myBrown text-32 lg:text-42 lh-none font-semibold">{{ $product->style_page['typeOfSkinTitle'] ?? 'ТИП КОЖИ' }}</div>
        @if(!isset($product->style_page['typeOfSkinTitle'])||mb_strtoupper($product->style_page['typeOfSkinTitle'])=='ТИП КОЖИ')
          <div class="text-lg md:text-xl lg:text-2xl font-medium text-myGreen2 mt-2 mb-6 md:mb-9 lg:mb-12">подходит для:</div>
        @else
          <div class="text-lg md:text-xl lg:text-2xl font-medium text-myGreen2 mt-2 mb-6 md:mb-9 lg:mb-12"></div>
        @endif
        <ul class="list-disc list-inside headline-4 !leading-1.6 mb-6 md:mb-9 lg:mb-12">
          @foreach($product->style_page['typeOfSkin'] as $item)
            <li>{{ $item['text'] }}</li>
          @endforeach
        </ul>
        @if(isset($product->style_page['typeOfSkinItalic']))
          <div class="lowercase italic font-medium md:font-normal text-base md:text-xl lg:text-2xl">{!! nl2br($product->style_page['typeOfSkinItalic']) !!}</div>
        @endif
      </div>
    </div>
  @endif

  <div>
    @if($product->style_page['care_compatibility'] ?? false)
      <div class="toggle-wrapper bg-myGreen2 text-white mb-6 sm:mb-9 md:mb-12 lg:mb-16 xl:mb-[86px] px-2">
        <div class="toggle-button py-4 headline-4 !leading-1.6 flex justify-center text-center items-center">
          Комплексный уход<br/>
          и совместимость продуктов
          <svg width="17" height="11" class="ml-2" viewBox="0 0 17 11" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g filter="url(#filter0_b_1943_23515)">
              <path d="M16.5 2.23962L8.5 10.166L0.5 2.23962L1.92 0.832683L8.5 7.35214L15.08 0.832682L16.5 2.23962Z"
                    fill="white"/>
            </g>
            <defs>
              <filter id="filter0_b_1943_23515" x="-31.5" y="-31.168" width="80" height="73.334"
                      filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                <feGaussianBlur in="BackgroundImageFix" stdDeviation="16"/>
                <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_1943_23515"/>
                <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_1943_23515" result="shape"/>
              </filter>
            </defs>
          </svg>
        </div>
        <div
          class="toggle-content overflow-hidden transition-max-height duration-500 h-0 mx-auto max-w-[700px] d-text-body m-text-body px-2 md:px-0">
          <div class="py-4 md:py-6 space-y-2">
            {!! $product->style_page['care_compatibility'] !!}
          </div>
        </div>
      </div>

    @endif

    <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16">
      @if($product->style_cards)
        <div id="swiper-product-carousel" class="swiper swiper-centered mb-6 md:mb-9 lg:mb-12">
          <div class="swiper-wrapper">
            @foreach($product->style_cards as $key => $card)
              @if($key == '_request')
                @continue
              @endif
              <div class="swiper-slide product-item-swiper__slide">
                <div class="product-card_item">
                  <div class="product_{{ $card['card_style'] }} relative bg-slate-100 overflow-hidden">
                    <div class="card_fields z-10 absolute top-0 left-0 w-full @if($card['card_style'] == 'card-style-5') {{ $card['vertical-align'] }} @else {{ 'h-full' }} @endif" style="{{ $card['style'] ?? '' }}">
                      @if(isset($card['fields']))
                        @foreach($card['fields'] as $field)
                          <div class="cormorantGaramond lh-base product-description-item {{ $field['align'] ?? '' }} {{ $field['vertical-align'] ?? '' }}" style="{{ $field['style'] ?? '' }}">
                            {!! nl2br($field['text']) !!}
                          </div>
                        @endforeach
                      @endif
                      @if(isset($card['big-text']))
                        <div class="cormorantGaramond lh-base product-description-bigText {{ $card['big-text-vertical-align'] ?? '' }} {{ $card['big-text-align'] ?? '' }}" style="{{ $card['big-text-style'] ?? '' }}">
                          {!! nl2br($card['big-text']) !!}
                        </div>
                      @endif
                      @if(isset($card['text']))
                        <div class="cormorantGaramond lh-base product-description-item {{ $card['align'] ?? '' }} {{ $card['vertical-align'] ?? '' }}" style="{{ $card['text-style'] ?? '' }}">
                          {!! nl2br($card['text']) !!}
                        </div>
                      @endif
                      @if(isset($card['small-text']))
                        <div class="cormorantGaramond lh-base product-description-smallText {{ $card['small-text-vertical-align'] ?? '' }} {{ $card['small-text-align'] ?? '' }}" style="{{ $card['small-text-style'] ?? '' }}">
                          {!! nl2br($card['small-text']) !!}
                        </div>
                      @endif
                    </div>
                    @if(isset($card['image']))
                      <div class="img product_card_preview item-square block">

                        <input type="hidden" data-id="productImage-{{ $product->id }}-{{ $key }}" class="json-image"
                               value="{{ e(json_encode($card['image'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover" data-img-class="block w-full">
                      </div>
                    @endif
                  </div>
                </div>
              </div>
            @endforeach
          </div>
          <div class="swiper-pagination swiper-pagination-outside swiper-pagination-dark"></div>
        </div>
      @endif
      @if(isset($product->style_page['accordion'])&&!empty($product->style_page['accordion']))
        @foreach($product->style_page['accordion'] as $accortion_item)
          <div class="toggle-wrapper mb-2 md:mb-4 bg-myGreen">
            <div class="toggle-button p-4 cursor-pointer headline-4 lh-none flex justify-center items-center text-center">
              <span>{{ $accortion_item['title'] }}</span>
              <svg width="17" height="11" class="ml-2" viewBox="0 0 17 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g filter="url(#filter0_b_1943_23515)">
                  <path d="M16.5 2.23962L8.5 10.166L0.5 2.23962L1.92 0.832683L8.5 7.35214L15.08 0.832682L16.5 2.23962Z"
                        fill="black"/>
                </g>
                <defs>
                  <filter id="filter0_b_1943_23515" x="-31.5" y="-31.168" width="80" height="73.334"
                          filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                    <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                    <feGaussianBlur in="BackgroundImageFix" stdDeviation="16"/>
                    <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_1943_23515"/>
                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_1943_23515" result="shape"/>
                  </filter>
                </defs>
              </svg>
            </div>
            <div
              class="toggle-content overflow-hidden transition-max-height duration-500 h-0 mx-auto max-w-[700px] d-text-body m-text-body px-2 md:px-0">
              <div class="py-4 md:py-6">
                @if(trim(mb_strtolower($accortion_item['title'])) == mb_strtolower('СПОСОБ ПРИМЕНЕНИЯ') && ($product->style_page['k_info'] ?? false))
                  <div class="text-white font-bold p-1 mb-4 text-center" style="background: rgba(0,0,0,.36);">
                    Подробнее о комплексном уходе<br> и сочетании продуктов читайте выше.
                  </div>
                @endif
                {!! $accortion_item['text'] !!}
              </div>
            </div>
          </div>
        @endforeach
      @endif
    </div>

  </div>
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-6 sm:py-10 md:py-14 lg:py-16 xl:py-[86px]">
    <div>
      <div class="flex justify-center items-center mb-8 md:mb-10 lg:mb-12">
        <div class="border-t border-b border-myBrown w-[124px]"></div>
        <h2 class="text-center product-headline text-myBrown mx-6 lh-base">Отзывы</h2>
        <div class="border-t border-b border-myBrown w-[124px]"></div>
      </div>
      @if($reviews->count())
        <div id="swiper-product-reviews" class="swiper overflow-visible" data-autoheight>
          <div class="swiper-wrapper overflow-hidden">
            @foreach($reviews as $review)
              <div class="swiper-slide flex flex-col overflow-hidden">
                <div class="mx-auto w-full max-w-md flex-1 product-review bg-myGreen rounded-[16px] md:rounded-[20px] lg:rounded-[24px] p-6 md:p-9 lg:p-12"
                     data-rating="{{ $review->rating }}" data-id="review-{{ $review->id }}">
                  <div id="review-{{ $review->id }}" class="hidden flex space-x-3 md:space-x-6 mb-6"></div>
                  <div class="lh-none d-text-body m-text-body cormorantInfant mb-6 font-semibold">{{ \Carbon\Carbon::parse($review->created_at)->format('d.m.Y') }}</div>
                  <div class="flex items-center mb-4 md:mb-5 lg:md-6 space-x-4">
                    <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center bg-white rounded-full border border-myGray overflow-hidden">
                      @if(!empty($review->img))
                        <span class="file_thumb"><img src="{{ storageToAsset($review->img) }}" alt="" class="w-12 h-12 rounded-full object-cover"></span>
                      @else
                        <span class="file_thumb">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user-circle" width="48" height="48" viewBox="0 0 24 24" stroke-width="1" stroke="#B2B2B2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                          <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                          <path d="M12 10m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                          <path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855" />
                        </svg>
                      </span>
                      @endif
                    </div>
                    <div class="lh-none text-2xl md:text-3xl lg:text-32 font-semibold">{{ getShortName($review->first_name, $review->last_name) }}</div>


                  </div>
                  <div>
                    <div class="relative txt-body overflow-hidden transition-all duration-500 ease-in-out collapsibleBlock"
                         data-symbols="88" data-button-id="toggleButton-review-{{ $review->id }}">
                      {{ $review->text }}
                    </div>
                    @if($review->files&&hasVisibleItem($review->files))
                      <div class="relative mt-6">
                        <div class="swiper swiper-review-photos overflow-visible">
                          <div class="swiper-wrapper overflow-visible">
                            @foreach($review->files as $index => $file)
                              @if($file['hidden'] ?? false)
                                @continue
                              @endif
                              <div class="swiper-slide w-[102px] !h-[102px]">
                                <a href="{{ storageToAsset($file['image']) }}" data-fancybox="review-{{ $review->id }}" class="block item-square rounded-lg bg-myBeige w-[102px]">
                                  <img src="{{ storageToAsset($file['thumb']) }}" class="block w-full rounded-lg" alt="{{ $product->name }}">
                                </a>
                              </div>
                            @endforeach
                          </div>
                        </div>
                      </div>
                    @endif
                    <div class="mt-4">
                      <button class="text-base md:text-lg lg:text-xl font-semibold flex items-center"
                              id="toggleButton-review-{{ $review->id }}" data-open-text="Развернуть" data-close-text="Свернуть"><span class="text">Развернуть</span>
                        <svg width="16" height="16" class="ml-2" viewBox="0 0 16 16" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                          <g filter="url(#filter0_b_1918_1487)">
                            <path d="M3 5.96202L8 10.916L13 5.96202L12.1125 5.08268L8 9.15735L3.8875 5.08268L3 5.96202Z"
                                  fill="#000"/>
                          </g>
                          <defs>
                            <filter id="filter0_b_1918_1487" x="-32" y="-32" width="80" height="80"
                                    filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                              <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                              <feGaussianBlur in="BackgroundImageFix" stdDeviation="16"/>
                              <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_1918_1487"/>
                              <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_1918_1487"
                                       result="shape"/>
                            </filter>
                          </defs>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
            @if($reviews->lastPage()>$reviews->currentPage())
              <div class="swiper-slide flex flex-col justify-center overflow-hidden">
                <div class="mx-auto max-w-md flex-1 flex items-center product-review rounded-[16px] md:rounded-[20px] lg:rounded-[24px] p-6 md:p-9 lg:p-12">
                  <x-public.primary-button href="{{ route('product.reviews', $product->slug) }}" class="md:w-full max-w-[193px] md:max-w-[223px] lg:max-w-[253px] xl:max-w-[283px] md:text-2xl md:h-14">
                    Посмотреть все отзывы
                  </x-public.primary-button>
                </div>
              </div>
            @endif
          </div>

          <div class="swiper-button-next swiper-buttom-outside backdrop-blur-sm hidden md:flex"></div>
          <div class="swiper-button-prev swiper-buttom-outside backdrop-blur-sm hidden md:flex"></div>
          <div class="swiper-pagination swiper-pagination-outside swiper-pagination-dark md:hidden"></div>
        </div>
      @else
        <div class="p-6 text-center">
          <div class="d-headline-4 m-headline-3 text-gray-400">Пока нет отзывов</div>
        </div>
      @endif
      <div class="text-center mt-6 md:mt-9 lg:mt-12">
        @auth
          @php($canLeaveReview = auth()->user()->canLeaveReview($product->id))
          <x-public.primary-button href="javascript:;" data-rating-form data-fancybox-no-close-btn data-src="#form" class="md:w-full max-w-[193px] md:max-w-[223px] lg:max-w-[253px] xl:max-w-[283px] md:text-2xl md:h-14">
            Оставить отзыв
          </x-public.primary-button>
        @else
          <x-public.primary-button href="javascript:;" data-fancybox-no-close-btn data-src="#authForm" class="md:w-full max-w-[193px] md:max-w-[223px] lg:max-w-[253px] xl:max-w-[283px] md:text-2xl md:h-14">
            Оставить отзыв
          </x-public.primary-button>
        @endauth
      </div>
    </div>
  </div>
  @auth
    @if($canLeaveReview)
      <x-public.popup id="form">
        <x-slot name="icon">
          <h4 class="d-headline-4 m-headline-3 leading-none lh-outline-none">Ваш отзыв</h4>
        </x-slot>
        <x-public.review-form action="{{ route('product.review', $product->slug) }}"/>
      </x-public.popup>

    @else

      <x-public.popup id="form">
        <div class="p-6 d-headline-4 m-headline-3 text-center">
          Вы можете оставить только один отзыв о тех продуктах, которые вы купили
        </div>
      </x-public.popup>
    @endif
  @endauth
  @if(isset($products)&&$products->count())
    <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-12 md:py-14 lg:py-16 xl:py-[86px]">
      <div>
        <div class="mb-6 md:mb-12">
          <h2 class="text-center m-headline-2 d-headline-2 lh-outline-none">Комплексный уход</h2>
        </div>
        <div class="flex flex-wrap -mx-2 md:-mx-3 -my-2 md:-my-3">
          @foreach($products as $p)
            <x-public.product-item id="{{ $p->id }}" class="w-1/2 md:w-1/3 xl:w-1/4 px-2 md:px-3 py-2 md:py-3  flex flex-col justify-between" :product="$p"/>
          @endforeach
        </div>
      </div>
    </div>
  @endif


  <!-- ... rating star example ... -->
  <div class="hidden">
    <svg class="md:w-6 w-[18px]" viewBox="0 0 24 22" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path
        d="M12 0L14.6942 8.2918H23.4127L16.3593 13.4164L19.0534 21.7082L12 16.5836L4.94658 21.7082L7.64074 13.4164L0.587322 8.2918H9.30583L12 0Z"
        fill="#2C2E35"/>
    </svg>
  </div>
  <!-- ... rating star example ... -->
  <script>
    new Swiper('#swiper-product-reviews', {
      slidesPerView: 1,
      spaceBetween: 24,
      mousewheel: {
        releaseOnEdges: true
      },
      preloadImages: false,
      lazy: true,
      cssMode: true,
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
      },
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
      breakpoints: {
        640: {
          slidesPerView: 2,
        },
        768: {},
        1024: {
          slidesPerView: 3,
        },
      },
    });
    new Swiper('#swiper-product-carousel', {
      effect: "coverflow",
      grabCursor: true,
      centeredSlides: true,
      slidesPerView: "auto",
      coverflowEffect: {
        rotate: 0,
        stretch: 100,
        depth: 100,
        modifier: 1,
        scale: .8,
        slideShadows: true,
      },
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
      },
      breakpoints: {
        640: {
          slidesPerView: 3,
        }
      },
    });

    const swiperOptions = {
      // // Optional parameters
      // direction: 'vertical',
      // loop: true,
      // wrapperClass: 'product-item-swiper__wrapper',
      // slideClass: 'product-item-swiper__slide',
      slidesPerView: "auto",
      // preloadImages: false,
      // lazy: true,
      // cssMode: true,
      spaceBetween: 10,
      freeMode: true,
      mousewheel: true,
      // And if we need scrollbar
      // scrollbar: {
      //   el: '#products-01 .swiper-scrollbar',
      // },
      // pagination: {
      //   clickable: true,
      //   el: ".product-item-swiper__pagination",
      // },
    }
    if(document.querySelectorAll('.swiper-review-photos').length>0){
      new Swiper('.swiper-review-photos', swiperOptions);
    }

  </script>
  <script>
    const reviews = document.querySelectorAll('.product-review[data-rating][data-id]');
    reviews.forEach((review) => {
      const rating = review.dataset.rating
      const block_id = review.dataset.id
      setRating(rating, block_id)
    })

    function setRating(rating, blockId) {
      const filledStar = `
        <svg class="md:w-6 w-[18px]" viewBox="0 0 24 22" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 0L14.6942 8.2918H23.4127L16.3593 13.4164L19.0534 21.7082L12 16.5836L4.94658 21.7082L7.64074 13.4164L0.587322 8.2918H9.30583L12 0Z" fill="#2C2E35"/>
        </svg>`;
      const unfilledStar = `
        <svg class="md:w-6 w-[18px]" viewBox="0 0 24 22" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path opacity="0.32" d="M12 0L14.6942 8.2918H23.4127L16.3593 13.4164L19.0534 21.7082L12 16.5836L4.94658 21.7082L7.64074 13.4164L0.587322 8.2918H9.30583L12 0Z" fill="#2C2E35"/>
        </svg>`;

      const container = document.getElementById(blockId);
      if (!container) return;

      container.innerHTML = '';

      for (let i = 0; i < 5; i++) {
        if (i < Math.floor(rating)) {
          container.innerHTML += filledStar;
        } else {
          container.innerHTML += unfilledStar;
        }
      }
    }
  </script>
  @if($product->style_page['alert'] ?? false && $product->style_page['alertText'] ?? false)
    <x-public.popup id="product-alert" :alert-button-text="$product->style_page['alertButtonText'] ?? false" :alert-button-link="$product->style_page['alertButtonUrl'] ?? false" :alert-close-button-text="$product->style_page['alertCloseButton'] ?? false">
      <x-slot name="icon">
        <svg width="65" height="64" viewBox="0 0 65 64" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M32.5 58.6667C47.1667 58.6667 59.1667 46.6667 59.1667 32C59.1667 17.3333 47.1667 5.33333 32.5 5.33333C17.8334 5.33333 5.83337 17.3333 5.83337 32C5.83337 46.6667 17.8334 58.6667 32.5 58.6667Z" stroke="#B1908E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M32.5 21.3333V34.6667" stroke="#B1908E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M32.4854 42.6667H32.5093" stroke="#B1908E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </x-slot>
      <div class="m-text-body d-text-body text-center">
        {!! $product->style_page['alertText'] !!}
      </div>
    </x-public.popup>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        setTimeout(()=>{
          Fancybox.show(
            [
              {
                src: '#product-alert',
              },
            ],
            {
              closeButton: false,
              Toolbar: {
                display: {
                  left: [],
                  middle: [],
                  right: [],
                },
              },
              loop: false,
              touch: false,
              contentClick: false,
              dragToClose: false,
            }
          );
        },1000)
      })
    </script>
  @endif
</x-app-layout>
