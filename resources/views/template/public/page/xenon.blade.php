@section('title', $seo['title'] ?? config('app.name'))
<x-app-layout>

  @include('_parts.public.pageTopBlock')
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 pt-6 sm:py-10 md:py-14 lg:py-16 xl:py-[86px]">
    <div class="md:flex md:flex-row">
      <div class="mb-6 md:pr-14 lg:pr-16 xl:pr-20 flex-1 flex items-center">
        <div class="text-center md:text-left pt-12 md:pt-0">
          <h1 class="headline-1 text-myBrown">Для чего?</h1>
          <div class="border-t border-myBrown my-6 w-[135px] mx-auto md:mx-0"></div>
          <div class="d-text-body m-text-body text-center md:text-left space-y-6">
            {!! $content->text_data['fofWhat'] ?? '' !!}
          </div>
        </div>
      </div>
      @if(isset($content->image_data['forWhat']['size']))
        <div class="md:max-w-[25.91463415%] mx-auto w-full flex flex-col">
          <div class="item-square imgforWhat1 w-full">
            <input type="hidden" data-id="forWhat" class="json-image"
                   value="{{ e(json_encode($content->image_data['forWhat']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover" data-img-class="absolute left-0 top-0 bottom-0 right-0 h-full w-full">
          </div>
        </div>
      @endif
    </div>
  </div>
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 pt-6 sm:py-10 md:py-14 lg:py-16 xl:py-[86px]">
    <div class="d-text-body m-text-body text-center md:text-left space-y-6 mb-6 md:mb-0">
      {!! $content->text_data['aboutXenon'] ?? '' !!}
    </div>
  </div>
  @if(isset($content->text_data['greenLine']))
    <div class="py-6 px-2 sm:px-4 md:px-8 bg-myGreen3 text-white text-xl sm:text-2xl md:text-32 !leading-1.6 !md:leading-tight italic font-medium flex justify-center text-center items-center">
      {!! nl2br($content->text_data['greenLine']) !!}
    </div>
  @endif
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 pt-6 sm:py-10 md:py-14 lg:py-16 xl:py-[86px]">
    <div class="text-center md:text-left">
      <h1 class="d-headline-1 m-headline-3 text-myBrown mb-6 md:mb-0">{{ $content->text_data['headline2'] ?? '' }}</h1>
      <div class="hidden md:block border-t border-myBrown my-6 w-[135px] mx-auto md:mx-0"></div>
      <div class="pl-5 md:pl-0 d-text-body m-text-body text-left space-y-6 !leading-1.6 !md:leading-tight">
        {!! $content->text_data['headline2text'] ?? '' !!}
      </div>
      <div class="-mx-2 md:mx-auto py-6 px-2 bg-myBrown mt-6 bg-opacity-[64%] uppercase cormorantInfant text-white text-center text-xl sm:text-2xl md:text-32 !leading-1.6 !md:leading-tight italic font-medium">
        {!! $content->text_data['beigeText'] ?? '' !!}
      </div>
    </div>
  </div>
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 pt-6 sm:py-10 md:py-14 lg:py-16 xl:py-[86px]">
    <div class="md:flex md:flex-row-reverse">
      @if(isset($content->image_data['whatProductHaveTo']['size']))
        <div class="md:max-w-[30.48780488%] mx-auto w-full flex flex-col">
          <div class="item-square imgwhatProductHaveTo w-full">
            <input type="hidden" data-id="whatProductHaveTo" class="json-image"
                   value="{{ e(json_encode($content->image_data['whatProductHaveTo']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover" data-img-class="absolute left-0 top-0 bottom-0 right-0 h-full w-full">
          </div>
        </div>
      @endif
      <div class="md:pr-14 lg:pr-16 xl:pr-20 flex-1 flex items-center">
        <div class="text-center md:text-left pt-6 md:pt-0">
          <h1 class="d-headline-1 m-headline-3 text-myBrown">{{ $content->text_data['headline3'] ?? '' }}</h1>
          <div class="border-t border-myBrown my-6 w-[135px] mx-auto md:mx-0"></div>
          <div class="d-text-body m-text-body text-center md:text-left space-y-6">
            {!! $content->text_data['headline3text'] ?? '' !!}
          </div>
        </div>
      </div>
    </div>

    <div class="-mx-2 md:mx-auto py-6 px-2 bg-myBrown mt-6 bg-opacity-[64%] italic text-white text-center text-xl sm:text-2xl md:text-32 !leading-1.6 !md:leading-tight italic font-medium hidden-br md:show-br">
      {!! isset($content->text_data['beigeText2']) ? nl2br($content->text_data['beigeText2']) : '' !!}
    </div>
  </div>
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 pt-6 sm:py-10 md:py-14 lg:py-16 xl:py-[86px]">
    <div class="d-text-body m-text-body text-center md:text-left space-y-6">
      {!! $content->text_data['aboutXenon2'] ?? '' !!}
    </div>
    <div class="space-y-6">
      @isset($content->text_data['beigeTextAboutXenon1'])
      <div class="md:hidden-br -mx-2 md:mx-auto py-6 px-2 bg-myBrown mt-6 bg-opacity-[64%] italic text-white text-center text-xl sm:text-2xl md:text-32 !leading-1.6 !md:leading-tight italic font-medium">
        {!! isset($content->text_data['beigeTextAboutXenon1']) ? nl2br($content->text_data['beigeTextAboutXenon1']) : '' !!}
      </div>
      @endisset
      @isset($content->text_data['beigeTextAboutXenon2'])
      <div class="md:hidden-br -mx-2 md:mx-auto py-6 px-2 bg-myBrown mt-6 bg-opacity-[64%] italic text-white text-center text-xl sm:text-2xl md:text-32 !leading-1.6 !md:leading-tight italic font-medium">
        {!! isset($content->text_data['beigeTextAboutXenon2']) ? nl2br($content->text_data['beigeTextAboutXenon2']) : '' !!}
      </div>
      @endisset
      @isset($content->text_data['beigeTextAboutXenon3'])
      <div class="md:hidden-br -mx-2 md:mx-auto py-6 px-2 bg-myBrown mt-6 bg-opacity-[64%] italic text-white text-center text-xl sm:text-2xl md:text-32 !leading-1.6 !md:leading-tight italic font-medium">
        {!! isset($content->text_data['beigeTextAboutXenon3']) ? nl2br($content->text_data['beigeTextAboutXenon3']) : '' !!}
      </div>
      @endisset
    </div>
  </div>
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-[56px] lg:py-16 xl:py-[86px]">
    <div>
      <div class="border-t border-myBrown mb-[56px] md:mb-12 mx-auto w-[135px]"></div>
      <div class="text-center mx-auto sm:text-xl md:text-2xl text-lg lh-outline-none">{!! $content->text_data['cosmeceuticalsText'] ?? '' !!}
      </div>
      <div class="border-t border-myBrown mt-[56px] md:mt-12 mx-auto w-[135px]"></div>
    </div>
  </div>

  @if(isset($content->text_data['greenLine2']))
    <div class="py-6 px-2 sm:px-4 md:px-8 bg-myGreen3 text-white text-xl sm:text-2xl md:text-32 !leading-1.6 !md:leading-tight italic font-medium flex justify-center text-center items-center">
      {!! nl2br($content->text_data['greenLine2']) !!}
    </div>
  @endif
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 pt-6 sm:py-10 md:py-14 lg:py-16 xl:py-[86px]">
    <div class="d-text-body m-text-body space-y-6">
      {!! $content->text_data['aboutXenon3'] ?? '' !!}
    </div>
  </div>
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 pt-6 pb-12 sm:py-10 md:py-14 lg:py-16 xl:py-[86px]">
    <div class="flex flex-col-reverse md:flex-row">
      <div class="md:pr-14 lg:pr-16 xl:pr-20 flex-1 flex items-center">
        <div class="text-center md:text-left pt-6 md:pt-0">
          <h1 class="d-headline-1 m-headline-3 text-myBrown">Более подробно об исследованиях</h1>
          <div class="border-t border-myBrown my-6 w-[135px] mx-auto md:mx-0"></div>
          <div class="d-text-body m-text-body text-myBrown text-left space-y-6">
            {!! $content->text_data['linksMoreInfo'] ?? '' !!}
          </div>
        </div>
      </div>
      @if(isset($content->image_data['moreInfo']['size']))
        <div class="md:max-w-[30.48780488%] mx-auto w-full flex flex-col">
          <div class="item-square imgwhatProductHaveTo w-full">
            <input type="hidden" data-id="moreInfo" class="json-image"
                   value="{{ e(json_encode($content->image_data['moreInfo']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover" data-img-class="absolute left-0 top-0 bottom-0 right-0 h-full w-full">
          </div>
        </div>
      @endif
    </div>
  </div>
  @include('_parts.public.pageCategories')

</x-app-layout>
