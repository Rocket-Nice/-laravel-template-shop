@section('title', $seo['title'] ?? config('app.name'))
<x-app-layout>

  <div class="rotate-180"></div>
  <div class="sm:px-4 md:px-8 lg:px-14 xl:px-16">
{{--    h-[180px] sm:h-[200px] md:h-[231px] lg:h-[298px] xl:h-[378px]--}}
    @if(isset($category->options['categoryImage']['size']))
      <input type="hidden" data-id="mainImage" class="json-image"
             value="{{ e(json_encode($category->options['categoryImage']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover" data-img-class="block h-[234px] sm:h-[260px] md:h-[300px] lg:h-[387px] xl:h-[491px] w-full object-cover">
    @elseif(isset($content->image_data['mainImage']['size']))
      <input type="hidden" data-id="mainImage" class="json-image"
             value="{{ e(json_encode($content->image_data['mainImage']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover" data-img-class="block h-[234px] sm:h-[260px] md:h-[300px] lg:h-[387px] xl:h-[491px] w-full object-cover">
    @endif

  </div>
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-6 sm:py-9 md:py-12">
    <div class="flex justify-between items-center">
      <div class="w-6 hidden md:block"></div>
      <h1 class="flex-1 d-headline-1 m-headline-1 text-left md:text-center">{{ $content->title ?? 'Каталог' }}</h1>
      <div class="w-6">
      </div>
    </div>
  </div>
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16">
    <div id="wrapper" class="flex relative">
      <!-- Main Content -->
      <div class="flex-1">
        <div class="flex flex-wrap -mx-2 md:-mx-3" id="catalog">
          @forelse($products as $product)
            <div class="w-1/2 md:w-1/3 lg:w-1/2 xl:w-1/3 px-2 md:px-3 py-2 md:py-3 flex flex-col justify-between">
              <div>
                <div class="swiper product-item-swiper">
                  <div class="swiper-wrapper product-item-swiper__wrapper">
                    <div class="swiper-slide product-item-swiper__slide">
                      <div class="product-card_item">
                        <a href="{{ route('product.present', $product->slug) }}" class="img product_card_preview item-square block">
                          @if(isset($product->cardImage['image']))
                            <input type="hidden" data-id="cardImage-{{ $product->id }}" class="json-image"
                                   value="{{ e(json_encode($product->cardImage['image'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover" data-img-class="block w-full object-cover">
                          @endif
                        </a>
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
              <div class="mb-4 mt-4 md:mt-6 flex-1">
                <h3 class="break-words font-light text-lg sm:text-2xl md:text-3xl lg:text-32 min-h-[52px] pr-2 lh-outline-none !leading-none"><a href="{{ route('product.present', $product->slug) }}" class="no-underline">{{ $product->name }}</a></h3>
              </div>
            </div>

          @empty

            <div class="text-center p-12 d-headline-4 m-headline-3 uppercase text-gray-400 w-full">Здесь пусто</div>

          @endforelse
        </div>
      </div>
    </div>
{{--    <div class="flex justify-center mt-12">--}}
{{--      <x-public.primary-button type="button" id="loadMore" class="md:h-13 md:w-full md:max-w-[285px] mx-auto">Показать еще</x-public.primary-button>--}}
{{--      <input type="hidden" name="page" id="loader-page" value="{{ $products->currentPage() + 1 }}">--}}
{{--      <script>--}}
{{--        window.loadRoute = @json(route('product.loadProducts'))--}}
{{--      </script>--}}
{{--    </div>--}}
  </div>

{{--  <script>--}}
{{--    new Swiper('.guests-swiper', {--}}
{{--      // // Optional parameters--}}
{{--      // direction: 'vertical',--}}
{{--      // loop: true,--}}
{{--      slidesPerView: "auto",--}}
{{--      preloadImages: false,--}}
{{--      lazy: true,--}}
{{--      cssMode: true,--}}
{{--      mousewheel: true,--}}
{{--      // And if we need scrollbar--}}
{{--      // scrollbar: {--}}
{{--      //   el: '#products-01 .swiper-scrollbar',--}}
{{--      // },--}}
{{--      pagination: {--}}
{{--        el: ".swiper-pagination",--}}
{{--      },--}}
{{--    });--}}
{{--  </script>--}}
  <script>
    // chat gpt Sticky Menu TailwindJS

    // document.addEventListener('DOMContentLoaded', function () {
    //   const leftMenu = document.getElementById('leftMenu');
    //   const leftMenuContent = document.getElementById('leftMenu-content');
    //   const wrapper = document.getElementById('wrapper');
    //
    //   window.addEventListener('scroll', handleScroll);
    //
    //   function handleScroll() {
    //     const wrapperHeight = wrapper.offsetHeight;
    //     const menuHeight = leftMenu.offsetHeight;
    //     const contentHeight = leftMenuContent.offsetHeight;
    //
    //     const wrapperTop = wrapper.getBoundingClientRect().top;
    //     const wrapperBottom = wrapper.getBoundingClientRect().bottom;
    //     const menuBottom = leftMenu.getBoundingClientRect().bottom;
    //
    //     if (wrapperHeight <= window.innerHeight) return;
    //
    //     // 2.2
    //     if (contentHeight <= window.innerHeight) {
    //       if (window.scrollY >= -wrapperTop) {
    //         leftMenuContent.style.position = 'fixed';
    //         leftMenuContent.style.top = '0px';
    //       }
    //       if (window.innerHeight + window.scrollY >= wrapperBottom) {
    //         leftMenuContent.style.position = 'absolute';
    //         leftMenuContent.style.bottom = '0px';
    //         leftMenuContent.style.top = 'auto';
    //       }
    //     }
    //     // 2.3
    //     else {
    //       if (menuBottom - contentHeight <= window.innerHeight) {
    //         leftMenuContent.style.position = 'fixed';
    //         leftMenuContent.style.bottom = '0px';
    //         leftMenuContent.style.top = 'auto';
    //       } else {
    //         leftMenuContent.style.position = 'fixed';
    //         leftMenuContent.style.top = '0px';
    //         leftMenuContent.style.bottom = 'auto';
    //       }
    //       if (window.scrollY <= -wrapperTop) {
    //         leftMenuContent.style.position = 'absolute';
    //         leftMenuContent.style.top = '0px';
    //         leftMenuContent.style.bottom = 'auto';
    //       }
    //     }
    //   }
    // });
  </script>
</x-app-layout>
