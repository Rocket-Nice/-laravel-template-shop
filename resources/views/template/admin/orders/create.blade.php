@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <form action="{{ route('admin.orders.store') }}" method="post">
    @csrf

    <div class="border-b">
      <div class="mx-auto px-2 sm:px-3 lg:px-4">
        <nav class="-mb-px flex flex-nowrap flex-col sm:flex-row sm:justify-start space-y-2 sm:space-y-0 sm:space-x-4"
             aria-label="Tabs" role="tablist">
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-1" aria-selected="true" role="tab" aria-controls="tab-1-content">Данные получателя
          </button>
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-2" aria-selected="true" role="tab" aria-controls="tab-2-content">Доставка
          </button>
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-3" aria-selected="true" role="tab" aria-controls="tab-3-content">Корзина
          </button>
        </nav>
      </div>
    </div>

    <div class="mx-auto px-2 sm:px-3 lg:px-4 py-6" id="tab-content">
      <div id="tab-1-content" role="tabpanel">
        <div class="sm:w-[50%]">
          <div class="form-group">
            <x-input-label for="email">Email сущетсвующего пользователя</x-input-label>
            <x-text-input type="text" name="email" id="email" placeholder="..." value="{{ old('email') ?? request()->email }}"/>
          </div>
        </div>
      </div>
      <div id="tab-2-content" role="tabpanel">
        <div>
          <div id="shipping-info"></div>
          <div class="form-group">
            <x-input-label for="country" :value="__('Страна')"/>
            <select id="country" name="country" class="form-control w-full">
              @foreach($countries as $c)
                <option value="{{ $c->id }}" @isset($c->options['status']) data-shipping="{{ implode(',',$c->options['status']) }}@if($c->id==1),{{ implode(',',$pickups->pluck('code')->toArray()) }},{{ 'none' }}@endif" @endisset data-pochta="{{ $c->options['pochta_id'] }}" @if(old('country_code')==$c->options['pochta_code']||($c->id==1 && !old('country_code'))){!! 'selected' !!}@endif>{{ $c->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="shipping-methods">
            <x-input-label>Способ доставки</x-input-label>
            @foreach($shipping_methods as $shipping_method)
              <div class="form-group">
                <div class="flex items-center">
                  <input type="radio" id="{{ $shipping_method->code }}" name="shipping-method" value="{{ $shipping_method->code }}" class="shipping-item form-control" @if(old('shipping-code') == $shipping_method->code){{ 'checked' }}@endif>
                  <label for="{{ $shipping_method->code }}" class="ml-2">{{ $shipping_method->name }}</label>
                </div>
                @if(in_array($shipping_method->code, ['cdek', 'boxberry']))
                  <span class="button-box @if(old('shipping-code')==$shipping_method->code){{ 'block' }}@else{{ 'hidden' }}@endif">
                    <a class="cursor-pointer" data-shipping="{{ $shipping_method->code }}">Выбрать ПВЗ</a>
                  </span>
                @elseif(in_array($shipping_method->code, ['cdek_courier']))
                  <span class="button-box @if(old('shipping-code')==$shipping_method->code){{ 'block' }}@else{{ 'hidden' }}@endif">
                    <a class="cursor-pointer" data-shipping="{{ $shipping_method->code }}">Указать адрес</a>
                  </span>
                @endif
              </div>
            @endforeach
            <div class="form-group">
              <div class="flex items-center">
                <input type="radio" id="pickup" name="shipping-method" value="pickup" class="shipping-item form-control" @if(old('shipping-code') == 'pickup'){{ 'checked' }}@endif>
                <label for="pickup" class="ml-2">Самовывоз в Волгограде</label>
              </div>
            </div>
            <div class="form-group">
              <div class="flex items-center">
                <input type="radio" id="none" name="shipping-method" value="none" class="shipping-item form-control" @if(old('shipping-code') == 'none'){{ 'checked' }}@endif>
                <label for="none" class="ml-2">Без доставки</label>
              </div>
            </div>
          </div>
        </div>
        <div class="pochta-address @if(old('shipping-code')!='pochta') hidden @endif mt-3" id="pochta-address">
          <div class="form-group">
            <x-input-label for="postcode">Почтовый индекс</x-input-label>
            <x-text-input type="text" class="form-control" name="postcode" value="{{ old('postcode') }}" id="postcode" data-required/>
          </div>
          <div class="form-group">
            <x-input-label for="region">Название района, области, края или республики</x-input-label>
            <x-text-input type="text" class="form-control" name="region" value="{{ old('region') }}" id="region" data-required/>
          </div>
          <div class="form-group">
            <x-input-label for="city">Название населенного пункта</x-input-label>
            <x-text-input type="text" class="form-control" name="city" value="{{ old('city') }}" id="city" data-required/>
          </div>
          <div class="form-group">
            <x-input-label for="street">Название улицы</x-input-label>
            <x-text-input type="text" class="form-control" name="street" value="{{ old('street') }}" id="street" data-required/>
          </div>
          <div class="flex flex-wrap -m-1">
            <div class="p-1 w-full lg:w-1/2">
              <div class="form-group">
                <x-input-label for="house">Номер дома</x-input-label>
                <x-text-input type="text" class="form-control" name="house" value="{{ old('house') }}" id="house" data-required/>
              </div>
            </div>
            <div class="p-1 w-full lg:w-1/2">
              <div class="form-group">
                <x-input-label for="flat">Номер квартиры</x-input-label>
                <x-text-input type="text" class="form-control" name="flat" value="{{ old('flat') }}" id="flat"/>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="tab-3-content" role="tabpanel">
        <div class="sm:w-[50%]">
          <div class="tab-content">

          </div>

          <div class="flex flex-wrap items-center">
            <div class="form-group !mb-0 mr-3 flex-1">
              <select name="product" class="form-control w-full">
                @foreach($products as $product)
                  <option value="" data-id="{{ $product->id }}" data-name="{{ str_replace('"', '\'', $product->name) }}" data-model="{{ $product->sku }}" data-price="{{ $product->price }}">{{ $product->name }} ({{ $product->sku }})</option>
                @endforeach
              </select>
            </div>
            <button type="button" class="button button-secondary text-white" id="actioncell_submit" onclick="addSlide(); return false;">Добавить</button>
          </div>
        </div>
      </div>
    </div>

    <div class="px-3 sm:px-4 lg:px-6 py-6 flex justify-end space-x-4">
      <x-secondary-button href="{{ url()->previous() }}">Отменить</x-secondary-button>
      <x-primary-button>Сохранить</x-primary-button>
    </div>
    <input type="hidden" name="shipping" id="shipping-method" value="{{ old('shipping-code') }}">
    <input type="hidden" name="cdek-pvz-id" id="cdek-pvz-id" value="{{ old('cdek-pvz-id') }}">
    <input type="hidden" name="cdek-pvz-address" id="cdek-pvz-address" value="{{ old('cdek-pvz-address') }}">
    <input type="hidden" name="boxberry-pvz-id" id="boxberry-pvz-id" value="{{ old('boxberry-pvz-id') }}">
    <input type="hidden" name="boxberry-pvz-address" id="boxberry-pvz-address" value="{{ old('boxberry-pvz-address') }}">
    <input type="hidden" name="cdek_courier-form-region" id="cdek_courier-form-region" value="{{ old('cdek_courier-form-region') }}">
    <input type="hidden" name="cdek_courier-form-city" id="cdek_courier-form-city" value="{{ old('cdek_courier-form-city') }}">
    <input type="hidden" name="cdek_courier-form-street" id="cdek_courier-form-street" value="{{ old('cdek_courier-form-street') }}">
    <input type="hidden" name="cdek_courier-form-house" id="cdek_courier-form-house" value="{{ old('cdek_courier-form-house') }}">
    <input type="hidden" name="cdek_courier-form-flat" id="cdek_courier-form-flat" value="{{ old('cdek_courier-form-flat') }}">
    <input type="hidden" name="cdek_courier-form-address" id="cdek_courier-form-address" value="{{ old('cdek_courier-form-address') }}">
  </form>

  <input type="hidden" class="js_data" id="route_getCdekCourierRegions" value="{{ route('getCdekCourierRegions') }}">
  <input type="hidden" class="js_data" id="route_getCdekCourierCities" value="{{ route('getCdekCourierCities') }}">
  <input type="hidden" class="js_data" id="route_getCdekRegions" value="{{ route('getCdekRegions') }}">
  <input type="hidden" class="js_data" id="route_getCdekCities" value="{{ route('getCdekCities') }}">
  <input type="hidden" class="js_data" id="route_getCdekPvz" value="{{ route('getCdekPvz') }}">
  <input type="hidden" class="js_data" id="route_getBoxberryRegions" value="{{ route('getBoxberryRegions') }}">
  <input type="hidden" class="js_data" id="route_getBoxberryCities" value="{{ route('getBoxberryCities') }}">
  <input type="hidden" class="js_data" id="route_getBoxberryPvz" value="{{ route('getBoxberryPvz') }}">
  <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey=c7744407-82c1-4c00-a488-4ae90d1e64ef" type="text/javascript"></script>

  <div class="max-w-screen-lg w-1/2 p-4 rounded-md bg-green-100 hidden"></div>
  <div id="item-donor" class="hidden">
    <div class="toggle-wrapper mb-2 md:mb-4 border border-gray-200 rounded-md">
      <div class="toggle-button p-4 cursor-pointer flex justify-between items-center">
        <span class="product-name"></span>
        <svg width="17" height="11" class="ml-2" viewBox="0 0 17 11" fill="none" xmlns="http://www.w3.org/2000/svg">
          <g filter="url(#filter0_b_1943_23515)">
            <path d="M16.5 2.23962L8.5 10.166L0.5 2.23962L1.92 0.832683L8.5 7.35214L15.08 0.832682L16.5 2.23962Z" fill="black"/>
          </g>
          <defs>
            <filter id="filter0_b_1943_23515" x="-31.5" y="-31.168" width="80" height="73.334" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
              <feFlood flood-opacity="0" result="BackgroundImageFix"/>
              <feGaussianBlur in="BackgroundImageFix" stdDeviation="16"/>
              <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_1943_23515"/>
              <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_1943_23515" result="shape"/>
            </filter>
          </defs>
        </svg>
      </div>
      <div class="toggle-content overflow-hidden transition-max-height duration-500 h-0 mx-auto max-w-[700px] txt-body px-2 md:px-0">
        <div class="p-4">
          <div class="mb-2">
            <div class="form-group">
              <label class="block font-medium text-sm text-gray-700" for="cart-{number}-id">
                Идентификатор
              </label>
              <input readonly="" class="block w-full border-gray-300 focus:border-lemousseColor focus:ring-lemousseColor rounded-md shadow-sm" type="text" name="cart[{number}][id]" id="cart-{number}-id" placeholder="..." value="" disabled>
            </div>
            <div class="form-group">
              <label class="block font-medium text-sm text-gray-700" for="cart-{number}-qty">
                Количество
              </label>
              <input class="block w-full border-gray-300 focus:border-lemousseColor focus:ring-lemousseColor rounded-md shadow-sm" type="text" name="cart[{number}][qty]" id="cart-{number}-qty" placeholder="..." value="" disabled>
            </div>
            <div class="form-group">
              <label class="block font-medium text-sm text-gray-700" for="cart-{number}-name">
                Нааименование
              </label>
              <input class="block w-full border-gray-300 focus:border-lemousseColor focus:ring-lemousseColor rounded-md shadow-sm" type="text" name="cart[{number}][name]" id="cart-{number}-name" placeholder="..." value="" disabled>
            </div>
            <div class="form-group">
              <label class="block font-medium text-sm text-gray-700" for="cart-{number}-model">
                Артикул
              </label>
              <input readonly="" class="block w-full border-gray-300 focus:border-lemousseColor focus:ring-lemousseColor rounded-md shadow-sm" type="text" name="cart[{number}][model]" id="cart-{number}-model" placeholder="..." value="" disabled>
            </div>
            <div class="form-group">
              <label class="block font-medium text-sm text-gray-700" for="cart-{number}-price">
                Цена
              </label>
              <input class="block w-full border-gray-300 focus:border-lemousseColor focus:ring-lemousseColor rounded-md shadow-sm" type="text" name="cart[{number}][price]" id="cart-{number}-price" placeholder="..." value="" disabled>
            </div>
          </div>
          <div class="text-right">
            <button type="button" class="underline hover:no-underline" onclick="remvoeSlide(this);">Удалить</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <x-slot name="script">
    <script>

      function findKey(i, selector) {
        if (document.querySelector(`div${selector}${i}`) !== null) {
          return findKey(i + 1, selector);
        }
        return i;
      }

      const updateDynamicField = (elem, number) => {
        let inputs = elem.querySelectorAll('input'),
          labels = elem.querySelectorAll('label[for]');

        inputs.forEach((input) => {
          input.id = input.id.replace('{number}', number)
          input.name = input.name.replace('{number}', number)
          if (input.disabled) {
            input.removeAttribute('disabled')
          }
        })
        labels.forEach((label) => {
          let thisId = label.getAttribute('for');
          label.setAttribute('for', thisId.replace('{number}', number));
        })
      }

      function addSlide() {
        var option = document.querySelector('select[name="product"] option:checked');
        var item_id = option.dataset.id;
        var name = option.dataset.name;
        var model = option.dataset.model;
        var price = option.dataset.price;
        var i = findKey(document.querySelectorAll('.toggle-wrapper').length, '#toggle-content-');

        var new_slide = document.getElementById('item-donor').querySelector('.toggle-wrapper').cloneNode(true);
        updateDynamicField(new_slide, i);
        new_slide.querySelector('.product-name').innerText = name
        new_slide.querySelector('.toggle-content').id = 'toggle-content-'+i
        new_slide.querySelector('#cart-'+i+'-id').value = item_id
        new_slide.querySelector('#cart-'+i+'-name').value = name
        new_slide.querySelector('#cart-'+i+'-model').value = model
        new_slide.querySelector('#cart-'+i+'-qty').value = 1
        new_slide.querySelector('#cart-'+i+'-price').value = price
        document.querySelector('.tab-content').append(new_slide);


        const content = new_slide.querySelector('.toggle-content');
        const toggleButton = new_slide.querySelector('.toggle-button');
        const arrow = new_slide.querySelector('svg');

        // Сохраняем высоту контента для каждого блока
        let contentHeight = content.scrollHeight + "px";

        // Устанавливаем начальную высоту и overflow
        content.style.height = contentHeight;
        content.style.overflow = 'hidden';
        arrow.style.transform = 'rotate(180deg)';

        toggleButton.addEventListener('click', (event) => {
          if(content.style.height === '0px') {
            content.style.height = contentHeight;
            arrow.style.transform = 'rotate(180deg)'; // Переворачиваем стрелку вниз
          } else {
            content.style.height = '0px';
            arrow.style.transform = 'rotate(0deg)'; // Возвращаем стрелку в начальное положение
          }
        });
      }

      function remvoeSlide(elem) {
        var box = elem.closest('.toggle-wrapper');
        box.parentNode.removeChild(box);
      }


      // end ozon
    </script>
  </x-slot>
</x-admin-layout>
