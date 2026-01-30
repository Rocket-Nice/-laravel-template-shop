@section('title', $seo['title'] ?? config('app.name'))
<x-app-layout>
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 !pt-0 py-6 sm:py-10 md:py-14 lg:py-16 xl:py-[86px]">
    <div class="lg:flex lg:flex-row-reverse">
      <div class="mb-6 lg:mb-0 w-full mx-auto sm:w-7/12 md:w-5/12 flex-1 relative" id="productSlider">
        <div>
          <div class="swiper product-item-swiper">
            <div class="swiper-wrapper product-item-swiper__wrapper">
              <div class="swiper-slide product-item-swiper__slide">
                <div class="product-card_item">
                  <div class="img product_card_preview item-square block">
                    @if(isset($product->style_page['cardImage']['image']))
                      <input type="hidden" data-id="cardImage-{{ $product->id }}" class="json-image"
                             value="{{ e(json_encode($product->style_page['cardImage']['image'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover" data-img-class="block w-full object-cover">
                    @endif
                  </div>
                </div>
              </div>
              @if($product->style_cards)
                @foreach($product->style_cards as $key => $card)
                  @if(!isset($card['card_style']))
                    @continue
                  @endif
                  <div class="swiper-slide product-item-swiper__slide">
                    <div class="product-card_item">
                      <div class="product_{{ $card['card_style'] }} relative bg-white overflow-hidden">
                        @if(isset($card['image']))
                          <div class="img product_card_preview item-square block">

                            <input type="hidden" data-id="productImage-{{ $product->id }}-{{ $key }}" class="swiper-json-image swiper-slide-img"
                                   value="{{ e(json_encode($card['image'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover" data-img-class="block w-full">

                          </div>
                        @endif
                      </div>
                    </div>
                  </div>
                @endforeach
              @endif
            </div>
            <div class="product-item-swiper__pagination swiper-pagination"></div>
          </div>
        </div>
      </div>
      <div class="md:pt-12 lg:pr-12 lg:w-7/12">
        <div class="flex justify-between items-center space-x-5">
          <h1 class="headline-3b">{{ $product->name }}</h1>
        </div>
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
      </div>
    </div>
  </div>
  <div>
    <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16">
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
                @if(trim(mb_strtolower($accortion_item['title'])) == mb_strtolower('СПОСОБ ПРИМЕНЕНИЯ') && (!isset($product->style_page['k_info']) || !$product->style_page['k_info']))
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
  @if(isset($products)&&$products->count())
    <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-12 md:py-14 lg:py-16 xl:py-[86px]">
      <div>
        <div class="mb-6 md:mb-12">
          <h2 class="text-center m-headline-2 d-headline-2 lh-outline-none text-myBrown">Подобный аромат вы также можете встретить
            в следующих наших продуктах:</h2>
        </div>
        <div class="flex flex-wrap -mx-2 md:-mx-3 -my-2 md:-my-3">
          @foreach($products as $p)
            <x-public.product-item id="{{ $p->id }}" class="w-1/2 md:w-1/3 xl:w-1/4 px-2 md:px-3 py-2 md:py-3" :product="$p"/>
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
</x-app-layout>
