@props(['filtres' => []])
<script>
  const filters = @json($filtres);
</script>
<div x-data="productsLoader(filters)" x-init="initializeLoader()">
  <div {{ $attributes->merge(['class' => 'products-container']) }}>
    <template x-for="product in products" :key="product.id">
      <div class="w-1/2 lg:w-1/3 px-2 md:px-3 py-2 md:py-3 flex flex-col justify-between product">
        <div>
          <div class="swiper product-item-swiper">
            <div class="swiper-wrapper product-item-swiper__wrapper">
              <div class="swiper-slide product-item-swiper__slide">
                <div class="product-card_item relative">
                  <template x-if="product.puzzles_count">
                    <div class="flex items-center absolute z-10 text-myGreen2 text-md" style="left: 9.625668449%; top:8.771929825%;" :style="`color: ${product.puzzle_color}`">
                      <x-public.puzzle-svg style="width: 6.417112299%;"/>
                      <span class="cormorantInfant ml-1.5 " style="line-height: 0" x-text="product.puzzles_count"></span>
                    </div>
                  </template>
                  <template x-if="product.refill">
                    <div x-data="{ open: false }">
                      <div x-show="!open" @click="open = !open" class="flex items-center absolute z-10 text-myDark bg-white top-3 left-3 text-md py-2 px-3 leading-none border border-myDark">
                        есть refill <svg width="20" height="20" class="ml-1.5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M19.5 10C19.5 15.2239 15.2239 19.5 10 19.5C4.77614 19.5 0.5 15.2239 0.5 10C0.5 4.77614 4.77614 0.5 10 0.5C15.2239 0.5 19.5 4.77614 19.5 10Z" stroke="#2C2E35" stroke-linecap="round" stroke-linejoin="round"/>
                          <path d="M10 15V10" stroke="#2C2E35" stroke-linecap="round" stroke-linejoin="round"/>
                          <path d="M9.99609 7H10.0051" stroke="#2C2E35" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                      </div>
                      <div class="absolute z-10 p-3 w-full">
                        <div x-show="open" @click.outside="if(open) open = false" class="flex items-center text-myDark bg-white leading-none border border-myDark">
{{--                          <div class="item-square hidden sm:block" style="width: 40%;max-width: 111px;">--}}
{{--                            <picture x-show="product.refill_img" x-html="product.refill_img" x-init="$nextTick(() => window.processNewContent());console.log('product.refill_img', product.refill_img)" class="block w-full" data-img-class="object-bottom object-cover block w-full"></picture>--}}
{{--                          </div>--}}
                          <div class="py-2 px-3 text-md sm:text-md">
                            <div x-text="product.refill.name" class="font-medium"></div>
                            <div class="flex justify-between mt-2 w-full">
                              <div x-text="product.volume" class="font-infant_font"></div>
                              <div x-html="product.refill_price" class="font-bold"></div>
                            </div>
                            <template x-if="product.refill_button">
                              <div x-html="product.refill_button" class="mt-2"></div>
                            </template>
                          </div>
                        </div>
                      </div>
                    </div>
                  </template>
                  <a :href="product.route ? product.route : null" class="img product_card_preview item-square block">
                    <picture x-show="typeof product.image !== 'undefined'" x-html="product.image" x-init="$nextTick(() => window.processNewContent())" class="block w-full" data-img-class="object-bottom object-cover block w-full"></picture>
                  </a>
                </div>
              </div>
              <template x-if="product.style_cards">
                <template x-for="card in product.style_cards" :key="card.id">
                  <div class="swiper-slide product-item-swiper__slide">
                    <div class="product-card_item relative">
                      <div :class="'product_' + card.card_style" class="relative bg-white overflow-hidden max-w-sm">
                        <div class="img product_card_preview item-square block">
                          <picture x-show="typeof card.image !== 'undefined'" x-html="card.image" x-init="$nextTick(() => window.processNewContent())" class="item-square block w-full" data-img-class="object-bottom object-cover block w-full"></picture>
                        </div>
                      </div>
                    </div>
                  </div>
                </template>

              </template>
            </div>
            <div class="product-item-swiper__pagination swiper-pagination"></div>
          </div>
          <div class="mb-4 mt-4 md:mt-6 flex-1">

            <h3 class="break-words font-light text-lg sm:text-2xl md:text-3xl lg:text-32 sm:min-h-[52px] pr-2 lh-outline-none !leading-none">
              <a :href="product.route ? product.route : null" x-text="product.name"></a>
            </h3>
            <template x-if="product.subtitle">
              <div class="text-myBrown d-text-body mt-2" x-html="product.subtitle"></div>
            </template>
            <template x-if="product.only_pickup">
              <p class="text-myRed text-sm md:text-md lg:text-lg mt-1 !leading-none">Доступен только для самовывоза г. Волгоград</p>
            </template>
          </div>
        </div>
        <div>
          <template x-if="product.quantity > 0 && product.status && product.stockStatus">
            <div class="text-xs sm:text-sm md:text-base mt-2 mb-4" style="color: {{ $product->stockStatus['color'] }}">
              <div class="cormorantInfant" x-text="product.stockStatus.text"></div>
              <div x-data>
                <svg style="width: 100%;" height="4" viewBox="0 0 100% 4" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <rect width="100%" height="4" fill="#B2B2B2" fill-opacity="0.25"/>
                  <rect :width="`${product.stockStatus.percent}%`" height="4" fill="currentColor"/>
                </svg>
              </div>
            </div>
          </template>
          <div class="relative flex items-center justify-between mb-4 md:mb-6">
            <div class="flex space-x-2 md:space-x-4 items-center">
              <div class="sm:flex sm:space-x-2">
                <template x-if="product.print_old_price && product.print_old_price > 0">
                  <div class="text-base sm:text-md md:text-lg cormorantInfant italic font-semibold text-myGray line-through" x-html="product.print_old_price"></div>
                </template>
                <div class="subtitle-1 text-myBrown" x-html="product.print_price"></div>
              </div>
              <template x-if="product.volume">
                <div class="text-sm md:text-base cormorantInfant font-medium italic opacity-50" x-text="product.volume"></div>
              </template>
            </div>
            <a class="btn-tooltip w-5 h-5 relative" :data-tooltip="'tooltip-'+product.id" :style="product.cardsDescription ? '' : 'display: hidden;'">
              <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 19C14.95 19 19 14.95 19 10C19 5.05 14.95 1 10 1C5.05 1 1 5.05 1 10C1 14.95 5.05 19 10 19Z" stroke="#B1908E" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M10 14.5V10" stroke="#B1908E" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M9.99512 7.30078H10.0032" stroke="#B1908E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </a>
            <div :id="'tooltip-'+product.id" class="tooltip hidden absolute bottom-full mb-4 shadow-md p-6 bg-white w-screen max-w-[260px] md:max-w-[386px] z-10">
              <div class="flex justify-between items-center mb-3 md:mb-6">
                <h4 class="headline-4">о продукте</h4>
                <button class="close-tooltip">
                  <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.0003 18.3327C14.5837 18.3327 18.3337 14.5827 18.3337 9.99935C18.3337 5.41602 14.5837 1.66602 10.0003 1.66602C5.41699 1.66602 1.66699 5.41602 1.66699 9.99935C1.66699 14.5827 5.41699 18.3327 10.0003 18.3327Z" stroke="#2C2E35" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7.6416 12.3592L12.3583 7.64258" stroke="#2C2E35" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12.3583 12.3592L7.6416 7.64258" stroke="#2C2E35" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </button>
              </div>
              <div class="mb-3 md:mb-6 subtitle-2" x-html="product.cardsDescription"></div>
              <div class="flex flex-col justify-between space-y-3 mb-3 md:mb-6  subtitle-2">
                <template x-if="product.cardsDescriptionIcons" x-for="icon in product.cardsDescriptionIcons">
                  <div class="flex items-center">
                    <div class="w-5 h-5 mr-2">
                      <img :src="icon.icon" :alt="icon.text" class="w-[18px] h-[18px]">
                    </div>
                    <div x-text="icon.text"></div>
                  </div>
                </template>
              </div>
              <a :href="product.route ? product.route : null" class="inline-flex items-center justify-center px-3 md:px-4 px-7 border border-black text-xl leading-none font-medium w-full h-8">
                Читать подробнее
              </a>
              <div class="absolute tooltip-arrow w-3 h-3 -bottom-1 right-1/2 transform translate-x-1/2 rotate-45 bg-white shadow-sm"></div>
            </div>
          </div>
          <template x-if="product.button">
            <div x-html="product.button"></div>
          </template>
        </div>
      </div>
    </template>
  </div>
  <div x-show="loading" class="flex justify-center items-center my-6">
    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-loader block mx-auto" width="30" height="30" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
      <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
      <path d="M12 6l0 -3" />
      <path d="M16.25 7.75l2.15 -2.15" />
      <path d="M18 12l3 0" />
      <path d="M16.25 16.25l2.15 2.15" />
      <path d="M12 18l0 3" />
      <path d="M7.75 16.25l-2.15 2.15" />
      <path d="M6 12l-3 0" />
      <path d="M7.75 7.75l-2.15 -2.15" />
    </svg>
  </div>
</div>

