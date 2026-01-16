@section('title', $seo['title'] ?? config('app.name'))
<x-app-layout>

  @include('_parts.public.pageTopBlock')
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 pt-6 pb-0 sm:py-10 md:py-14 lg:py-16 xl:py-[86px]">
      <div class="flex flex-wrap align-end justify-center space-y-[80px] md:space-y-0 md:space-x-0 md:space-x-[128px] mx-0 md:-mx-3 md:-my-6">
        @if(isset($content->carousel_data['certificatesRed']))
          <div class="w-full md:w-1/2 px-0 md:px-3 md:py-6 max-w-[390px]">
            <div  class="text-center italic text-myBrown font-medium text-2xl md:text-27 mb-4 flex-1 flex flex-col justify-center items-center">На производстве ООО «Ле Мусс» внедрена система менеджмента качества, что соответствует международным нормам и подтверждается сертификатом соответствия <span class="whitespace-nowrap">ГОСТ&nbsp;Р&nbsp;ИСО&nbsp;9001-2015</span></div>
            <div class="swiper-container relative">
              <div class="swiper-wrapper">
                @foreach($content->carousel_data['certificatesRed'] as $key => $certificate)
                  <div class="swiper-slide">
                    <div class="item-certificate flex flex-col items-center justify-between h-full">
                      {{--                  <div>{{ $certificate['name'] }}</div>--}}
                      @if(isset($certificate['files'])&&!empty($certificate['files']))
                        @php($i = 0)
                        @foreach($certificate['files'] as $file)
                          @if(isset($file['thumb']) && $i == 0)
                            <a href="javascript:;" data-src="{{ $file['file'] }}" class="block relative w-full item-square item-certificate" data-fancybox="{{ $key }}">

                              <img src="{{ $file['thumb'] }}" alt="" class="block object-cover mx-auto w-full">

                            </a>
                          @else
                            <a href="javascript:;" data-src="{{ $file['file'] }}" style="opacity: 0" data-fancybox="{{ $key }}"></a>
                          @endif
                          @php($i++)
                        @endforeach
                      @elseif(isset($certificate['file']))
                        <a href="javascript:;" data-src="{{ $certificate['file'] }}" class="block relative w-full item-square item-certificate" data-fancybox="{{ $key }}">
                          @if(isset($certificate['thumb']))
                            <img src="{{ $certificate['thumb'] }}" alt="" class="block object-cover mx-auto w-full">
                          @endif
                        </a>
                      @endif

                    </div>
                  </div>
                @endforeach
              </div>
              <div class="swiper-pagination"></div>
            </div>
          </div>
        @endif
          @if(isset($content->carousel_data['certificatesGreen']))
            <div class="flex flex-col justify-end w-full md:w-1/2 px-0 md:px-3 md:py-6 max-w-[390px]">
              <div  class="italic text-center text-myBrown  font-medium text-2xl md:text-27 mb-4 flex-1 flex flex-col justify-center items-center">Сертификаты соответствия СДС «Органический продукт России»</div>
              <div class="swiper-container relative">
                <div class="swiper-wrapper">
                  @foreach($content->carousel_data['certificatesGreen'] as $key => $certificate)
                    <div class="swiper-slide">
                      <div class="item-certificate flex flex-col items-center justify-between h-full">
                        {{--                  <div>{{ $certificate['name'] }}</div>--}}
                        @if(isset($certificate['files'])&&!empty($certificate['files']))
                          @php($i = 0)
                          @foreach($certificate['files'] as $file)
                            @if(isset($file['thumb']) && $i == 0)
                              <a href="javascript:;" data-src="{{ $file['file'] }}" class="block relative w-full item-square item-certificate" data-fancybox="{{ $key }}">

                                <img src="{{ $file['thumb'] }}" alt="" class="block object-cover mx-auto w-full">

                              </a>
                            @else
                              <a href="javascript:;" data-src="{{ $file['file'] }}" style="opacity: 0" data-fancybox="{{ $key }}"></a>
                            @endif
                            @php($i++)
                          @endforeach
                        @elseif(isset($certificate['file']))
                          <a href="javascript:;" data-src="{{ $certificate['file'] }}" class="block relative w-full item-square item-certificate" data-fancybox="{{ $key }}">
                            @if(isset($certificate['thumb']))
                              <img src="{{ $certificate['thumb'] }}" alt="" class="block object-cover mx-auto w-full">
                            @endif
                          </a>
                        @endif
                        {{--                  @if(isset($certificate['description']))--}}
                        {{--                    <div class="md:hidden subtitle-1 text-myGray mb-4 flex-1 flex flex-col justify-center items-center text-center mt-6">{{ $certificate['description'] }}</div>--}}
                        {{--                  @endif--}}
                      </div>
                    </div>
                  @endforeach
                </div>
                <div class="swiper-pagination"></div>
              </div>
            </div>
          @endif
          @if(isset($content->carousel_data['certificatesKids']))
            <div class="flex flex-col justify-end w-full md:w-1/2 px-0 md:px-3 md:py-6 max-w-[390px]">
              <div  class="italic text-center text-myBrown  font-medium text-2xl md:text-27 mb-4 flex-1 flex flex-col justify-center items-center">Свидетельства о государственной регистрации косметической продукции</div>
              <div class="swiper-container relative">
                <div class="swiper-wrapper">
                  @foreach($content->carousel_data['certificatesKids'] as $key => $certificate)
                    <div class="swiper-slide">
                      <div class="item-certificate flex flex-col items-center justify-between h-full">
                        {{--                  <div>{{ $certificate['name'] }}</div>--}}
                        @if(isset($certificate['files'])&&!empty($certificate['files']))
                          @php($i = 0)
                          @foreach($certificate['files'] as $file)
                            @if(isset($file['thumb']) && $i == 0)
                              <a href="javascript:;" data-src="{{ $file['file'] }}" class="block relative w-full item-square item-certificate" data-fancybox="{{ $key }}">

                                <img src="{{ $file['thumb'] }}" alt="" class="block object-cover mx-auto w-full">

                              </a>
                            @else
                              <a href="javascript:;" data-src="{{ $file['file'] }}" style="opacity: 0" data-fancybox="{{ $key }}"></a>
                            @endif
                            @php($i++)
                          @endforeach
                        @elseif(isset($certificate['file']))
                          <a href="javascript:;" data-src="{{ $certificate['file'] }}" class="block relative w-full item-square item-certificate" data-fancybox="{{ $key }}">
                            @if(isset($certificate['thumb']))
                              <img src="{{ $certificate['thumb'] }}" alt="" class="block object-cover mx-auto w-full">
                            @endif
                          </a>
                        @endif
                        {{--                  @if(isset($certificate['description']))--}}
                        {{--                    <div class="md:hidden subtitle-1 text-myGray mb-4 flex-1 flex flex-col justify-center items-center text-center mt-6">{{ $certificate['description'] }}</div>--}}
                        {{--                  @endif--}}
                      </div>
                    </div>
                  @endforeach
                </div>
                <div class="swiper-pagination"></div>
              </div>
            </div>
          @endif
      </div>

  </div>
  <div class="mt-[80px] md:mt-0 px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-6 sm:py-10 md:py-14 lg:py-16 xl:py-[86px]">
    <div  class="text-center italic font-medium text-myBrown text-2xl md:text-27 flex-1 flex flex-col justify-center items-center text-center mb-8">Декларации соответствия каждой единицы продукции</div>
    <div id="certificates" class="flex flex-wrap mx-0 sm:-mx-3 sm:-my-6">
      @if(isset($content->carousel_data['certificates']))
        @foreach($content->carousel_data['certificates'] as $key => $certificate)
          <div class="w-1/2 sm:w-1/4 md:w-1/5 px-0 sm:px-3 sm:py-6">
            <div class="item-certificate flex flex-col items-center justify-between h-full">
{{--              <div class="subtitle-1 text-myBrown mb-4 flex-1 flex flex-col justify-center items-center text-center">{{ $certificate['name'] }}</div>--}}
              @if(isset($certificate['files'])&&!empty($certificate['files']))
                @php($i = 0)
                @foreach($certificate['files'] as $file)
                  @if(isset($file['thumb']) && $i == 0)
                    <a href="javascript:;" data-src="{{ $file['file'] }}" class="block relative w-full item-square item-certificate" data-fancybox="{{ $key }}">

                      <img src="{{ $file['thumb'] }}" alt="" class="block object-cover mx-auto w-full">

                    </a>
                  @else
                    <a href="javascript:;" data-src="{{ $file['file'] }}" style="opacity: 0" data-fancybox="{{ $key }}"></a>
                  @endif
                  @php($i++)
                @endforeach
              @elseif(isset($certificate['file']))
                <a href="javascript:;" data-src="{{ $certificate['file'] }}" class="block relative w-full item-square item-certificate" data-fancybox="{{ $key }}">
                  @if(isset($certificate['thumb']))
                    <img src="{{ $certificate['thumb'] }}" alt="" class="block object-cover mx-auto w-full">
                  @endif
                </a>
              @endif
              @if(isset($certificate['description']))
              <div class="md:hidden subtitle-1 text-myGray mb-4 flex-1 flex flex-col justify-center items-center text-center mt-6">{{ $certificate['description'] }}</div>
              @endif
            </div>
          </div>
        @endforeach
      @endif

    </div>
  </div>

  <span class="hidden item-square square-0.14 bg-gradient-to-t sm:hidden from-black to-transparent w-full !absolute left-0 bottom-0 block opacity-40"></span>
  @if(isset($content->text_data['greenLine']))
  <div class="py-6 px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 bg-myGreen2 text-white text-xl sm:text-2xl md:text-4xl lg:text-5xl !leading-tight italic font-medium flex justify-center text-center items-center">
    {!! nl2br($content->text_data['greenLine']) !!}
  </div>
  @endif
  @include('_parts.public.pageCategories')

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      if (window.innerWidth < 768) {
        // Скрываем основной блок
        let certificatesBlock = document.getElementById('certificates');
        certificatesBlock.style.display = 'none';

        // Создаем контейнер для Swiper
        let swiperContainer = document.createElement('div');
        swiperContainer.className = 'swiper-container relative';
        swiperContainer.innerHTML = `
          <div class="swiper-wrapper"></div>
          <div class="swiper-pagination"></div>
        `;

        // Копируем содержимое в Swiper
        let swiperWrapper = swiperContainer.querySelector('.swiper-wrapper');
        Array.from(certificatesBlock.children).forEach(child => {
          let swiperSlide = document.createElement('div');
          swiperSlide.className = 'swiper-slide';
          swiperSlide.innerHTML = child.innerHTML;
          let links = swiperSlide.querySelectorAll('a')
          links.forEach(link => {
            link.dataset.fancybox = 'swiper-'+link.dataset.fancybox
          })
          swiperWrapper.appendChild(swiperSlide);
        });

        // Добавляем Swiper на страницу
        certificatesBlock.parentNode.insertBefore(swiperContainer, certificatesBlock);


        // let certificatesBlockGreen = document.getElementById('certificatesGreen');
        // certificatesBlockGreen.style.display = 'none';

        // Создаем контейнер для Swiper
        // let swiperContainerGreen = document.createElement('div');
        // swiperContainerGreen.className = 'swiper-container relative';
        // swiperContainerGreen.innerHTML = `
        //   <div class="swiper-wrapper"></div>
        //   <div class="swiper-pagination"></div>
        // `;
        //
        // // Копируем содержимое в Swiper
        // let swiperWrapperGreen = swiperContainerGreen.querySelector('.swiper-wrapper');
        // Array.from(certificatesBlockGreen.children).forEach(child => {
        //   let swiperSlide = document.createElement('div');
        //   swiperSlide.className = 'swiper-slide';
        //   swiperSlide.innerHTML = child.innerHTML;
        //   let links = swiperSlide.querySelectorAll('a')
        //   links.forEach(link => {
        //     link.dataset.fancybox = 'swiper-'+link.dataset.fancybox
        //   })
        //   swiperWrapperGreen.appendChild(swiperSlide);
        // });
        //
        // // Добавляем Swiper на страницу
        // certificatesBlockGreen.parentNode.insertBefore(swiperContainerGreen, certificatesBlockGreen);
        // Инициализация Swiper

      }
      new Swiper('.swiper-container', {
        slidesPerView: 1,
        lazy: true,
        cssMode: true,
        grabCursor: true,
        pagination: {
          el: '.swiper-pagination',
          clickable: true,
        },
      });
    });

  </script>
</x-app-layout>
