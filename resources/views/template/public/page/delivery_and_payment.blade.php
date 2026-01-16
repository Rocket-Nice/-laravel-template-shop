@section('title', $seo['title'] ?? config('app.name'))
<x-app-layout>

  @include('_parts.public.pageTopBlock')
  @if(isset($content->carousel_data['deliveryElems']))
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-6 sm:py-10 md:py-14 lg:py-16 xl:py-[86px]">
    <div>
      <div class="flex justify-center items-center mb-8 md:mb-10 lg:mb-12">
        <div class="border-t border-b border-myBrown w-[124px]"></div>
        <h2 class="text-center product-headline text-myBrown mx-6 lh-base">Действие</h2>
        <div class="border-t border-b border-myBrown w-[124px]"></div>
      </div>
      <div class="flex mx-0 sm:-mx-3 justify-between sm:justify-center">
        @foreach($content->carousel_data['deliveryElems'] as $elem)
          <div class="w-1/3 sm:w-[200px] sm:p-0 sm:mx-[44px]">
            <div class="item-square rounded-full bg-myBeige w-[86px] mx-auto">
              <div class="flex items-center justify-center">
                <img src="{{ $elem['icon'] }}" alt="" class="w-16">
              </div>
            </div>
            <div class="mt-4 text-base md:text-xl lg:text-2xl lg-none font-semibold text-center">{{ $elem['text'] }}</div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
  @endif
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 pt-12 pb-6 sm:pb-10 md:pb-14 lg:pb-16 xl:pb-[86px]">
    <div class="md:flex md:flex-row-reverse">
      <div class="md:max-w-[39.4817%] w-full mx-auto md:flex md:justify-end pb-6 md:pb-0">
        <div class="item-square w-full max-w-[68.44919786%] mx-auto md:mx-0 md:max-w-[468px] shippingImage w-full">
          <input type="hidden" data-id="shippingImage" class="json-image"
                 value="{{ e(json_encode($content->image_data['deliveryImage']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover" data-img-class="absolute left-0 top-0 bottom-0 right-0 h-full w-full">
        </div>
      </div>
      <div class="md:pr-14 lg:pr-16 xl:pr-20 flex-1 flex justify-center items-center">
        <div class="d-text-body m-text-body text-center sm:text-left space-y-6">
          {!! $content->text_data['shipping'] ?? '' !!}
        </div>
      </div>
    </div>
  </div>
{{--  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 pt-6 pb-12 sm:pb-10 md:pb-14 lg:pb-16 xl:pb-[86px]">--}}
{{--    <div class="md:flex md:flex-row-reverse">--}}
{{--      <div class="md:pl-14 lg:pl-16 xl:pl-20 flex-1 flex items-center">--}}
{{--        <div class="d-text-body m-text-body text-center sm:text-left space-y-6">--}}
{{--          {!! $content->text_data['payment'] ?? '' !!}--}}
{{--        </div>--}}
{{--      </div>--}}
{{--      <div class="md:max-w-[39.4817%] w-full mx-auto md:flex pt-6 md:pt-0">--}}
{{--        <div class="item-square w-full max-w-[68.44919786%] mx-auto md:mx-0 md:max-w-[468px] shippingImage w-full">--}}
{{--          <input type="hidden" data-id="paymentImage" class="json-image"--}}
{{--                 value="{{ e(json_encode($content->image_data['paymentImage']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover" data-img-class="absolute left-0 top-0 bottom-0 right-0 h-full w-full">--}}
{{--        </div>--}}
{{--      </div>--}}
{{--    </div>--}}
{{--  </div>--}}
    @if(isset($content->text_data['greenLine']))
      <div class="py-6 px-2 sm:px-4 md:px-8 bg-myGreen2 text-white text-xl sm:text-2xl md:text-4xl lg:text-5xl !leading-tight italic font-medium flex justify-center text-center items-center">
        {!! nl2br($content->text_data['greenLine']) !!}
      </div>
    @endif
  @include('_parts.public.mailingSubscribe')


</x-app-layout>
