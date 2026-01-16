@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
      @if(auth()->user()->hasPermissionTo('Создание заказов'))
      <a href="{{ route('admin.orders.create') }}" class="button button-success">Создать заказ</a>
      @endif
    @endif
  </x-slot>
  <x-admin.search-form :route="url()->current()">
    @if(request()->get('ticket_id'))
      <input type="hidden" name="ticket_id" value="{{ request()->get('ticket_id') }}">
    @endif
    @if(request()->get('user_id'))
      <input type="hidden" name="user_id" value="{{ request()->get('user_id') }}">
    @endif
      @if(request()->get('country'))
        <input type="hidden" name="country" value="{{ request()->get('country') }}">
      @endif
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="date_from" :value="__('Дата от')"/>
        <x-text-input type="text" name="date_from" id="date_from" value="{{ request()->get('date_from') }}" data-minDate="false" placeholder="{{ now()->startOfMonth()->format('d.m.Y H:i') }}" class="mt-1 block w-full datepicker"/>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="date_until" :value="__('Дата до')"/>
        <x-text-input type="text" name="date_until" id="date_until" value="{{ request()->get('date_until') }}" placeholder="{{ now()->format('d.m.Y H:i') }}" data-minDate="false" class="mt-1 block w-full datepicker"/>
      </div>
    </div>
      <script>
        const exceptions = @json(request()->exceptions ?? []);
      </script>
      <div
        class="w-full p-1"
        x-data="exceptionsForm(exceptions, '.datepicker')"
        x-init="init()"
      >
        <div class="space-y-1">

          <div class="p-1 w-full flex justify-end gap-2">
            <button type="button" class="button button-sm button-light-secondary"
                    @click="add()">
              Добавить исключение
            </button>
            <template x-if="exceptions.length">
              <button type="button" class="button button-sm button-light-danger"
                      @click="clearAll()">
                Очистить все
              </button>
            </template>
          </div>

          {{-- Динамические исключения --}}
          <template x-for="(item, i) in exceptions" :key="i">
            <div class="w-full flex flex-wrap -m-1 p-1">
              <div class="p-1 border border-gray-200 rounded-xl w-full flex flex-wrap -m-1">
                <div class="p-1 w-full flex justify-between items-center">
                  <div class="font-medium">Исключение <span x-text="i + 1"></span></div>
                  <button type="button"
                          @click="remove(i)">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="24"
                      height="24"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="#ddd"
                      stroke-width="1"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    >
                      <path d="M18 6l-12 12" />
                      <path d="M6 6l12 12" />
                    </svg>

                  </button>
                </div>

                <div class="p-1 w-full lg:w-1/2">
                  <div class="form-group">
                    <label :for="'exceptions_'+i+'_date_from'">Дата от</label>
                    <input type="text"
                           class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm datepicker"
                           :id="'exceptions_'+i+'_date_from'"
                           :name="'exceptions['+i+'][date_from]'"
                           x-model="item.date_from"
                           data-minDate="false"
                           placeholder="{{ now()->startOfMonth()->format('d.m.Y H:i') }}"
                           required
                    />
                    @isset($errors)
                      {{-- Показ ошибок на конкретную строку --}}
                      <template x-if="$store?.errors?.['exceptions.'+i+'.date_from']">
                        <p class="text-red-600 text-sm mt-1"
                           x-text="$store.errors['exceptions.'+i+'.date_from']"></p>
                      </template>
                    @endisset
                  </div>
                </div>

                <div class="p-1 w-full lg:w-1/2">
                  <div class="form-group">
                    <label :for="'exceptions_'+i+'_date_until'">Дата до</label>
                    <input type="text"
                           class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm datepicker"
                           :id="'exceptions_'+i+'_date_until'"
                           :name="'exceptions['+i+'][date_until]'"
                           x-model="item.date_until"
                           data-minDate="false"
                           placeholder="{{ now()->format('d.m.Y H:i') }}"
                           required
                    />
                    @isset($errors)
                      <template x-if="$store?.errors?.['exceptions.'+i+'.date_until']">
                        <p class="text-red-600 text-sm mt-1"
                           x-text="$store.errors['exceptions.'+i+'.date_until']"></p>
                      </template>
                    @endisset
                  </div>
                </div>
              </div>


            </div>
          </template>

        </div>
      </div>
    <div class="p-1 w-full lg:w-1/3">
      <div class="form-group">
        <x-input-label for="order_id" :value="__('ID заказа')"/>
        <x-text-input type="text" name="order_id" id="order_id" value="{{ request()->get('order_id') }}" class="mt-1 block w-full"/>
      </div>
    </div>
    <div class="p-1 w-full lg:w-2/3">
      <div class="form-group">
        <x-input-label for="keyword" :value="__('Имя, email или телефон')"/>
        <x-text-input type="text" name="keyword" id="keyword" value="{{ request()->get('keyword') }}" class="mt-1 block w-full"/>
      </div>
    </div>
    <div class="p-1 w-full">
      <div class="form-group">
        <x-input-label for="exclude_order_id" :value="__('Исключить ID (несколько через запятую)')"/>
        <x-text-input type="text" name="exclude_order_id" id="exclude_order_id" value="{{ request()->get('exclude_order_id') }}" placeholder="1,2,3"
                      class="mt-1 block w-full"/>
      </div>
    </div>
      <div class="p-1 w-full lg:w-1/2">
        <div class="form-group">
          <x-input-label for="filter-shipping" :value="__('Способ доставки')"/>
          <select id="filter-shipping" name="shipping[]" multiple class="multipleSelect form-control">
            <option value="boxberry" @if(is_array(request()->get('shipping'))&&in_array('boxberry', request()->get('shipping'))){!! 'selected' !!}@endif>Boxberry</option>
            <option value="yandex" @if(is_array(request()->get('shipping'))&&in_array('yandex', request()->get('shipping'))){!! 'selected' !!}@endif>Яндекс Доставка</option>
            <option value="cdek" @if(is_array(request()->get('shipping'))&&in_array('cdek', request()->get('shipping'))){!! 'selected' !!}@endif>СДЭК</option>
            <option value="cdek_courier" @if(is_array(request()->get('shipping'))&&in_array('cdek_courier', request()->get('shipping'))){!! 'selected' !!}@endif>СДЭК курьер</option>
            <option value="x5post" @if(is_array(request()->get('shipping'))&&in_array('x5post', request()->get('shipping'))){!! 'selected' !!}@endif>5 Пост</option>
            {{--              <option value="ozon" @if(is_array(request()->get('shipping'))&&in_array('ozon', request()->get('shipping'))){!! 'selected' !!}@endif>OZON</option>--}}

            <option value="pochta_russia" @if(is_array(request()->get('shipping'))&&in_array('pochta_russia', request()->get('shipping'))){!! 'selected' !!}@endif>Почта (РФ)</option>
            <option value="pochta_world" @if(is_array(request()->get('shipping'))&&in_array('pochta_world', request()->get('shipping'))){!! 'selected' !!}@endif>Почта (Мир)</option>

            @foreach($pickups as $pickup)
              <option value="{{ $pickup->code }}" @if(is_array(request()->get('shipping'))&&in_array($pickup->code, request()->get('shipping'))){!! 'selected' !!}@endif>{{ $pickup->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="p-1 w-full lg:w-1/2">
        <div class="form-group">
          <x-input-label for="filter-status" :value="__('Статус заказа')"/>
          <select id="filter-status" name="status[]" multiple class="multipleSelect form-control">
            @foreach($statuses as $status)
              <option
                value="{{ $status->key }}" @if(is_array(request()->get('status'))&&in_array($status->key, request()->get('status')))
                {!! 'selected' !!}
                @endif>{{ $status->name }}
              </option>
            @endforeach
{{--            <option value="is_processing" @if(is_array(request()->get('status'))&&in_array('is_processing', request()->get('status'))){!! 'selected' !!}@endif>В обработке</option>--}}
{{--            <option value="is_waiting" @if(is_array(request()->get('status'))&&in_array('is_waiting', request()->get('status'))){!! 'selected' !!}@endif>В ожидании</option>--}}
{{--            <option value="was_processed" @if(is_array(request()->get('status'))&&in_array('was_processed', request()->get('status'))){!! 'selected' !!}@endif>Обработан</option>--}}
{{--            <option value="was_sended_to_store" @if(is_array(request()->get('status'))&&in_array('was_sended_to_store', request()->get('status'))){!! 'selected' !!}@endif>Отправлен в сборку на склад</option>--}}
{{--            <option value="is_assembled" @if(is_array(request()->get('status'))&&in_array('is_assembled', request()->get('status'))){!! 'selected' !!}@endif>Собран на складе</option>--}}
{{--            <option value="is_ready" @if(is_array(request()->get('status'))&&in_array('is_ready', request()->get('status'))){!! 'selected' !!}@endif>Готов к выдачи</option>--}}
{{--            <option value="was_delivered" @if(is_array(request()->get('status'))&&in_array('was_delivered', request()->get('status'))){!! 'selected' !!}@endif>Выдан</option>--}}
{{--            <option value="refund" @if(is_array(request()->get('status'))&&in_array('refund', request()->get('status'))){!! 'selected' !!}@endif>Возврат</option>--}}
{{--            <option value="cdek_CREATED" @if(is_array(request()->get('status'))&&in_array('cdek_CREATED', request()->get('status'))){!! 'selected' !!}@endif>Сдэк: Создан</option>--}}
{{--            <option value="boxberry_загружен реестр им" @if(is_array(request()->get('status'))&&in_array('boxberry_загружен реестр им', request()->get('status'))){!! 'selected' !!}@endif>Boxberry: Загружен реестр ИМ</option>--}}
{{--            <option value="has_error" @if(is_array(request()->get('status'))&&in_array('has_error', request()->get('status'))){!! 'selected' !!}@endif>Ошибка в заказе</option>--}}
{{--            <option value="no_gift" @if(is_array(request()->get('status'))&&in_array('no_gift', request()->get('status'))){!! 'selected' !!}@endif>Не выбран подарок</option>--}}
{{--            <option value="address_error" @if(is_array(request()->get('status'))&&in_array('address_error', request()->get('status'))){!! 'selected' !!}@endif>Ошибка в адресе</option>--}}
{{--            <option value="test" @if(is_array(request()->get('status'))&&in_array('test', request()->get('status'))){!! 'selected' !!}@endif>Тест</option>--}}
{{--            <option value="not_in_demand" @if(is_array(request()->get('status'))&&in_array('not_in_demand', request()->get('status'))){!! 'selected' !!}@endif>Не востребован</option>--}}
          </select>
        </div>
      </div>
      <div class="p-1 w-full lg:w-1/2">
        <div class="form-group">
          <x-input-label for="invoice_id" :value="__('ID накладной (только цифры)')"/>
          <x-text-input type="text" name="invoice_id" id="invoice_id" value="{{ request()->get('invoice_id') }}" class="mt-1 block w-full"/>
        </div>
      </div>
      <div class="p-1 w-full lg:w-1/2">
        <div class="form-group">
          <x-input-label for="date_status" :value="__('Дата принятия статуса')"/>
          <x-text-input type="text" name="date_status" id="date_status" value="{{ request()->get('date_status') }}" data-minDate="false" class="mt-1 block w-full datepicker"/>
        </div>
      </div>

      <div class="p-1 w-full lg:w-1/2">
        <div class="form-group">
          <x-input-label for="filter-product" :value="__('Товар в корзине')"/>
          <select id="filter-product" name="product[]" multiple class="multipleSelect form-control">
            @foreach($products as $product)
              @if(is_array(request()->get('not_product'))&&in_array($product->sku, request()->get('not_product')))
                @continue
              @endif
              <option value="{{ $product->sku }}" @if(is_array(request()->get('product'))&&in_array($product->sku, request()->get('product'))){!! 'selected' !!}@endif>{{ $product->name }} ({{ $product->sku }})</option>
            @endforeach
{{--            <option value="scrub-kokos-bonus" @if(is_array(request()->get('product'))&&in_array('scrub-kokos-bonus', request()->get('product'))){!! 'selected' !!}@endif>Скраб кокос (бонус)</option>--}}
{{--            <option value="hgift-bg-malina" @if(is_array(request()->get('product'))&&in_array('hgift-bg-malina', request()->get('product'))){!! 'selected' !!}@endif>Бальзам для губ (подарок)</option>--}}
{{--            <option value="hgift-mm-kutikuli" @if(is_array(request()->get('product'))&&in_array('hgift-mm-kutikuli', request()->get('product'))){!! 'selected' !!}@endif>Масло для кутикулы (подарок)</option>--}}
          </select>
        </div>
      </div>
      <div class="p-1 w-full lg:w-1/2">
        <div class="form-group">
          <x-input-label for="filter-referers" :value="__('Товар не в корзине')"/>
          <select id="filter-referers" name="not_product[]" multiple class="multipleSelect form-control">
            @foreach($products as $product)
              @if(is_array(request()->get('product'))&&in_array($product->sku, request()->get('product')))
                @continue
              @endif
              <option value="{{ $product->sku }}" @if(is_array(request()->get('not_product'))&&in_array($product->sku, request()->get('not_product'))){!! 'selected' !!}@endif>{{ $product->name }} ({{ $product->sku }})</option>
            @endforeach
{{--            <option value="scrub-kokos-bonus" @if(is_array(request()->get('not_product'))&&in_array('scrub-kokos-bonus', request()->get('not_product'))){!! 'selected' !!}@endif>Скраб кокос (бонус)</option>--}}
{{--            <option value="hgift-bg-malina" @if(is_array(request()->get('not_product'))&&in_array('hgift-bg-malina', request()->get('not_product'))){!! 'selected' !!}@endif>Бальзам для губ (подарок)</option>--}}
{{--            <option value="hgift-mm-kutikuli" @if(is_array(request()->get('not_product'))&&in_array('hgift-mm-kutikuli', request()->get('not_product'))){!! 'selected' !!}@endif>Масло для кутикулы (подарок)</option>--}}
          </select>
        </div>
      </div>
      @if(auth()->user()->hasRole('admin'))
        <div class="p-1 w-full lg:w-1/2">
          <div class="form-group">
            <x-input-label for="filter-referrers" :value="__('Партнер')"/>
            <select id="filter-referrers" name="referrers[]" multiple class="multipleSelect form-control">
              @foreach($referrers as $referrer)
                <option value="{{ $referrer->id }}" @if(is_array(request()->get('referrers'))&&in_array($referrer->id, request()->get('referrers'))){!! 'selected' !!}@endif>{{ $referrer->name }} (id {{ $referrer->id }})</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="p-1 w-full lg:w-1/2">
          <div class="form-group">
            <x-input-label for="promocode" :value="__('Промокод')"/>
            <x-text-input type="text" name="promocode" id="promocode" value="{{ request()->get('promocode') }}"
                          class="mt-1 block w-full"/>
          </div>
        </div>
        <div class="p-1 w-full">
          <div class="form-group">
            <x-input-label for="payment_provider" :value="__('Платежный шлюз')" />
            <select id="payment_provider" name="payment_provider" class="form-control w-full">
              <option value="">Все</option>
              <option value="robokassa"  @if(request()->payment_provider=='robokassa'){{ 'selected' }}@endif>Robokassa</option>
              <option value="cloudpayments"  @if(request()->payment_provider=='cloudpayments'){{ 'selected' }}@endif>Cloudpayments</option>
            </select>
          </div>
        </div>
      @endif
      <div class="p-1 w-full">
        <div class="form-group">
          <x-input-label for="filter-orderBy" :value="__('Сортировка')"/>
          <select id="filter-orderBy" name="orderBy" class="form-control w-full">
            <option>Выбрать</option>
            <option value="amount|asc" @if(request()->get('orderBy')&&request()->get('orderBy')=='amount|asc'){!! 'selected' !!}@endif>Сначала дешевле</option>
            <option value="amount|desc" @if(request()->get('orderBy')&&request()->get('orderBy')=='amount|desc'){!! 'selected' !!}@endif>Сначала дороже</option>
            <option value="created_at|asc" @if(request()->get('orderBy')&&request()->get('orderBy')=='created_at|asc'){!! 'selected' !!}@endif>Сначала старые</option>
            <option value="created_at|desc" @if(request()->get('orderBy')&&request()->get('orderBy')=='created_at|desc'){!! 'selected' !!}@endif>Сначала новые</option>
          </select>
        </div>
      </div>
      <div class="p-1 w-full">
        <div class="form-group">
          <div class="flex items-center">
            <x-checkbox id="has_paid" name="has_paid" value="1"
                        :checked="request()->has_paid ? true : false"/>
            <x-input-label for="has_paid" class="ml-2" :value="__('Показать неоплаченные')"/>
          </div>
        </div>
      </div>
      <div class="p-1 w-full">
        <div class="form-group">
          <div class="flex items-center">
            <x-checkbox id="no_ticket" name="no_ticket" value="1"
                        :checked="request()->no_ticket ? true : false"/>
            <x-input-label for="no_ticket" class="ml-2" :value="__('Показать без этикетки')"/>
          </div>
        </div>
      </div>
      <div class="p-1 w-full">
        <div class="form-group">
          <div class="flex items-center">
            <x-checkbox id="preorder" name="preorder" value="1"
                        :checked="request()->preorder ? true : false"/>
            <x-input-label for="preorder" class="ml-2" :value="__('Показать предзаказы')"/>
          </div>
        </div>
      </div>
  </x-admin.search-form>
  <div class="mx-auto py-4">
    <div class="flex justify-between py-2">
      <div>Всего {{ $orders->total() }}</div>
      @if(auth()->user()->hasPermissionTo('Этикетки ШК'))
      <form action="{{ route('admin.requestTickets')  }}" id="request-tickets" method="POST">
        @csrf
      </form>
      @endif
      @if(auth()->user()->hasPermissionTo('Выгрузка заказов'))
      <form action="{{ route('admin.orders.export')  }}{{ stristr($_SERVER['REQUEST_URI'], '?') }}" id="export-orders" method="POST">
        @csrf
      </form>
      @endif
      <x-dropdown_menu>
        <x-slot name="content">
          <div class="py-1" role="none">
            @if(auth()->user()->hasPermissionTo('Выгрузка заказов'))
              <a class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1" href="#" onclick="if(confirm('Выгрузить в Excel выбранные заказы?'))document.getElementById('export-orders').submit();">
                Выгрузить в Excel
              </a>
              <a class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1" href="{{ route('admin.export_data.index') }}">
                Открыть выгруженные файлы
              </a>
            @endif
            @if(auth()->user()->hasPermissionTo('Этикетки ШК'))
              <a class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1" href="#" onclick="document.getElementById('request-tickets').submit();">
                Запросить этикетки ШК
              </a>
            @endif
            @if(auth()->user()->hasPermissionTo('Просмотр накладных'))
              <a class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1" href="{!! route('admin.invoices.create') !!}{{ stristr($_SERVER['REQUEST_URI'], '?') }}">
                Создать накладную
              </a>
            @endif
            <a class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1" href="javascript:;" data-src="{!! route('admin.orders.statistic.form') !!}{{ stristr($_SERVER['REQUEST_URI'], '?') }}" data-type="iframe" data-fancybox>
              Открыть статистику
            </a>
          </div>
        </x-slot>
      </x-dropdown_menu>
    </div>
    @if(auth()->id()==1)
      @foreach($orders as $order)
        {{ $order->id }},
      @endforeach
    @endif
    <div id="action-box" class="flex items-center flex-wrap -mx-1 hidden">
      <div class="p-1 w-full lg:w-1/3">
        <div class="form-group">
          <select name="action" form="action" class="form-control" id="do_action">
            <option>Действие с выбранными</option>
            @if(auth()->user()->hasPermissionTo('Редактирование заказов'))
            <optgroup label="Установить статус">
              <option value="set_status|is_processing">Cтатус "В обработке"</option>
              <option value="set_status|is_waiting">Cтатус "В ожидании"</option>
              <option value="set_status|was_processed">Cтатус "Обработан"</option>
              <option value="set_status|was_sended_to_store">Cтатус "Отправлен в сборку на склад"</option>
              <option value="set_status|is_assembled">Cтатус "Собран на складе"</option>
              <option value="set_status|is_ready">Cтатус "Готов к выдаче"</option>
              <option value="set_status|was_delivered">Cтатус "Выдан"</option>
              <option value="set_status|refund">Cтатус "Возврат"</option>
              <option value="set_status|address_error">Cтатус "Ошибка в адресе"</option>
              <option value="set_status|cancelled">Cтатус "Аннулирован"</option>
              <option value="set_status|test">Cтатус "Тест"</option>
              <option value="set_status|not_in_demand">Cтатус "Не востребован"</option>
              {{--              <option value="set_status|has_error">Cтатус "Ошибка в заказе"</option>--}}
            </optgroup>
            @endif

            @if(auth()->user()->hasPermissionTo('Отгрузка заказов'))

            <optgroup label="Доставка">
              <option value="delivery|send_to_boxberry">Отправить в Яндекс</option>
              <option value="delivery|send_to_cdek">Отправить в СДЭК</option>
              <option value="delivery|send_to_pochta">Отправить в Почту</option>
              <option value="delivery|send_to_x5post">Отправить в 5 Пост</option>
            </optgroup>
            @endif
            <optgroup label="Общее">
              <option value="check_status">Проверить статус (яндекс или cdek)</option>
            </optgroup>
          </select>
        </div>
      </div>
      <div class="p-1 w-full lg:w-2/3 flex justify-end">
        <button class="button button-success" id="actioncell_submit" form="action">Применить</button>
      </div>
    </div>
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
        <thead>
        <tr>
          <th class="bg-gray-100 border p-2" style="width: 2%">
            <input type="checkbox" class="action" id="check_all">
          </th>
          <th class="bg-gray-100 border p-2" style="width: 5%">#</th>
          <th class="bg-gray-100 border p-2" style="width: 15%">ФИО</th>
          <th class="bg-gray-100 border p-2" style="width: 20%">Контакты</th>
          <th class="bg-gray-100 border p-2" style="width: 20%">Корзина</th>
          <th class="bg-gray-100 border p-2">Сумма</th>
          <th class="bg-gray-100 border p-2" style="width: 10%">Доставка</th>
          <th class="bg-gray-100 border p-2">Статус</th>
          <th class="bg-gray-100 border p-2" style="width:120px"></th>
        </tr>
        </thead>
        <tbody>
        @foreach($orders as $order)
          <tr @if(!$order->confirm)class="text-gray-400"@endif >
            <td class="border p-2">
              <input type="checkbox" name="order_ids[]" form="action" value="{{ $order->id }}" class="action" id="checkbox_{{ $order->id }}">
            </td>
            <td class="border p-1">
              {{ $order->getOrderNumber() }}
              @if($order->preorder)
                <span class="badge-yellow text-xs whitespace-nowrap">Предзаказ</span>
              @endif
              @if(isset($order->copied))
                <span class="badge-orange text-xs whitespace-nowrap" title="Этот заказ был продублирован">{{ $order->copied }}</span>
              @elseif(isset($order->double))
                <span class="badge-blue text-xs whitespace-nowrap" title="Это копия заказа {{ $order->double }}">{{ $order->double }}</span>
              @endif
              <div class="whitespace-nowrap text-xs text-gray-400">{{ date('d.m.Y H:i', strtotime($order->created_at)) }}</div>
            </td>
            <td class="border p-1">
              {!! str_replace(' ', '<br/>', $order->full_name) !!}
            </td>
            <td class="border p-1">
              {{ $order->phone }}<br/>
              <span class="text-break"><a href="{{ route('admin.users.edit', ['user' => $order->user_id]) }}">{{ $order->email }}</a></span>
            </td>
            <td class="border p-1 text-left">
              <table class="w-full text-xs">
                @foreach($order->data_cart as $key => $item)
                  <tr>
                    <td class="border-b border-b-gray-200">{{ $item['model'] }}</td>
                    <td class="border-b border-b-gray-200 text-right"><span class="font-bold">{{ $item['qty'] }}шт</span></td>
                  </tr>
                @endforeach
              </table>

            </td>
{{--            @if(request()->show_cart)--}}
{{--            <td class="border p-1 text-left">--}}
{{--              <table class="w-full text-xs">--}}
{{--                @foreach($order->data_cart as $key => $item)--}}
{{--                  <tr>--}}
{{--                    <td class="border-b border-b-gray-200">{{ $item['model'] }}</td>--}}
{{--                    <td class="border-b border-b-gray-200 text-right"><span class="font-bold">{{ $item['qty'] }}шт</span></td>--}}
{{--                  </tr>--}}
{{--                @endforeach--}}
{{--              </table>--}}

{{--            </td>--}}
{{--            @endif--}}
            <td class="border p-1 whitespace-nowrap">
              {{ formatPrice($order->amount) }}
            </td>
            <td class="border p-1">
              {!! isset($order->data_shipping['ticket']) ? "<i class=\"fas fa-file-invoice\"></i> " : '' !!}{{ $order->data_shipping['shipping-method'] }}
            </td>
            <td class="border p-1 text-center">
              {!! $order->getStatusBadge('text-xs') !!}
              <span id="comment-{{ $order->id }}" class="block text-xs">{{ $order->comment ?? '' }}</span>
            </td>
            <td class="border p-1">
              @if(auth()->user()->hasPermissionTo('Комментирование заказов'))
                <div id="comment-order-{{ $order->id }}" class="hidden w-full max-w-2xl">
                  <form action="{{ route('admin.orders.order_comment') }}" class="p-4 add_comment">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    <div class="form-group">
                      <x-input-label for="comment" :value="__('Комментарий к заказу ').$order->getOrderNumber()" />
                      <x-textarea name="comment" id="comment" class="mt-1 w-full">{{ $order->comment }}</x-textarea>
                    </div>
                    <x-primary-button>Добавить</x-primary-button>
                  </form>
                </div>

              @endif

                <form action="{{ route('admin.orders.order_copy', $order->slug)  }}" id="copy-order-{{ $order->id }}" method="POST">
                  @csrf
                </form>
                <form action="{{ route('admin.orders.checkStatus', $order->slug)  }}" id="check-status-{{ $order->id }}" method="POST">
                  @csrf
                </form>
              <x-dropdown_menu>
                <x-slot name="content">
                  <div class="py-1" role="none">
                    @if(!$order->confirm && $order->status != 'cancelled')
                      <a href="#" data-copy="{{ route('order.robokassa', $order->slug)  }}" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline copy-to-clipboard" role="menuitem" tabindex="-1">Ссылка на оплату</a>
                    @endif
                    <a href="{{ route('admin.orders.show', $order->slug)  }}" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1">Открыть</a>
                    @if(auth()->user()->hasPermissionTo('Редактирование заказов'))
                      <a href="{{ route('admin.orders.edit', $order->slug)  }}" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1">Редактировать заказ</a>
                    @endif
                    @if(auth()->user()->hasPermissionTo('Управление корзиной'))
                      <a href="{{ route('admin.orders.editCart', $order->slug)  }}" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1">Редактировать корзину</a>
                    @endif
                    @if(auth()->user()->hasPermissionTo('Копирование заказов'))
                      <a href="#" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1" onclick="if(confirm('Копировать заказ {{ $order->getOrderNumber() }}?'))document.getElementById('copy-order-{{ $order->id }}').submit();">Копировать заказ</a>
                    @endif
                    @if(auth()->user()->hasPermissionTo('Комментирование заказов'))
                    <a href="javascript:;" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1" data-fancybox="comment-order-{{ $order->id }}" data-src="#comment-order-{{ $order->id }}">Добавить комментарий</a>
                    @endif
                    @if(in_array($order->data_shipping['shipping-code'], ['cdek','cdek_courier',  'yandex']))
                      <a href="#" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1" onclick="document.getElementById('check-status-{{ $order->id }}').submit();">Проверить статус заказа</a>
                    @endif
                    @if(auth()->user()->hasPermissionTo('Просмотр накладных')&&$order->invoices()->count())
                      <a class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1" href="{{ route('admin.invoices.index', ['order_id' => $order->id]) }}">Накладные</a>
                    @endif

                  </div>
                </x-slot>
              </x-dropdown_menu>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    <div class="p-2">
      {{ $orders->appends(request()->input())->links('pagination::tailwind') }}
    </div>
  </div>
  <form action="{{ route('admin.orders.batchUpdate') }}" id="action" method="POST">
    @csrf
    @method('PUT')
  </form>
  <div class="text-xs"></div>
  <div class="text-base"></div>
  <div class="text-md"></div>
  <div class="text-sm"></div>
  <div class="text-lg"></div>
</x-admin-layout>
