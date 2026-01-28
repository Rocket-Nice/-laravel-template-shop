@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <form action="{{ route('admin.settings.save') }}" method="post">
    @csrf
    <div class="border-b">
      <div class="mx-auto px-2 sm:px-3 lg:px-4">
        <nav class="-mb-px flex flex-col sm:flex-row sm:justify-start space-y-2 sm:space-y-0 sm:space-x-4"
             aria-label="Tabs" role="tablist">
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-1" aria-selected="true" role="tab" aria-controls="tab-1-content">Общие
          </button>
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-gold-ticket" aria-selected="false" role="tab" aria-controls="tab-gold-ticket-content">Режим «Золотой билет»
          </button>
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-2" aria-selected="false" role="tab" aria-controls="tab-2-content">Режим «1+1=3»
          </button>
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-3" aria-selected="false" role="tab" aria-controls="tab-3-content">Режим «Счастливый купон»
          </button>
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-cat-in-bag" aria-selected="false" role="tab" aria-controls="tab-cat-in-bag-content">Режим «Кот в мешке»
          </button>
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-4" aria-selected="false" role="tab" aria-controls="tab-4-content">Раздача пазлов
          </button>
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-5" aria-selected="false" role="tab" aria-controls="tab-5-content">Режим «Бриллиантовые сутки»
          </button>
          {{--          <button type="button"--}}
          {{--                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"--}}
          {{--                  id="tab-promo20" aria-selected="false" role="tab" aria-controls="tab-promo20-content">Режим «Акция 20% с подарками»--}}
          {{--          </button>--}}
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-promo30" aria-selected="false" role="tab" aria-controls="tab-promo30-content">Режим «Акция 30% с подарками»
          </button>
        </nav>
      </div>
    </div>

    <div class="mx-auto px-2 sm:px-3 lg:px-4 py-6" id="tab-content">
      <div id="tab-1-content" role="tabpanel">
        <div class="sm:w-[75%]">
          <div class="form-group">
            <x-input-label :value="__('Доступность сообщения')" />
            <div class="flex items-center">
              <input type="radio" id="maintenanceStatus-1" name="maintenanceStatus" value="1" class="form-control" @if(getSettings('maintenanceStatus') == 1) {{ 'checked' }}@endif>
              <label for="maintenanceStatus-1" class="ml-2">Сайт доступен</label>
            </div>
            <div class="flex items-center">
              <input type="radio" id="maintenanceStatus-2" name="maintenanceStatus" value="0" class="form-control" @if(getSettings('maintenanceStatus') == 0) {{ 'checked' }}@endif>
              <label for="maintenanceStatus-2" class="ml-2">Сайт закрыт</label>
            </div>
          </div>
          <div class="form-group">
            <x-input-label for="maintenanceNotification" :value="__('Текст сообщения')" />
            <x-textarea name="maintenanceNotification" id="maintenanceNotification" class="mt-1 tinymce-textarea w-full">{!! old('maintenanceNotification') ?? getSettings('maintenanceNotification') !!}</x-textarea>
          </div>

          <div class="form-group">
            <x-input-label :value="__('Платежный шлюз')" />
            <div class="flex items-center">
              <input type="radio" id="paymentMethod-1" name="paymentMethod" value="Robokassa" class="form-control" @if(getSettings('paymentMethod') == 'Robokassa') {{ 'checked' }}@endif>
              <label for="paymentMethod-1" class="ml-2">Robokassa</label>
            </div>
            <div class="flex items-center">
              <input type="radio" id="paymentMethod-2" name="paymentMethod" value="Cloudpayments" class="form-control" @if(getSettings('paymentMethod') == 'Cloudpayments') {{ 'checked' }}@endif>
              <label for="paymentMethod-2" class="ml-2">Cloudpayments</label>
            </div>
          </div>
          <div class="form-group">
            <x-input-label :value="__('Режим оплаты')" />
            <div class="flex items-center">
              <input type="radio" id="payment_test-1" name="payment_test" value="0" class="form-control" @if(getSettings('payment_test') == 0) {{ 'checked' }}@endif>
              <label for="payment_test-1" class="ml-2">Боевой режим</label>
            </div>
            <div class="flex items-center">
              <input type="radio" id="payment_test-2" name="payment_test" value="1" class="form-control" @if(getSettings('payment_test') == 1) {{ 'checked' }}@endif>
              <label for="payment_test-2" class="ml-2">Тестовй режим</label>
            </div>
            <div class="hint">Только для робокассы</div>
          </div>
        </div>
      </div>
      <div id="tab-2-content" role="tabpanel">
        <div class="form-group">
          <x-input-label :value="__('Режим «1+1=3»')" />
          <div class="flex items-center">
            <input type="radio" id="promo_1+1=3-1" name="promo_1+1=3" value="1" class="form-control" @if(getSettings('promo_1+1=3') == 1) {{ 'checked' }}@endif>
            <label for="promo_1+1=3-1" class="ml-2">Акция включена</label>
          </div>
          <div class="flex items-center">
            <input type="radio" id="promo_1+1=3-2" name="promo_1+1=3" value="0" class="form-control" @if(getSettings('promo_1+1=3') == 0) {{ 'checked' }}@endif>
            <label for="promo_1+1=3-2" class="ml-2">Акция выключена</label>
          </div>
        </div>
      </div>
      <div id="tab-promo20-content" role="tabpanel">
        <div class="form-group">
          <x-input-label :value="__('Режим «Акция 20% с подарками»')" />
          <div class="flex items-center">
            <input type="radio" id="promo20-1" name="promo20" value="1" class="form-control" @if(getSettings('promo20') == 1) {{ 'checked' }}@endif>
            <label for="promo20-1" class="ml-2">Акция включена</label>
          </div>
          <div class="flex items-center">
            <input type="radio" id="promo20-2" name="promo20" value="0" class="form-control" @if(getSettings('promo20') == 0) {{ 'checked' }}@endif>
            <label for="promo20-2" class="ml-2">Акция выключена</label>
          </div>
        </div>
      </div>
      <div id="tab-promo30-content" role="tabpanel">
        <div class="form-group">
          <x-input-label :value="__('Режим «Акция 20% с подарками»')" />
          <div class="flex items-center">
            <input type="radio" id="promo30-1" name="promo30" value="1" class="form-control" @if(getSettings('promo30') == 1) {{ 'checked' }}@endif>
            <label for="promo30-1" class="ml-2">Акция включена</label>
          </div>
          <div class="flex items-center">
            <input type="radio" id="promo30-2" name="promo30" value="0" class="form-control" @if(getSettings('promo30') == 0) {{ 'checked' }}@endif>
            <label for="promo30-2" class="ml-2">Акция выключена</label>
          </div>
        </div>
      </div>
      <div id="tab-3-content" role="tabpanel">
        <div class="form-group">
          <x-input-label :value="__('Режим «Счастливый купон»')" />
          <div class="flex items-center">
            <input type="radio" id="happyCoupon-1" name="happyCoupon" value="1" class="form-control" @if(getSettings('happyCoupon') == 1) {{ 'checked' }}@endif>
            <label for="happyCoupon-1" class="ml-2">Акция включена</label>
          </div>
          <div class="flex items-center">
            <input type="radio" id="happyCoupon-2" name="happyCoupon" value="0" class="form-control" @if(getSettings('happyCoupon') == 0) {{ 'checked' }}@endif>
            <label for="happyCoupon-2" class="ml-2">Акция выключена</label>
          </div>
        </div>
      </div>
      <div id="tab-cat-in-bag-content" role="tabpanel">
        <div class="form-group">
          <x-input-label :value="__('Режим «Кот в мешке»')" />
          <div class="flex items-center">
            <input type="radio" id="catInBag-1" name="catInBag" value="1" class="form-control" @if(getSettings('catInBag') == 1) {{ 'checked' }}@endif>
            <label for="catInBag-1" class="ml-2">Акция включена</label>
          </div>
          <div class="flex items-center">
            <input type="radio" id="catInBag-2" name="catInBag" value="0" class="form-control" @if(getSettings('catInBag') == 0) {{ 'checked' }}@endif>
            <label for="catInBag-2" class="ml-2">Акция выключена</label>
          </div>
        </div>
      </div>
      <div id="tab-gold-ticket-content" role="tabpanel">
        <div class="form-group">
          <x-input-label :value="__('Режим «Золотой билет')" />
          <div class="flex items-center">
            <input type="radio" id="goldTicket-1" name="goldTicket" value="1" class="form-control" @if(getSettings('goldTicket') == 1) {{ 'checked' }}@endif>
            <label for="goldTicket-1" class="ml-2">Акция включена</label>
          </div>
          <div class="flex items-center">
            <input type="radio" id="goldTicket-2" name="goldTicket" value="0" class="form-control" @if(getSettings('goldTicket') == 0) {{ 'checked' }}@endif>
            <label for="goldTicket-2" class="ml-2">Акция выключена</label>
          </div>
        </div>
      </div>
      <div id="tab-4-content" role="tabpanel">
        <div class="form-group">
          <x-input-label :value="__('Раздача пазлов с товарами')" />
          <div class="flex items-center">
            <input type="radio" id="puzzlesStatus-1" name="puzzlesStatus" value="1" class="form-control" @if(getSettings('puzzlesStatus') == 1) {{ 'checked' }}@endif>
            <label for="puzzlesStatus-1" class="ml-2">Пазлы раздаются</label>
          </div>
          <div class="flex items-center">
            <input type="radio" id="puzzlesStatus-2" name="puzzlesStatus" value="0" class="form-control" @if(getSettings('puzzlesStatus') == 0) {{ 'checked' }}@endif>
            <label for="puzzlesStatus-2" class="ml-2">Пазлы не раздаются</label>
          </div>
        </div>
      </div>
      <div id="tab-5-content" role="tabpanel">
        <div class="form-group">
          <x-input-label :value="__('Режим «Бриллиантовые сутки» (Раздача бонусов)')" />
          <div class="flex items-center">
            <input type="radio" id="diamondPromo1-1" name="diamondPromo1" value="1" class="form-control" @if(getSettings('diamondPromo1') == 1) {{ 'checked' }}@endif>
            <label for="diamondPromo1-1" class="ml-2">Акция включена</label>
          </div>
          <div class="flex items-center">
            <input type="radio" id="diamondPromo1-2" name="diamondPromo1" value="0" class="form-control" @if(getSettings('diamondPromo1') == 0) {{ 'checked' }}@endif>
            <label for="diamondPromo1-2" class="ml-2">Акция выключена</label>
          </div>
        </div>
        <div class="form-group">
          <x-input-label :value="__('Режим «Бриллиантовые сутки» (Использование бонусов)')" />
          <div class="flex items-center">
            <input type="radio" id="diamondPromo2-1" name="diamondPromo2" value="1" class="form-control" @if(getSettings('diamondPromo2') == 1) {{ 'checked' }}@endif>
            <label for="diamondPromo2-1" class="ml-2">Акция включена</label>
          </div>
          <div class="flex items-center">
            <input type="radio" id="diamondPromo2-2" name="diamondPromo2" value="0" class="form-control" @if(getSettings('diamondPromo2') == 0) {{ 'checked' }}@endif>
            <label for="diamondPromo2-2" class="ml-2">Акция выключена</label>
          </div>
        </div>
      </div>
    </div>
    <div class="px-3 sm:px-4 lg:px-6 py-6 flex justify-end">
      <x-primary-button>Сохранить</x-primary-button>
    </div>
  </form>
</x-admin-layout>
