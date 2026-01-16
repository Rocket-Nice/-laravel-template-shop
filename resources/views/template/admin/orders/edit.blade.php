@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <form action="{{ route('admin.orders.update', $order->slug) }}" method="post">
    @csrf
    @method('PUT')

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
        </nav>
      </div>
    </div>

    <div class="mx-auto px-2 sm:px-3 lg:px-4 py-6" id="tab-content">
      <div id="tab-1-content" role="tabpanel">
        <div class="sm:w-[50%]">
          <div class="form-group">
            <x-input-label for="last_name">Фамилия</x-input-label>
            <x-text-input type="text" name="last_name" id="last_name" placeholder="..." value="{{ old('last_name') ?? $order->data['form']['last_name'] }}"/>
          </div>
          <div class="form-group">
            <x-input-label for="first_name">Имя</x-input-label>
            <x-text-input type="text" name="first_name" id="first_name" placeholder="..." value="{{ old('first_name') ?? $order->data['form']['first_name'] }}"/>
          </div>
          <div class="form-group">
            <x-input-label for="middle_name">Отчество</x-input-label>
            <x-text-input type="text" name="middle_name" id="middle_name" placeholder="..." value="{{ old('middle_name') ?? $order->data['form']['middle_name'] ?? '' }}"/>
          </div>
          <div class="form-group">
            <x-input-label for="email">Email</x-input-label>
            <x-text-input type="text" name="email" id="email" placeholder="..." value="{{ old('email') ?? $order->data['form']['email'] }}"/>
          </div>
          <div class="form-group">
            <x-input-label for="phone">Телефон</x-input-label>
            <x-text-input type="text" name="phone" id="phone" placeholder="..." value="{{ old('phone') ?? $order->data['form']['phone'] ?? '' }}"/>
          </div>
          <div class="form-group">
            <div class="flex items-center">
              <input type="checkbox" id="change_user" name="change_user" value="1" class="form-control">
              <label for="change_user" class="ml-2">Изменить данные пользователя</label>
            </div>
            <div class="hint">Без этой галочки данные обновятся только в заказе, но не у пользователя</div>
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
                <option value="{{ $c->id }}" @isset($c->options['status']) data-shipping="{{ implode(',',$c->options['status']) }}@if($c->id==1),{{ implode(',',$pickups->pluck('code')->toArray()) }},{{ 'none' }}@endif" @endisset data-pochta="{{ $c->options['pochta_id'] }}" @if($order->data_shipping['country_code']==$c->options['pochta_code']){!! 'selected' !!}@endif>{{ $c->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="shipping-methods">
            <x-input-label>Способ доставки</x-input-label>
            @foreach($shipping_methods as $shipping_method)
              <div class="form-group">
                <div class="flex items-center">
                  <input type="radio" id="{{ $shipping_method->code }}" name="shipping-method" value="{{ $shipping_method->code }}" class="shipping-item form-control" @if($order->data_shipping['shipping-code'] == $shipping_method->code){{ 'checked' }}@endif>
                  <label for="{{ $shipping_method->code }}" class="ml-2">{{ $shipping_method->name }}</label>
                </div>
                @if(in_array($shipping_method->code, ['cdek', 'boxberry','x5post', 'yandex']))
                  <span class="button-box @if($order->data_shipping['shipping-code']==$shipping_method->code){{ 'block' }}@else{{ 'hidden' }}@endif">
                    <a class="cursor-pointer" data-shipping="{{ $shipping_method->code }}">Выбрать ПВЗ</a>
                  </span>
                @elseif(in_array($shipping_method->code, ['cdek_courier']))
                  <span class="button-box @if($order->data_shipping['shipping-code']==$shipping_method->code){{ 'block' }}@else{{ 'hidden' }}@endif">
                    <a class="cursor-pointer" data-shipping="{{ $shipping_method->code }}">Указать адрес</a>
                  </span>
                @endif
              </div>
            @endforeach
            <div class="form-group">
              <div class="flex items-center">
                <input type="radio" id="pickup" name="shipping-method" value="pickup" class="shipping-item form-control" @if($order->data_shipping['shipping-code'] == 'pickup'){{ 'checked' }}@endif>
                <label for="pickup" class="ml-2">Самовывоз в Волгограде</label>
              </div>
            </div>
          </div>
        </div>
        <div class="pochta-address @if($order->data_shipping['shipping-code']!='pochta') hidden @endif mt-3" id="pochta-address">
          <div class="form-group">
            <x-input-label for="postcode">Почтовый индекс</x-input-label>
            <x-text-input type="text" class="form-control" name="postcode" value="{{ $order->data_shipping['postcode'] ?? '' }}" id="postcode" data-required/>
          </div>
          <div class="form-group">
            <label for="region">Название района, области, края или республики</label>
            <input type="text" class="form-control" name="region" value="{{ $order->data_shipping['region'] ?? '' }}" id="region" data-required>
          </div>
          <div class="form-group">
            <x-input-label for="city">Название населенного пункта</x-input-label>
            <x-text-input type="text" class="form-control" name="city" value="{{ $order->data_shipping['city'] ?? '' }}" id="city" data-required/>
          </div>
          <div class="form-group">
            <x-input-label for="street">Название улицы</x-input-label>
            <x-text-input type="text" class="form-control" name="street" value="{{ $order->data_shipping['street'] ?? '' }}" id="street" data-required/>
          </div>
          <div class="flex flex-wrap -m-1">
            <div class="p-1 w-full lg:w-1/2">
              <div class="form-group">
                <x-input-label for="house">Номер дома</x-input-label>
                <x-text-input type="text" class="form-control" name="house" value="{{ $order->data_shipping['house'] ?? '' }}" id="house" data-required/>
              </div>
            </div>
            <div class="p-1 w-full lg:w-1/2">
              <div class="form-group">
                <x-input-label for="flat">Номер квартиры</x-input-label>
                <x-text-input type="text" class="form-control" name="flat" value="{{ $order->data_shipping['flat'] ?? '' }}" id="flat"/>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="px-3 sm:px-4 lg:px-6 py-6 flex justify-end space-x-4">
      <x-secondary-button href="{{ route('admin.orders.show', $order->slug) }}">Отменить</x-secondary-button>
      <x-primary-button>Сохранить</x-primary-button>
    </div>
    <input type="hidden" name="shipping" id="shipping-method" value="{{ $order->data_shipping['shipping-code'] }}">
    <input type="hidden" name="cdek-pvz-id" id="cdek-pvz-id" value="{{ $order->data_shipping['cdek-pvz-id'] ?? '' }}">
    <input type="hidden" name="cdek-pvz-address" id="cdek-pvz-address" value="{{ $order->data_shipping['cdek-pvz-address'] ?? '' }}">
    <input type="hidden" name="boxberry-pvz-id" id="boxberry-pvz-id" value="{{ $order->data_shipping['boxberry-pvz-id'] ?? '' }}">
    <input type="hidden" name="boxberry-pvz-address" id="boxberry-pvz-address" value="{{ $order->data_shipping['boxberry-pvz-address'] ?? '' }}">
    <input type="hidden" name="x5post-pvz-id" id="x5post-pvz-id" value="{{ $order->data_shipping['x5post-pvz-id'] ?? '' }}">
    <input type="hidden" name="x5post-pvz-address" id="x5post-pvz-address" value="{{ $order->data_shipping['x5post-pvz-address'] ?? '' }}">
    <input type="hidden" name="cdek_courier-form-region" id="cdek_courier-form-region" value="{{ $order->data_shipping['cdek_courier-form-region'] ?? '' }}">
    <input type="hidden" name="cdek_courier-form-city" id="cdek_courier-form-city" value="{{ $order->data_shipping['cdek_courier-form-city'] ?? '' }}">
    <input type="hidden" name="cdek_courier-form-street" id="cdek_courier-form-street" value="{{ $order->data_shipping['cdek_courier-form-street'] ?? '' }}">
    <input type="hidden" name="cdek_courier-form-house" id="cdek_courier-form-house" value="{{ $order->data_shipping['cdek_courier-form-house'] ?? '' }}">
    <input type="hidden" name="cdek_courier-form-flat" id="cdek_courier-form-flat" value="{{ $order->data_shipping['cdek_courier-form-flat'] ?? '' }}">
    <input type="hidden" name="cdek_courier-form-address" id="cdek_courier-form-address" value="{{ $order->data_shipping['cdek_courier-form-address'] ?? '' }}">
  </form>

  <input type="hidden" class="js_data" id="route_getCdekCourierRegions" value="{{ route('getCdekCourierRegions') }}">
  <input type="hidden" class="js_data" id="route_getCdekCourierCities" value="{{ route('getCdekCourierCities') }}">
  <input type="hidden" class="js_data" id="route_getCdekRegions" value="{{ route('getCdekRegions') }}">
  <input type="hidden" class="js_data" id="route_getCdekCities" value="{{ route('getCdekCities') }}">
  <input type="hidden" class="js_data" id="route_getCdekPvz" value="{{ route('getCdekPvz') }}">
  <input type="hidden" class="js_data" id="route_getBoxberryRegions" value="{{ route('getBoxberryRegions') }}">
  <input type="hidden" class="js_data" id="route_getBoxberryCities" value="{{ route('getBoxberryCities') }}">
  <input type="hidden" class="js_data" id="route_getBoxberryPvz" value="{{ route('getBoxberryPvz') }}">

  <input type="hidden" class="js_data" id="route_getX5PostRegions" value="{{ route('getX5PostRegions') }}">
  <input type="hidden" class="js_data" id="route_getX5PostCities" value="{{ route('getX5PostCities') }}">
  <input type="hidden" class="js_data" id="route_getX5PostPvz" value="{{ route('getX5PostPvz') }}">
  <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey=c7744407-82c1-4c00-a488-4ae90d1e64ef" type="text/javascript"></script>

  <div class="max-w-screen-lg w-1/2 p-4 rounded-md bg-green-100 hidden"></div>
</x-admin-layout>
