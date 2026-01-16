@section('title', $seo['title'] ?? config('app.name'))
<x-app-layout>
  <div class="max-w-[520px] mx-auto py-6 md:py-8 px-2">
    <h1
      class="font-normal text-[32px] leading-relaxed uppercase sm:text-center"
    >
      НОВОСТИ
    </h1>
  </div>
  <main>
    <section class="relative">
{{--      <div class="flex flex-row gap-2 mb-6 md:mb-10 draggable">--}}
{{--        @foreach($categories as $category)--}}
{{--          <div class="flex flex-col items-center px-[13px] max-w-[170px] relative @if($category->status == 2) opacity-40 pointer-events-none @endif">--}}
{{--            @if($category->status == 1)--}}
{{--              <a href="{{ route('blog.category', $category->slug) }}" class="block absolute w-full h-full left-0 top-0 z-10"></a>--}}
{{--            @endif--}}
{{--            @if(isset($category->data['image']['size']))--}}
{{--              <div class="item-square rounded-full overflow-hidden w-[86px]">--}}
{{--                <input type="hidden" data-id="productionImage" class="json-image"--}}
{{--                       value="{{ e(json_encode($category->data['image']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover" data-alt="{{ $category->name }}">--}}
{{--              </div>--}}
{{--            @endif--}}
{{--            <div class="text-center">{{ $category->name }}</div>--}}
{{--          </div>--}}
{{--        @endforeach--}}
{{--      </div>--}}
{{--      <div class="relative mb-6 md:mb-10">--}}
{{--      <div class="stories-container">--}}
{{--        <div class="stories-wrapper">--}}
{{--          @foreach($categories as $category)--}}
{{--            <div class="story max-w-[170px] w-auto">--}}
{{--              <div class="flex flex-col items-center px-[13px]  relative @if($category->status == 2) opacity-40 pointer-events-none @endif">--}}
{{--                @if($category->status == 1)--}}
{{--                  <a href="{{ route('blog.category', $category->slug) }}" class="block absolute w-full h-full left-0 top-0 z-10"></a>--}}
{{--                @endif--}}
{{--                @if(isset($category->data['image']['size']))--}}
{{--                  <div class="item-square rounded-full overflow-hidden w-[86px]">--}}
{{--                    <input type="hidden" data-id="productionImage" class="json-image"--}}
{{--                           value="{{ e(json_encode($category->data['image']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover" data-alt="{{ $category->name }}">--}}
{{--                  </div>--}}
{{--                @endif--}}
{{--                <div class="text-center">{{ $category->name }}</div>--}}
{{--              </div>--}}
{{--            </div>--}}
{{--          @endforeach--}}
{{--        </div>--}}
{{--      </div>--}}
{{--      </div>--}}
      <style>
        .stories-container {
          overflow-x: auto;
          white-space: nowrap;
          padding: 10px;
          -webkit-overflow-scrolling: touch;
          scrollbar-width: none; /* Firefox */
        }

        .stories-container::-webkit-scrollbar {
          display: none; /* Chrome/Safari */
        }

        .stories-wrapper {
          display: flex;
          gap: 10px;
        }

        .story {
          display: inline-block;
        }
      </style>
      <script>
        // const container = document.querySelector('.stories-container');
        // let isDown = false;
        // let startX, scrollLeft;
        //
        // container.addEventListener('mousedown', (e) => {
        //   isDown = true;
        //   container.classList.add('dragging');
        //   startX = e.pageX - container.offsetLeft;
        //   scrollLeft = container.scrollLeft;
        // });
        //
        // container.addEventListener('mouseleave', () => {
        //   isDown = false;
        //   container.classList.remove('dragging');
        // });
        //
        // container.addEventListener('mouseup', () => {
        //   isDown = false;
        //   container.classList.remove('dragging');
        // });
        //
        // container.addEventListener('mousemove', (e) => {
        //   if (!isDown) return;
        //   e.preventDefault();
        //   const x = e.pageX - container.offsetLeft;
        //   const walk = x - startX; // 1:1 скорость
        //   container.scrollLeft = scrollLeft - walk;
        // });
      </script>
      <div class="relative mb-6 md:mb-10">
        <div id="swiper-block-categories" class="swiper">
          <div class="swiper-wrapper">
            @foreach($categories as $category)
              <div class="swiper-slide max-w-[170px] w-auto">
                <div class="flex flex-col items-center px-[13px]  relative @if($category->status == 2) opacity-40 pointer-events-none @endif">
                  @if($category->status == 1)
                    <a href="{{ route('blog.category', $category->slug) }}" class="block absolute w-full h-full left-0 top-0 z-10"></a>
                  @endif
                  @if(isset($category->data['image']['size']))
                    <div class="item-square rounded-full overflow-hidden w-[86px]">
                      <input type="hidden" data-id="productionImage" class="json-image"
                             value="{{ e(json_encode($category->data['image']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover" data-alt="{{ $category->name }}">
                    </div>
                  @endif
                  <div class="text-center">{{ $category->name }}</div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
      <script>
        new Swiper('#swiper-block-categories', {
          slidesPerView: 'auto',
          spaceBetween: 8,
          mousewheel: {
            releaseOnEdges: true
          },
          freeMode: true,
                      @if(isset($product->style_page['productEffect']) && count($product->style_page['productEffect']) > 3)
                        navigation: {
                          nextEl: ".swiper-button-action-next",
                          prevEl: ".swiper-button-action-prev",
                        },
                        @endif
          breakpoints: {
            768: {
              spaceBetween: 8,
            },
            1024: {
              spaceBetween: 8,
            },
          },
        });

      </script>
      <div class="sm:hidden absolute top-0 bottom-0 right-0 pb-14 gradient-block  w-1/4 pointer-events-none bg-gradient-to-r from-white/0 to-white flex justify-end items-center pr-px">
        <svg width="23" height="16" viewBox="0 0 23 16" fill="none" xmlns="http://www.w3.org/2000/svg">
          <g filter="url(#filter0_b_9474_36336)">
            <path d="M5.96263 3L10.9166 8L5.96263 13L5.08329 12.1125L9.15796 8L5.08329 3.8875L5.96263 3Z" fill="#6C715C"/>
          </g>
          <g opacity="0.48" filter="url(#filter1_b_9474_36336)">
            <path d="M12.9626 3L17.9166 8L12.9626 13L12.0833 12.1125L16.158 8L12.0833 3.8875L12.9626 3Z" fill="#6C715C"/>
          </g>
          <defs>
            <filter id="filter0_b_9474_36336" x="-32" y="-32" width="80" height="80" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
              <feFlood flood-opacity="0" result="BackgroundImageFix"/>
              <feGaussianBlur in="BackgroundImageFix" stdDeviation="16"/>
              <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_9474_36336"/>
              <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_9474_36336" result="shape"/>
            </filter>
            <filter id="filter1_b_9474_36336" x="-25" y="-32" width="80" height="80" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
              <feFlood flood-opacity="0" result="BackgroundImageFix"/>
              <feGaussianBlur in="BackgroundImageFix" stdDeviation="16"/>
              <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_9474_36336"/>
              <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_9474_36336" result="shape"/>
            </filter>
          </defs>
        </svg>
      </div>

    </section>
    @foreach($articles as $article)
      <article class="mb-12">
        <div class="relative max-w-[520px] mx-auto mb-4 px-2">
          @if(isset($article->data_title['image']['size']))
            <div>
              <input type="hidden" data-id="productionImage" class="json-image"
                     value="{{ e(json_encode($article->data_title['image']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover" data-alt="{{ $article->title }}">
            </div>
          @endif
        </div>
        <div class="flex flex-col gap-5 px-2 max-w-[520px] mx-auto">
          <h3 class="font-light text-[32px] leading-none">
            {!! nl2br($article->title) !!}
          </h3>
          @if($article->data_title['short_description'] ?? false)
          <div class="italic font-semibold text-xl leading-tight text-myBrown">
            {!! $article->data_title['short_description'] !!}
          </div>
          @endif
          <a href="{{ route('blog.article', $article->slug) }}"
            class="block text-center w-full font-medium text-xl leading-none mx-auto py-3 border border-solid border-black"
          >
            Читать
          </a>
        </div>
      </article>
    @endforeach

    <section class="py-12">
      <div class="mb-12 mx-auto w-full max-w-[1000px]">
        @if(isset($content->image_data['bottomImage']['size']))
          <input type="hidden" data-id="bottomImage" class="json-image" value="{{ e(json_encode($content->image_data['bottomImage']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="w-full block object-cover">
        @endif
      </div>
    </section>
  </main>
</x-app-layout>
