@section('title', cleanSpaces($seo['title']) ?? config('app.name'))
<x-app-layout>
  <main class="pt-4 lg:pt-8 xl:pt-10 ">
    @if(isset($article->data_title['image']['size']))
      <div class="w-full object-cover max-w-[520px] mx-auto">
        <input type="hidden" data-id="productionImage" class="json-image"
               value="{{ e(json_encode($article->data_title['image']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover" data-alt="{{ $article->name }}">
      </div>
    @endif
    <a
      href="{{ route('blog.index') }}"
      class="flex justify-center max-w-[520px] mx-auto my-6 flex items-center gap-2 border-2 border-myGreen px-[18px] py-2 text-2xl font-medium uppercase leading-1.6 text-center"
    >
      <svg width="25" height="25" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.07 18.57 4 12.5l6.07-6.07M21 12.5H4.17" stroke="#6C715C" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/></svg>
      <p>читать все новости</p>
    </a>
    <section class="py-6 sm:py-8 lg:mb-6">
      <div class="mx-auto h-[1px] max-w-[132px] bg-myBrown"></div>
      <h3
        class="py-8 text-center text-2xl lg:text-3xl uppercase"
      >Раздел {{ $article->category?->name}}</h3>
      <div class="mx-auto h-[1px] max-w-[186px] bg-myBrown"></div>
    </section>
    @if(isset($article->data_content['articleContent'])&&is_array($article->data_content['articleContent']))
      @foreach($article->data_content['articleContent'] as $block)
        @if(isset($block['youtube']))
          <div class="{{ $block['class'] ?? '' }} ">
            <div class="px-3 lg:max-w-[1000px] mx-auto rounded-lg overflow-hidden border w-full mb-4 relative video item-square">
              <div id="{{ $block['youtube'] }}" class="youtube"></div>
              {{--            <div class="play relative z-10 absolute left-0 top-0 w-full h-full">--}}
              {{--              <svg class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 z-10" width="50" height="40" viewBox="0 0 50 40" fill="none" xmlns="http://www.w3.org/2000/svg">--}}
              {{--                <path d="M48.481 5.8836C47.9174 3.77614 46.2559 2.11468 44.1485 1.55106C40.3256 0.526734 25 0.526733 25 0.526733C25 0.526733 9.67433 0.526734 5.8515 1.55106C3.74404 2.11468 2.08258 3.77614 1.51895 5.8836C0.494629 9.70644 0.494629 20.131 0.494629 20.131C0.494629 20.131 0.494629 30.5556 1.51895 34.3784C2.08258 36.4859 3.74404 38.1473 5.8515 38.711C9.67433 39.7353 25 39.7353 25 39.7353C25 39.7353 40.3256 39.7353 44.1485 38.711C46.2584 38.1473 47.9174 36.4859 48.481 34.3784C49.5053 30.5556 49.5053 20.131 49.5053 20.131C49.5053 20.131 49.5053 9.70644 48.481 5.8836ZM20.0989 26.4975V13.7645C20.0989 12.8211 21.1208 12.2329 21.9368 12.7034L32.9642 19.0699C33.7802 19.5404 33.7802 20.7216 32.9642 21.1921L21.9368 27.5586C21.1208 28.0315 20.0989 27.441 20.0989 26.4975Z" fill="white"/>--}}
              {{--              </svg>--}}
              {{--              @if(isset($block['image']))--}}
              {{--                <input type="hidden" data-id="productionImage" class="json-image"--}}
              {{--                       value="{{ e(json_encode($block['image']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover">--}}
              {{--              @endif--}}
              {{--            </div>--}}
            </div>
          </div>

        @elseif(isset($block['rutube']))
            <div class="{{ $block['class'] ?? '' }} ">
              <div class="px-3 lg:max-w-[1000px] mx-auto rounded-lg overflow-hidden border w-full mb-4 relative video item-square">
                <div id="{{ $block['rutube'] }}" class="rutube"></div>
                {{--            <div class="play relative z-10 absolute left-0 top-0 w-full h-full">--}}
                {{--              <svg class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 z-10" width="50" height="40" viewBox="0 0 50 40" fill="none" xmlns="http://www.w3.org/2000/svg">--}}
                {{--                <path d="M48.481 5.8836C47.9174 3.77614 46.2559 2.11468 44.1485 1.55106C40.3256 0.526734 25 0.526733 25 0.526733C25 0.526733 9.67433 0.526734 5.8515 1.55106C3.74404 2.11468 2.08258 3.77614 1.51895 5.8836C0.494629 9.70644 0.494629 20.131 0.494629 20.131C0.494629 20.131 0.494629 30.5556 1.51895 34.3784C2.08258 36.4859 3.74404 38.1473 5.8515 38.711C9.67433 39.7353 25 39.7353 25 39.7353C25 39.7353 40.3256 39.7353 44.1485 38.711C46.2584 38.1473 47.9174 36.4859 48.481 34.3784C49.5053 30.5556 49.5053 20.131 49.5053 20.131C49.5053 20.131 49.5053 9.70644 48.481 5.8836ZM20.0989 26.4975V13.7645C20.0989 12.8211 21.1208 12.2329 21.9368 12.7034L32.9642 19.0699C33.7802 19.5404 33.7802 20.7216 32.9642 21.1921L21.9368 27.5586C21.1208 28.0315 20.0989 27.441 20.0989 26.4975Z" fill="white"/>--}}
                {{--              </svg>--}}
                {{--              @if(isset($block['image']))--}}
                {{--                <input type="hidden" data-id="productionImage" class="json-image"--}}
                {{--                       value="{{ e(json_encode($block['image']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover">--}}
                {{--              @endif--}}
                {{--            </div>--}}
              </div>
            </div>
        @elseif(isset($block['image']))
          <div class="{{ $block['class'] ?? '' }}">
            <div class="relative max-w-[520px] xl:max-w-[700px] mx-auto">
              <input type="hidden" data-id="productionImage" class="json-image"
                     value="{{ e(json_encode($block['image']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover">
            </div>
          </div>

        @elseif(isset($block['content']))
          {!! $block['content'] !!}
        @elseif(isset($block['text']))
          <div class="mx-auto max-w-[520px] text-[20px] lg:text-2xl font-normal leading-1.6">
            {!! $block['text'] !!}
          </div>
        @endif
        @endforeach
    @endif


      @if(isset($article->products))
        <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-12 md:py-14 lg:py-16 xl:py-[86px]">
          <div>
            @if(isset($article->data_content['products-title']) && is_string($article->data_content['products-title']))
            <div class="mb-6 md:mb-12">
              <h2 class="text-center text-2xl md:text-3xl xl:text-4xl lh-outline-none">{!! $article->data_content['products-title'] ?? '' !!}</h2>
            </div>
            @endif
            <div id="swiper-product-add" class="swiper overflow-visible">
              <div class="swiper-wrapper overflow-hidden">
                @foreach($article->products as $product)
                  <div class="swiper-slide product-item-swiper__slide h-auto pb-px">
                    <x-public.product-item id="{{ $product->id }}" class="w-full h-full flex flex-col justify-between" :product="$product"/>
                  </div>

                @endforeach
              </div>

              <div class="swiper-button-next swiper-buttom-outside backdrop-blur-sm"></div>
              <div class="swiper-button-prev swiper-buttom-outside backdrop-blur-sm"></div>
{{--              <div class="swiper-pagination swiper-pagination-outside swiper-pagination-dark md:hidden"></div>--}}
            </div>
          </div>
        </div>
      @endif
      <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-12 md:py-14 lg:py-16 xl:py-[86px]">
        <div>
          <div class="mb-6 md:mb-12">
            <h2 class="text-center m-headline-2 d-headline-2 lh-outline-none">Новости</h2>
          </div>
          <div>
            <div id="swiper-2" class="swiper swipre-1/3-2/3 overflow-visible">
              <div class="swiper-wrapper overflow-hidden">
                @if(isset($articles))
                  @foreach($articles as $key => $slide)
                    <div class="swiper-slide product-item-swiper__slide h-auto">
                      <x-public.blog-item id="{{ $slide->id }}" class="w-full h-full flex flex-col justify-between" :article="$slide"/>
                    </div>
                  @endforeach
                @endif
              </div>
              <div class="swiper-button-next swiper-buttom-outside backdrop-blur-sm !top-1/4"></div>
              <div class="swiper-button-prev swiper-buttom-outside backdrop-blur-sm !top-1/4"></div>
            </div>
          </div>
        </div>
      </div>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', function () {

      new Swiper('#swiper-2', {
        slidesPerView: 2, // на больших экранах
        spaceBetween: 24, // расстояние между слайдами
        breakpoints: {
          640: {
            slidesPerView: 2, // на мобильных устройствах
          },
          768: {
            slidesPerView: 3, // на мобильных устройствах
          },
        },
        preloadImages: false,
        lazy: false,
        cssMode: true,
        mousewheel: true,
        navigation: {
          nextEl: ".swiper-button-next",
          prevEl: ".swiper-button-prev",
        },
      });
    });

  </script>
  <script>
    new Swiper('#swiper-product-add', {
      slidesPerView: 2,
      spaceBetween: 24,
      mousewheel: {
        releaseOnEdges: true
      },
      preloadImages: false,
      lazy: true,
      cssMode: true,
      // pagination: {
      //   el: '.swiper-pagination',
      //   clickable: true,
      // },
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
      breakpoints: {
        640: {
          slidesPerView: 3,
        },
        768: {
          slidesPerView: 4,
        },
        1024: {
          slidesPerView: 4,
        },
      },
    });

  </script>
  <div class="pb-px pb-8"></div>
</x-app-layout>
