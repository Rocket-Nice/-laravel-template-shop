<div class="md:flex p-0 md:flex-row-reverse">
  <div class="md:max-w-[40%] xl:max-w-[546px] w-full mx-auto flex flex-col">
    <div class="item-square mainImage square-0.91 w-full">
      @if(isset($content->image_data['mainImage']['size']))
      <input type="hidden" data-id="mainImage" class="json-image" value="{{ e(json_encode($content->image_data['mainImage']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="w-full h-full block object-cover">
      @endif
    </div>
  </div>
  <div class="px-2 md:px-4 lg:pl-16 bg-myGreen flex-1 flex items-center">
    <div class="text-center md:text-left pt-5 pb-10 md:pb-0 md:pt-0 w-full">
      <h1 class="headline-1 mb-4 md:mb-6">{{ $content->text_data['headline1'] ?? '' }}</h1>
      @if(isset($content->text_data['subtitle1']))
      <div class="m-text-body d-text-body">{!! nl2br($content->text_data['subtitle1']) !!}</div>
      @endif
    </div>
  </div>
</div>
