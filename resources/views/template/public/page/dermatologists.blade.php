@section('title', $seo['title'] ?? config('app.name'))
<x-app-layout>
  <div class="md:flex p-0 md:flex-row-reverse">
    <div class="md:max-w-[40%] xl:max-w-[546px] w-full mx-auto flex flex-col">
      <div class="item-square mainImage square-0.91 w-full">
        @if(isset($content->image_data['mainImage']['size']))
          <input type="hidden" data-id="mainImage" class="json-image" value="{{ e(json_encode($content->image_data['mainImage']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="w-full h-full block object-cover">
        @endif
      </div>
    </div>
    <div class="px-2 md:px-4 lg:pl-16 flex-1 flex items-center">
      <div class="text-center md:text-left pt-5 pb-10 md:pb-0 md:pt-0">
        <h1 class="headline-1 mb-4 md:mb-6">{{ $content->text_data['headline1'] ?? '' }}</h1>
        @if(isset($content->text_data['subtitle1']))
          <div class="m-text-body d-text-body">{!! nl2br($content->text_data['subtitle1']) !!}</div>
        @endif
      </div>
    </div>
  </div>
  <div class="px-2 sm:px-4 bg-myGreen md:px-8 lg:px-14 xl:px-16 py-6 sm:py-10 md:py-14 lg:py-16 xl:py-[86px]">
    <div>
      <div class="border-t border-myBrown mb-6 mx-auto w-[186px] sm:w-[292px]"></div>
      <div class="text-center mx-auto sm:text-xl md:text-2xl text-lg lh-outline-none uppercase">{!! $content->text_data['cosmeceuticalsText'] ?? '' !!}
      </div>
      <div class="border-t border-myBrown mt-6 mx-auto w-[248px] sm:w-[353px]"></div>
    </div>
  </div>
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 flex flex-wrap mt-16">
    <div class="w-full md:w-1/2">
      <h2 class="text-center m-headline-2 d-headline-2">Химики-технологи</h2>
      @if(isset($content->carousel_data['technologists']))
        <div id="swiper-technologists" class="swiper technologists-swiper px-0 sm:px-6 lg:px-12 xl:px-[55px] py-8 sm:py-6">
          <div class="swiper-wrapper">
            @foreach($content->carousel_data['technologists'] as $key => $slide)
              <div class="swiper-slide">
              <div class="technologist-item w-full">
                <div class="flex flex-col justify-between">
                  <div class="mb-4">
                    <div class="lh-outline-none headline">
                      <div class="text-2xl md:text-28 lg:text-32 font-medium leading-none sm:leading-1.6 text-myGray">{{ $slide['name'] }}</div>
                    </div>
                  </div>
                  <div>
                    @if(isset($slide['image']['size']))
                      <div class="technologist-image">
                        <input type="hidden" data-id="technologists-{{ $key }}" class="json-image"
                               value="{{ e(json_encode($slide['image']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover item-square square-1.22">
                      </div>
                    @elseif(isset($slide['image']) && is_array($slide['image']))
                      <div id="swiper-technologists-{{ $key }}" class="swiper technologists-swiper technologist-image">
                        <div class="swiper-wrapper">
                          @foreach($slide['image'] as $image_key => $image)
                            @if(!isset($image['size']))
                              @continue
                            @endif
                            <div class="swiper-slide">
                              <div>
                                <input type="hidden" data-id="technologists-{{ $key }}-{{ $image_key }}" class="json-image"
                                       value="{{ e(json_encode($image['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="swiper-image block object-cover item-square square-1.22">
                              </div>
                            </div>
                          @endforeach
                        </div>
                        <div class="swiper-pagination"></div>
                      </div>
                    @endif
                    <div class="space-y-4 lh-none text-xl md:text-2xl italic my-6 md:my-8 text-myGray">
                      {!! $slide['description'] !!}
                    </div>
                  </div>
                </div>
              </div>
              </div>
            @endforeach
          </div>
          <div class="swiper-pagination"></div>
        </div>
      @endif
    </div>
    <div class="w-full md:w-1/2">
      <h2 class="text-center m-headline-2 d-headline-2">Врачи-дерматологи</h2>
      @if(isset($content->carousel_data['dermatologists']))
        <div id="swiper-technologists" class="swiper technologists-swiper px-0 sm:px-6 lg:px-12 xl:px-[55px] py-8 sm:py-6">
          <div class="swiper-wrapper">
            @foreach($content->carousel_data['dermatologists'] as $key => $slide)
              <div class="swiper-slide">
                <div class="technologist-item w-full">
                  <div class="flex flex-col justify-between">
                    <div class="mb-4">
                      <div class="lh-outline-none headline">
                        <div class="text-2xl md:text-28 lg:text-32 font-medium leading-none sm:leading-1.6 text-myGray">{{ $slide['name'] }}</div>
                      </div>
                    </div>
                    <div>
                      @if(isset($slide['image']['size']))
                        <div class="technologist-image">
                          <input type="hidden" data-id="technologists-{{ $key }}" class="json-image"
                                 value="{{ e(json_encode($slide['image']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover item-square square-1.22">
                        </div>
                      @elseif(isset($slide['image']) && is_array($slide['image']))
                        <div id="swiper-technologists-{{ $key }}" class="swiper technologists-swiper technologist-image">
                          <div class="swiper-wrapper">
                            @foreach($slide['image'] as $image_key => $image)
                              @if(!isset($image['size']))
                                @continue
                              @endif
                              <div class="swiper-slide">
                                <div>
                                  <input type="hidden" data-id="technologists-{{ $key }}-{{ $image_key }}" class="json-image"
                                         value="{{ e(json_encode($image['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="swiper-image block object-cover item-square square-1.22">
                                </div>
                              </div>
                            @endforeach
                          </div>
                          <div class="swiper-pagination"></div>
                        </div>
                      @endif
                      <div class="space-y-4 lh-none text-xl md:text-2xl italic my-6 md:my-8 text-myGray">
                        {!! $slide['description'] !!}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
          <div class="swiper-pagination"></div>
        </div>
      @endif
    </div>
  </div>
  <div class="text-center flex justify-center">
    <x-public.primary-button href="{{ route('page.dermatologists.all') }}">Посмотреть всех врачей</x-public.primary-button>
  </div>
  <div class="mb-12 mt-8">
  <div class="lh-outline-none text-center d-text-body m-text-body text-center">
    <a @if(!auth()->check()) href="javascript:;" data-src="#dermatologists-modal" data-fancybox-no-close-btn @else href="https://t.me/dermatolog_lm_bot" target="_blank" @endif class="text-myBrown uppercase underline underline-offset-4 hover:no-underline outline-none">проконсультироваться с <br/>врачом-дерматологом</a>
  </div>
  </div>
  <script>
    new Swiper('.technologists-swiper', {
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
    function checkPagination(){
      var swipers = document.querySelectorAll('.technologists-swiper');

      swipers.forEach(function(swiper) {
        var firstSlide = swiper.querySelector('.swiper-slide');
        var imageBlock = firstSlide.querySelector('.technologist-image');
        var swiperContainer = swiper.closest('.swiper');

        if (imageBlock && swiperContainer) {
          var imageRect = imageBlock.getBoundingClientRect();
          var swiperRect = swiperContainer.getBoundingClientRect();

          // Рассчитываем относительные координаты
          var relativeTop = imageRect.top - swiperRect.top + imageRect.height - 20;
          var relativeLeft = (imageRect.left - swiperRect.left) + (imageRect.width / 2);

          var pagination = swiper.querySelector('.swiper-pagination');
          if (pagination) {
            pagination.style.position = 'absolute';
            pagination.style.top = relativeTop + 'px';
            pagination.style.left = relativeLeft + 'px';
            pagination.style.transform = 'translateX(-50%)';
          }
        }
      });
    }
    window.onload = checkHeadlineHeight;
    function checkHeadlineHeight(){
      var swiperWrappers = document.querySelectorAll('.swiper-wrapper');
      var maxHeight = 0;
      swiperWrappers.forEach(function(wrapper) {
        // Находим все .headline внутри текущего .swiper-wrapper
        var headlines = wrapper.querySelectorAll('.technologist-item .headline');

        // Находим самый высокий .headline
        headlines.forEach(function(headline) {
          headline.style.height = ''
          var headlineHeight = headline.offsetHeight;
          if (headlineHeight > maxHeight) {
            maxHeight = headlineHeight;
          }
        });

        // Устанавливаем высоту всех .headline равной максимальной
        headlines.forEach(function(headline) {
          headline.style.height = maxHeight + 'px';
        });
      });
    }
    window.onload = () => {
      checkHeadlineHeight();
      checkPagination();
    }
    window.addEventListener('resize', function() {
      checkHeadlineHeight();
      checkPagination();
    });
  </script>
  @auth
    <script src="https://livechatv2.chat2desk.com/packs/ie-11-support.js"></script>
    <script>
      window.chat24_token = "d3663b5228d0bdab21fb6c311827e8be";
      window.chat24_url = "https://livechatv2.chat2desk.com";
      window.chat24_socket_url ="wss://livechatv2.chat2desk.com/widget_ws_new";
      window.chat24_show_new_wysiwyg = "true";
      window.chat24_static_files_domain = "https://storage.chat2desk.com/";
      window.lang = "ru";
      window.fetch("".concat(window.chat24_url, "/packs/manifest.json?nocache=").concat(new Date().getTime())).then(function (res) {
        return res.json();
      }).then(function (data) {
        var chat24 = document.createElement("script");
        chat24.type = "text/javascript";
        chat24.async = true;
        chat24.src = "".concat(window.chat24_url).concat(data["application.js"]);
        document.body.appendChild(chat24);
      });
    </script>

  @endauth
</x-app-layout>
