<div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-6 sm:py-10 md:py-14 lg:py-16 xl:py-[86px]">
  <div class="flex items-end md:items-center">
    @if(isset($common->image_data['readMoreImage']['size']))

    <div class="item-square pageCategories w-[36.89839572%] sm:w-[35.6707%]">
      <input type="hidden" data-id="pageCategories" class="json-image" value="{{ e(json_encode($common->image_data['readMoreImage']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover">
    </div>
    @endif
    <div
      class="space-y-[11px] sm:space-y-3 md:space-y-4 lg:space-y-5 xl:space-y-6 ml-4 md:ml-8 sm:ml-16 lg:ml-32 xl:ml-[150px]">
      <h3 class="headline-4">Читать дополнительно</h3>
      <a href="{{ route('page.dermatologists') }}" class="block @if(mb_strtolower(Route::currentRouteName())!='page.dermatologists') text-myBrown @endif text-xl leading-1.2 d-headline-3">Наши специалисты</a>
      <a href="{{ route('page.guests') }}" class="block @if(mb_strtolower(Route::currentRouteName())!='page.guests') text-myBrown @endif text-xl leading-1.2 d-headline-3">Бренд, который выбирают звезды</a>
      <a href="{{ route('page.awards') }}" class="block @if(mb_strtolower(Route::currentRouteName())!='page.awards') text-myBrown @endif text-xl leading-1.2 d-headline-3">Премии и награды</a>
      <a href="{{ route('page.certificates') }}" class="block @if(mb_strtolower(Route::currentRouteName())!='page.certificates') text-myBrown @endif text-xl leading-1.2 d-headline-3">Сертификаты бренда</a>
      <a href="{{ route('page.xenon') }}" class="block @if(mb_strtolower(Route::currentRouteName())!='page.xenon') text-myBrown @endif text-xl leading-1.2 d-headline-3">Ксенон в косметике</a>
{{--      <a href="" class="block text-myBrown text-xl leading-1.2 d-headline-3">Наше производство</a>--}}
      <a href="{{ route('page.contacts') }}" class="block text-myBrown text-xl leading-1.2 d-headline-3">Контакты</a>
{{--      <a href="{{ route('page.ambassadors') }}" class="block @if(mb_strtolower(Route::currentRouteName())!='page.ambassadors') text-myBrown @endif text-xl leading-1.2 d-headline-3">Амбассадоры бренда</a>--}}
    </div>
  </div>
</div>
