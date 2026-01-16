@section('title', $seo['title'] ?? config('app.name'))
<x-app-layout>

  @include('_parts.public.pageTopBlock')
  @include('_parts.public.pageTextBetweenLines', ['class' => 'max-w-[720px]'])
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-6 sm:py-10 md:py-14 lg:py-16 xl:py-[86px]">
    <div class="flex flex-wrap mx-0 sm:-mx-6 lg:-mx-12 xl:-mx-[55px] sm:-my-6 -my-12">
      @if(isset($content->carousel_data['ourAwards']))
        @foreach($content->carousel_data['ourAwards'] as $key => $slide)
          <div class="award-item w-full sm:w-1/2 px-0 sm:px-6 lg:px-12 xl:px-[55px] py-12 sm:py-6">
            <div>
              <div class="lh-outline-none headline">
                <div class="text-2xl md:text-28 lg:text-32 font-medium mb-4 leading-none sm:leading-1.6">{{ $slide['name'] }}</div>
              </div>
              <div class="font-light italic text-xl lg:text-2xl text-myGray mb-4 md:mb-6 lg:mb-8 lh-none">{{ $slide['date'] }}</div>
              @if(isset($slide['image']['size']))
                <div class="md-item-square square-1.09">
                  <input type="hidden" data-id="ourAwards-{{ $key }}" class="json-image"
                         value="{{ e(json_encode($slide['image']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover">
                </div>
              @elseif(isset($slide['image']) && is_array($slide['image']))
                <div id="swiper-awards-{{ $key }}" class="swiper awards-swiper">
                  <div class="swiper-wrapper">
                    @foreach($slide['image'] as $image_key => $image)
                      @if(!isset($image['size']))
                        @continue
                      @endif
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
    function checkHeadlineHeight(){
      if (window.innerWidth > 640) {
        var awardItems = document.querySelectorAll('.award-item');
        for (var i = 0; i < awardItems.length; i += 2) {
          var height1 = awardItems[i].querySelector('.headline').offsetHeight;
          var height2 = awardItems[i + 1] ? awardItems[i + 1].querySelector('.headline').offsetHeight : 0;
          var maxHeight = Math.max(height1, height2);

          awardItems[i].querySelector('.headline').style.height = maxHeight + 'px';
          if (awardItems[i + 1]) {
            awardItems[i + 1].querySelector('.headline').style.height = maxHeight + 'px';
          }
        }
      }
    }
    window.onload = checkHeadlineHeight;
    window.addEventListener('resize', function() {
      checkHeadlineHeight();
    });
    document.addEventListener("DOMContentLoaded", function() {
      new Swiper('.awards-swiper', {
        // // Optional parameters
        // direction: 'vertical',
        // loop: true,
        slidesPerView: 1,
        preloadImages: false,
        lazy: false,
        cssMode: false,
        mousewheel: true,
        initialSlide: 0,
        pagination: {
          el: ".swiper-pagination",
          clickable: true,
        },
        breakpoints: {
          640: {
            cssMode: true,
          }
        },
      });
    });


  </script>
</x-app-layout>
