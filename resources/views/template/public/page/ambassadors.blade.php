<x-app-layout>

  @include('_parts.public.pageTopBlock')
  @include('_parts.public.pageTextBetweenLines', ['class' => 'max-w-[720px]'])
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-6 sm:py-10 md:py-14 lg:py-16 xl:py-[86px]">
    <div class="flex flex-wrap mx-0 sm:-mx-6 lg:-mx-12 xl:-mx-[55px] sm:-my-6 -my-12">
      @if(isset($content->carousel_data['ourAwards']))
        @foreach($content->carousel_data['ourAwards'] as $key => $slide)
          <div class="w-full sm:w-1/2 px-0 sm:px-6 lg:px-12 xl:px-[55px] py-12 sm:py-6">
            <div>
              <div class="lh-outline-none">
                <div class="text-2xl md:text-28 lg:text-32 font-medium mb-4 leading-none sm:leading-1.6">{{ $slide['name'] }}</div>
              </div>
              <div class="font-light italic text-xl lg:text-2xl text-myGray mb-4 md:mb-6 lg:mb-8 lh-none">{{ $slide['date'] }}</div>
              @if(isset($slide['image']['size']))
                <div class="md-item-square square-1.09">
                  <input type="hidden" data-id="ourAwards-{{ $key }}" class="json-image"
                         value="{{ e(json_encode($slide['image']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover">
                </div>
              @elseif(isset($slide['image']) && is_array($slide['image']))
                <div id="swiper-ambassadors-{{ $key }}" class="swiper ambassadors-swiper">
                  <div class="swiper-wrapper">
                    @foreach($slide['image'] as $image_key => $image)
                      <div class="swiper-slide">
                        <div class="md-item-square square-1.09">
                          <input type="hidden" data-id="ourAwards-{{ $key }}-{{ $image_key }}" class="json-image"
                                 value="{{ e(json_encode($image['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="swiper-image block object-cover">
                        </div>
                      </div>
                    @endforeach
                  </div>
                  <div class="swiper-pagination"></div>
                </div>
              @endif
              <div class="text-center">
                <h3 class="lh-none text-xl md:text-2xl italic my-6 md:my-8 text-myGray">{{ $slide['description'] }}</h3>
                <a href="{{ $slide['link'] }}" class="lh-none text-myBrown text-xl md:text-2xl underline hover:no-underline">Перейти</a>
              </div>
            </div>
          </div>
        @endforeach
      @endif
    </div>
  </div>
  <div class="text-center text-myBrown md:py-6 lg:py-12 pb-12">
    <div class="headline-1">LE MOUSSE</div>
    <div class="border-t border-myBrown mt-6 mx-auto w-[135px] lg:w-[255px]"></div>
  </div>
  @include('_parts.public.pageCategories')

  <script>
    new Swiper('.ambassadors-swiper', {
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
</x-app-layout>
