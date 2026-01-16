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
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-6 sm:py-10 md:py-14 lg:py-16 xl:py-[86px]">
    <div class="flex flex-wrap -mx-2 sm:-mx-3 lg:-mx-4 -my-6 ">
      @if(isset($content->carousel_data['dermatologists']))
        @foreach($content->carousel_data['dermatologists'] as $key => $slide)
          <div class="award-item w-1/3 sm:w-1/4 px-2 sm:px-3 lg:px-4 py-6">
            <div class="technologist-item w-full">
              <div>
                @if(isset($slide['image']['size']))
                  <div class="technologist-image">
                    <input type="hidden" data-id="technologists-{{ $key }}" class="json-image"
                           value="{{ e(json_encode($slide['image']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover item-square square-1.36">
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
                                   value="{{ e(json_encode($image['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="swiper-image block object-cover item-square square-1.36">
                          </div>
                        </div>
                      @endforeach
                    </div>
                    <div class="swiper-pagination"></div>
                  </div>
                @endif
                <div class="mt-2 5">
                  <div class="lh-outline-none headline">
                    <div class="text-19 md:text-2xl font-medium mb-4 leading-none text-myGray">{!! str_replace(" ", "<br/>", $slide['name']) !!}</div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        @endforeach
      @endif
    </div>
  </div>
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
