@section('title', $seo['title'] ?? config('app.name'))
<x-app-layout>

  <div class="rotate-180"></div>
  @if(isset($content->image_data['mainImage']['size']))
  <div class="sm:px-4 md:px-8 lg:px-14 xl:px-16">
    <input type="hidden" data-id="mainImage" class="json-image"
           value="{{ e(json_encode($content->image_data['mainImage']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover" data-img-class="block h-[180px] sm:h-[200px] md:h-[231px] lg:h-[298px] xl:h-[378px] w-full object-cover">
  </div>
  @endif
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-6 sm:py-9 md:py-12">
    <div class="flex justify-between items-center">
      <h1 class="flex-1 d-headline-1 m-headline-1 text-left md:text-center">Сертификаты</h1>
    </div>
  </div>
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16">
    <div id="wrapper">
      <div class="flex flex-wrap -mx-2 md:-mx-3 -my-12 md:-my-24">
          @forelse($products as $product)
          <div class="w-full sm:w-1/2 md:w-1/3 xl:w-1/4 px-2 md:px-3 py-12 md:py-24">
            <div>
              <div class="product-card_item">
                <div class="img product_card_voucher item-square block">
                  @if(isset($product->cardImage['image']))
                    <input type="hidden" data-id="cardImage-{{ $product->id }}" class="json-image"
                           value="{{ e(json_encode($product->cardImage['image'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover" data-img-class="block w-full object-cover">
                  @endif
                </div>
              </div>
            </div>
            <div class="mb-4 mt-4 md:mt-6 flex-1">
              <h3 class="break-words font-light text-lg sm:text-2xl md:text-3xl lg:text-32 pr-2 lh-outline-none !leading-none">{{ $product->name }}</h3>
            </div>
            <div class="relative flex items-center justify-between mb-4 md:mb-6">
              <div class="flex space-x-3 md:space-x-4 items-center">
                <div class="subtitle-1 text-myBrown">{{ formatPrice($product->price) }}</div>
                <div class="text-sm md:text-base cormorantInfant font-medium italic opacity-50"></div>
              </div>
              <a class="btn-tooltip" data-tooltip="tooltip-{{ $product->id }}">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M10 19C14.95 19 19 14.95 19 10C19 5.05 14.95 1 10 1C5.05 1 1 5.05 1 10C1 14.95 5.05 19 10 19Z" stroke="#B1908E" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M10 14.5V10" stroke="#B1908E" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M9.99512 7.30078H10.0032" stroke="#B1908E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </a>
              <div id="tooltip-{{ $product->id }}" class="tooltip hidden absolute bottom-full mb-4 shadow-md p-6 bg-white w-screen max-w-[260px] md:max-w-[386px] z-10">
                <div class="flex justify-between items-center mb-3 md:mb-6">
                  <h4 class="headline-4">Условия использования</h4>
                  <button class="close-tooltip">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M10.0003 18.3327C14.5837 18.3327 18.3337 14.5827 18.3337 9.99935C18.3337 5.41602 14.5837 1.66602 10.0003 1.66602C5.41699 1.66602 1.66699 5.41602 1.66699 9.99935C1.66699 14.5827 5.41699 18.3327 10.0003 18.3327Z" stroke="#2C2E35" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M7.6416 12.3592L12.3583 7.64258" stroke="#2C2E35" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M12.3583 12.3592L7.6416 7.64258" stroke="#2C2E35" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                  </button>
                </div>
                <div class="mb-3 md:mb-6 subtitle-2">
                  <p>Данный сертификат дает возможность приобрести любой товар на сайте и в магазинах LE&nbsp;MOUSSE в соответствии с номиналом. При оплате сертификатом товара на сумму большую номинала сертификата, производится доплата. Если стоимость товара меньше номинала, денежная разница не компенсируется.<br/>
                  Возврату и обмену не подлежит.</p>
                  <br/>
                  <p>Радуйте близких вместе с LE&nbsp;MOUSSE.</p>
                </div>
              </div>
            </div>
            @if($product->getStock())
              <x-public.primary-button href="{{ route('order.voucher', $product->sku) }}" class="w-full !px-2 text-center"><span class="text-base sm:text-xl whitespace-nowrap">Заказать</span></x-public.primary-button>
            @else
              <div class="text-center text-xl bg-gray-200 text-gray-500 h-11 flex justify-center items-center">
                sold out
              </div>
            @endif
          </div>
          @empty

            <div class="text-center p-12 d-headline-4 m-headline-3 uppercase text-gray-400 w-full">Здесь пусто</div>

          @endforelse
      </div>

    </div>
  </div>
</x-app-layout>
