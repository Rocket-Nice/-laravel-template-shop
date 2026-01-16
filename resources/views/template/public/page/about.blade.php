@section('title', $seo['title'] ?? config('app.name'))
<x-app-layout>

  @include('_parts.public.pageTopBlock')
  @include('_parts.public.pageTextBetweenLines', ['class' => 'max-w-[598px]'])
  @include('_parts.public.pageCategories')
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 pt-6 sm:py-10 md:py-14 lg:py-16 xl:py-[86px]">
    <div class="md:flex md:flex-row-reverse">
      @if(isset($content->image_data['leMousseImage']['size']))
      <div class="md:max-w-[39.48170732%] w-full mx-auto flex flex-col">
        <div class="item-square imgAbout1 w-full">
          <input type="hidden" data-id="leMousseImage" class="json-image"
          value="{{ e(json_encode($content->image_data['leMousseImage']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover" data-img-class="absolute left-0 top-0 bottom-0 right-0 h-full w-full">
        </div>
      </div>
      @endif
      <div class="md:pr-14 lg:pr-16 xl:pr-20 flex-1 flex items-center">
        <div class="text-center md:text-left pt-12 md:pt-0">
          <h1 class="headline-1 text-myBrown">LE MOUSSE</h1>
          <div class="border-t border-myBrown my-6 w-[135px] mx-auto md:mx-0"></div>
          <div class="d-text-body m-text-body text-center md:text-left space-y-6">
            {!! $content->text_data['lemousseText'] ?? '' !!}
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-6 sm:py-10 md:py-14 lg:py-16 xl:py-[86px]">
    <div class="md:flex md:flex-row">
      @if(isset($content->image_data['productionImage']['size']))
      <div class="md:max-w-[39.48170732%] w-full mx-auto flex flex-col">
        <div class="item-square imgAbout1 w-full">
          <input type="hidden" data-id="productionImage" class="json-image"
                 value="{{ e(json_encode($content->image_data['productionImage']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover" data-img-class="absolute left-0 top-0 bottom-0 right-0 h-full w-full">
        </div>
      </div>
      @endif
      <div class="md:pl-14 lg:pl-16 xl:pl-20 flex-1 flex items-center">
        <div class="text-center md:text-left pt-6 md:pb-0 md:pt-0">
          <div class="d-text-body m-text-body text-center md:text-left space-y-6">
            {!! $content->text_data['productionText'] ?? '' !!}
          </div>
        </div>
      </div>
    </div>
  </div>
  @if(isset($content->text_data['greenLine']))
  <div class="py-6 px-2 sm:px-4 md:px-8 bg-myGreen2 text-white text-xl sm:text-2xl md:text-4xl lg:text-5xl !leading-tight italic font-medium flex justify-center text-center items-center">
    {!! nl2br($content->text_data['greenLine']) !!}
  </div>
  @endif
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-6 sm:py-10 md:py-14 lg:py-16 xl:py-[86px]">
    <div class="d-text-body m-text-body mb-16 pl-8">
      {!! $content->text_data['productionList'] ?? '' !!}
    </div>
    <div class="text-center md:text-left d-text-body m-text-body max-w-[1040px]">
      {!! $content->text_data['production'] ?? '' !!}
    </div>
  </div>
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-6 sm:py-10 md:py-14 lg:py-16 xl:py-[86px]">
    <div class="md:flex md:flex-row-reverse">
      <div class="md:max-w-[39.4817%] w-full mx-auto flex flex-col">
        <input type="hidden" data-id="NechaevaPhoto" class="json-image"
               value="{{ e(json_encode($content->image_data['NechaevaPhoto']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="w-full h-full block object-cover">
      </div>
      <div class="md:pr-14 lg:pr-16 xl:pr-20 md:py-14 lg:py-16 xl:py-[86px] flex-1 flex items-center">
        <div class="text-center md:text-left pt-2.5 md:pt-0">
          <h4 class="headline-4 ln-none mb-6">Создатель бренда Нечаева Ольга</h4>
          <div class="d-text-body m-text-body text-center md:text-left space-y-6 italic font-medium text-myBrown">
            {!! $content->text_data['nechaeva_quote'] !!}
          </div>
        </div>
      </div>
    </div>
  </div>

</x-app-layout>
