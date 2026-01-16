@section('title', $seo['title'] ?? config('app.name'))
<x-app-layout>
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-6 sm:py-10 md:py-14 lg:py-16 xl:py-[86px]">
    <div>
      <div class="flex justify-center items-center mb-8 md:mb-10 lg:mb-12">
        <div class="border-t border-b border-myBrown w-[124px]"></div>
        <h2 class="text-center product-headline text-myBrown mx-6 lh-base">Отзывы {{ $product->name }}</h2>
        <div class="border-t border-b border-myBrown w-[124px]"></div>
      </div>
      @if($reviews->count())
        @foreach($reviews as $review)
          <div class="mx-auto flex-1 product-review bg-myGreen rounded-[16px] md:rounded-[20px] lg:rounded-[24px] p-6 md:p-9 lg:p-12 mb-8"
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
              <div class="relative txt-body overflow-hidden transition-all duration-500 ease-in-out">
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
            </div>
          </div>
        @endforeach
      <div class="p-2 d-text-body m-text-body">
        {{ $reviews->appends(request()->input())->links('pagination::tailwind-public') }}
      </div>
      @else
        <div class="p-6 text-center">
          <div class="d-headline-4 m-headline-3 text-gray-400">Пока нет отзывов</div>
        </div>
      @endif
      <div class="text-center mt-6 md:mt-9 lg:mt-12">
        <x-public.primary-button href="{{ route('product.index', $product->slug) }}" class="md:w-full max-w-[193px] md:max-w-[223px] lg:max-w-[253px] xl:max-w-[283px] md:text-2xl md:h-14">
          Вернуться к товару
        </x-public.primary-button>
      </div>
    </div>
  </div>

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
  <script>
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
</x-app-layout>
