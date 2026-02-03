@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <div class="border-b">
    <div class="mx-auto px-2 sm:px-3 lg:px-4">
      <nav class="-mb-px flex flex-nowrap flex-col sm:flex-row sm:justify-start space-y-2 sm:space-y-0 sm:space-x-4"
           aria-label="Tabs" role="tablist">
        <button type="button"
                class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                id="tab-1" aria-selected="true" role="tab" aria-controls="tab-1-content">О заказе
        </button>
        <button type="button"
                class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                id="tab-2" aria-selected="true" role="tab" aria-controls="tab-2-content">История статусов
        </button>
        @if($order->giftCoupons()->exists())
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-3" aria-selected="true" role="tab" aria-controls="tab-3-content">Подарки
          </button>
        @endif
        @if($catInBagSession && $catInBagSession->bags->count() > 0)
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-cat-in-bag" aria-selected="true" role="tab"
                  aria-controls="tab-cat-in-bag-content">Подарок Кот в мешке
          </button>
        @endif
        @if(auth()->id()==1)
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-99" aria-selected="true" role="tab" aria-controls="tab-99-content">Данные о доставке
          </button>
        @endif
        <div class="flex-1 flex justify-end items-center">
          @if(auth()->user()->hasPermissionTo('Комментирование заказов'))
            <div id="comment-order-{{ $order->id }}" class="hidden w-full max-w-2xl">
              <form action="{{ route('admin.orders.order_comment') }}" class="p-4 add_comment">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <div class="form-group">
                  <x-input-label for="comment" :value="__('Комментарий к заказу ').$order->getOrderNumber()"/>
                  <x-textarea name="comment" id="comment" class="mt-1 w-full"></x-textarea>
                </div>
                <x-primary-button>Добавить</x-primary-button>
              </form>
            </div>

          @endif
          <form action="{{ route('admin.orders.mailResend', $order->slug)  }}" id="resend-mail-{{ $order->id }}"
                method="POST">
            @csrf
          </form>
          <form action="{{ route('admin.orders.checkStatus', $order->slug)  }}" id="check-status-{{ $order->id }}"
                method="POST">
            @csrf
          </form>
          <x-dropdown_menu>
            <x-slot name="content">
              <div class="py-1" role="none">
                @if(!$order->confirm and $order->status != 'cancelled')
                  <a href="#" data-copy="{{ route('order.robokassa', $order->slug)  }}"
                     class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline copy-to-clipboard"
                     role="menuitem" tabindex="-1">Ссылка на оплату</a>
                @endif
                @if(auth()->user()->hasPermissionTo('Редактирование заказов'))
                  <a href="{{ route('admin.orders.edit', $order->slug)  }}"
                     class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem"
                     tabindex="-1">Редактировать заказ</a>
                @endif
                @if(auth()->user()->hasPermissionTo('Управление корзиной'))
                  <a href="{{ route('admin.orders.editCart', $order->slug)  }}"
                     class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem"
                     tabindex="-1">Редактировать корзину</a>
                @endif
                @if(auth()->user()->hasPermissionTo('Комментирование заказов'))
                  <a href="javascript:;" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline"
                     role="menuitem" tabindex="-1" data-fancybox="comment-order-{{ $order->id }}"
                     data-src="#comment-order-{{ $order->id }}">Добавить комментарий</a>
                @endif
                <a href="#" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem"
                   tabindex="-1" onclick="document.getElementById('resend-mail-{{ $order->id }}').submit();">Переотправить
                  письмо</a>
                @if(in_array($order->data_shipping['shipping-code'], ['cdek','cdek_courier',  'yandex']))
                  <a href="#" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline"
                     role="menuitem" tabindex="-1"
                     onclick="document.getElementById('check-status-{{ $order->id }}').submit();">Проверить статус
                    заказа</a>
                @endif

                @if(auth()->user()->hasPermissionTo('Создание заказов'))
                  <a href="{{ route('admin.orders.create', ['email' => $order->user->email])  }}"
                     class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem"
                     tabindex="-1">Создать дополнительный заказ</a>
                @endif
                {{--                  <a href="#" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1">Накладные</a>--}}
              </div>
            </x-slot>
          </x-dropdown_menu>
        </div>
      </nav>
    </div>
  </div>

  <div class="mx-auto px-2 sm:px-3 lg:px-4 py-6" id="tab-content">
    <div id="tab-1-content" role="tabpanel">
      <div class="w-full">
        <p><strong>Номер заказа:</strong> {{ $order->getOrderNumber() }}</p>
        <p><strong>Дата заказа:</strong> {{ date('d.m.Y H:i:s', strtotime($order->created_at)) }}</p>
        <p><strong>Статус заказа:</strong> {!! $order->getStatusBadge('text-sm') !!}</p>
        <div id="comment-{{ $order->id }}">
          @if(isset($order->data['comment']))
            <p><strong>Комментарий:</strong> <span>{{ $order->data['comment'] ?? '' }}</span></p>
          @endif
        </div>
        <div class="flex flex-wrap -m-2 py-8 items-stretch">
          <div class="p-2 w-full md:w-auto flex-initial">
            <div class="rounded-md shadow-md bg-gray-50 py-2 px-4 text-sm h-full">
              <h2 class="text-lg font-semibold mb-2">Информация о покупателе</h2>
              <p><strong>ФИО клиента:</strong> {{ $order->data['form']['full_name'] }}</p>
              <p><strong>Телефон:</strong> {{ $order->data['form']['phone'] }}</p>
              <p><strong>Email:</strong> {{ $order->data['form']['email'] }}</p>
            </div>
          </div>
          @if(!isset($order->data['is_voucher'])||!$order->data['is_voucher'])
            <div class="p-2 w-full md:w-auto flex-initial">
              <div class="rounded-md shadow-md bg-gray-50 py-2 px-4 text-sm h-full">
                <h2 class="text-lg font-semibold mb-2">Информация о доставке</h2>
                <p><strong>Способ доставки:</strong> {{ $order->data_shipping['shipping-method'] }}</p>
                @if($order->data_shipping['shipping-code'] == 'ozon')
                  <p><strong>Адрес доставки:</strong> {{ $order->data_shipping['ozon-pvz-address'] }}</p>
                  @if(isset($order->data_shipping['ozon']['logisticOrderNumber'])&&!empty($order->data_shipping['ozon']['logisticOrderNumber']))
                    <p><span
                        class="font-weight-bold">Трек-номер:</span> {{ $order->data_shipping['ozon']['logisticOrderNumber'] }}
                      <a
                        href="https://rocket.ozon.ru/tracking/?SearchId={{ $order->data_shipping['ozon']['logisticOrderNumber'] }}"
                        target="_blank">Отследить заказ</a></p>
                  @endif
                @elseif($order->data_shipping['shipping-code'] == 'boxberry')
                  <p><strong>Адрес доставки:</strong> {{ $order->data_shipping['boxberry-pvz-address'] }}</p>
                  @if(isset($order->data_shipping['boxberry']['track'])&&!empty($order->data_shipping['boxberry']['track']))
                    <p><span
                        class="font-weight-bold">Трек-номер:</span> {{ $order->data_shipping['boxberry']['track'] }} <a
                        href="https://boxberry.ru/tracking-page?id={{ $order->data_shipping['boxberry']['track'] }}"
                        target="_blank">Отследить заказ</a></p>
                  @endif
                @elseif($order->data_shipping['shipping-code'] == 'yandex')
                  <p><strong>Адрес доставки:</strong> {{ $order->data_shipping['yandex-pvz-address'] }}</p>
                  @if(isset($order->data_shipping['yandex']['track'])&&!empty($order->data_shipping['yandex']['track']))
                    <p><span
                        class="font-weight-bold">Трек-номер:</span> {{ $order->data_shipping['yandex']['track'] }} <a
                        href="#?id={{ $order->data_shipping['yandex']['track'] }}"
                        target="_blank">Отследить заказ</a></p>
                  @endif
                @elseif($order->data_shipping['shipping-code'] == 'x5post')
                  <p><strong>Адрес доставки:</strong> {{ $order->data_shipping['x5post-pvz-address'] }}</p>
                  @if(isset($order->data_shipping['x5post']['senderOrderId'])&&!empty($order->data_shipping['x5post']['senderOrderId']))
                    <p><span
                        class="font-weight-bold">Трек-номер:</span> {{ $order->data_shipping['x5post']['senderOrderId'] }}
                      <a href="https://fivepost.ru/tracking/?id={{ $order->data_shipping['x5post']['senderOrderId'] }}"
                         target="_blank">Отследить заказ</a></p>
                  @endif
                @elseif($order->data_shipping['shipping-code'] == 'cdek')
                  <p><strong>Адрес доставки:</strong> {{ $order->data_shipping['region'] ?? '' }}
                    , {{ $order->data_shipping['city'] ?? '' }},
                    {{ $order->data_shipping['cdek-pvz-address'] }}</p>
                  @if(isset($order->data_shipping['cdek']['invoice_number'])&&!empty($order->data_shipping['cdek']['invoice_number']))
                    <p><span
                        class="font-weight-bold">Трек-номер:</span> {{ $order->data_shipping['cdek']['invoice_number'] }}
                      <a
                        href="https://www.cdek.ru/ru/tracking?order_id={{ $order->data_shipping['cdek']['invoice_number'] }}"
                        target="_blank">Отследить заказ</a></p>
                  @endif
                @elseif($order->data_shipping['shipping-code'] == 'cdek_courier')
                  <p><strong>Адрес доставки:</strong>
                    {{ $order->data_shipping['cdek_courier-form-address'] }}</p>
                  @if(isset($order->data_shipping['cdek_courier']['invoice_number'])&&!empty($order->data_shipping['cdek_courier']['invoice_number']))
                    <p><span
                        class="font-weight-bold">Трек-номер:</span> {{ $order->data_shipping['cdek_courier']['invoice_number'] }}
                      <a
                        href="https://www.cdek.ru/ru/tracking?order_id={{ $order->data_shipping['cdek_courier']['invoice_number'] }}"
                        target="_blank">Отследить заказ</a></p>
                  @endif
                @elseif($order->data_shipping['shipping-code'] == 'pochta')
                  <p><strong>Адрес доставки:</strong>
                    {{ $order->data_shipping['full_address'] }}</p>
                  @if(isset($order->data_shipping['pochta']['barcode'])&&!empty($order->data_shipping['pochta']['barcode']))
                    <p><span
                        class="font-weight-bold">Трек-номер:</span> {{ $order->data_shipping['pochta']['barcode'] }}</p>
                  @endif
                @elseif($order->data_shipping['shipping-code'] == 'bxb')
                  <p><strong>Адрес доставки:</strong>
                    {{ $order->data_shipping['full_address'] }}</p>
                  {{--                    @if(isset($order->data_shipping['pochta']['barcode'])&&!empty($order->data_shipping['pochta']['barcode']))--}}
                  {{--                      <br/><span class="font-weight-bold">Трек-номер:</span> {{ $order->data_shipping['pochta']['barcode'] }}--}}
                  {{--                    @endif--}}
                @elseif(isset($pickup->code)&&$order->data_shipping['shipping-code'] == $pickup->code)
                  <p><strong>Самовывоз по адресу:</strong>
                    {{ $pickup->address }}, <br/>{!! $pickup->params['times'] ?? '' !!}<br/>Тел. {{ $pickup->phone }}
                  </p>
                @endif
              </div>
            </div>
          @endif
        </div>
        <div class="mx-auto py-4">
          <p class="mb-2"><strong>Корзина:</strong></p>
          <div class="relative overflow-x-auto">
            <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
              <thead>
              <tr>
                <th class="bg-gray-100 border p-2 text-left">Наименование</th>
                <th class="bg-gray-100 border p-2">Артикул</th>
                <th class="bg-gray-100 border p-2">Объем</th>
                <th class="bg-gray-100 border p-2">Количество</th>
                <th class="bg-gray-100 border p-2">Цена</th>
                <th class="bg-gray-100 border p-2">Сумма</th>
              </tr>
              </thead>
              <tbody>
              @foreach($order->data_cart as $item)
                <tr>
                  <td class="border p-2 text-left">
                    {{ $item['id'] }} {{ $item['name'] }}
                    @if(in_array($item['id'], [20,21,22])&&isset($item['vouchers']))
                      @foreach($item['vouchers'] as $voucher)
                        <br/><a href="{{ asset('voucher/'.$voucher.'.png') }}" target="_blank">Открыть сертификат</a>
                      @endforeach
                    @endif
                    @if(isset($item['builder']))
                      <div>
                        @foreach($item['builder'] as $builder_item)
                          - {{ $builder_item['name'] }}<br/>
                        @endforeach
                      </div>
                    @endif
                  </td>
                  <td class="border p-2">{{ $item['model'] }}</td>
                  <td class="border p-2">{{ $item['volume'] ?? '' }}</td>
                  <td class="border p-2">{{ $item['qty'] }}</td>
                  <td class="border p-2">{{ formatPrice($item['price']) }}</td>
                  <td class="border p-2">{{ formatPrice($item['price'] * $item['qty']) }}</td>
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        </div>
        @if($catInBagSession && $catInBagSession->bags->whereNotNull('opened_at')->count())
          @php
            $openedCatBags = $catInBagSession->bags->whereNotNull('opened_at')->values();
          @endphp
          <div class="mx-auto py-4">
            <p class="mb-2"><strong>Подарки «Кот в мешке»:</strong></p>
            <div class="relative overflow-x-auto">
              <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
                <thead>
                <tr>
                  <th class="bg-gray-100 border p-2 text-left">Наименование</th>
                  <th class="bg-gray-100 border p-2">Артикул</th>
                  <th class="bg-gray-100 border p-2">Количество</th>
                  <th class="bg-gray-100 border p-2">Цена</th>
                </tr>
                </thead>
                <tbody>
                @foreach($openedCatBags as $bag)
                  @php
                    $giftProduct = $bag->product ?? $bag->prize?->product;
                    $giftName = $bag->prize?->name ?? $giftProduct?->name ?? 'Подарок';
                    $giftPrice = 0;
                    if ($bag->prize_type === 'certificate' && empty($giftProduct?->name) && !empty($bag->nominal)) {
                      $giftName = 'Сертификат на ' . number_format($bag->nominal, 0, '.', ' ') . ' ₽';
                    }
                  @endphp
                  <tr>
                    <td class="border p-2 text-left">{{ $giftName }}</td>
                    <td class="border p-2">{{ $giftProduct?->article ?? $giftProduct?->sku ?? '' }}</td>
                    <td class="border p-2">1</td>
                    <td class="border p-2">{{ formatPrice($giftPrice) }}</td>
                  </tr>
                @endforeach
                </tbody>
              </table>
            </div>
          </div>
        @endif
        <div class="mx-auto">
          <div class="flex justify-end">
            <div class="w-full md:w-auto flex-initial">
              <div class="rounded-md shadow-md bg-gray-50 overflow-hidden h-full">
                <table class="overflow-hidden">
                  <tr>
                    <td class="border p-2">Сумма товаров:</td>
                    <td class="border p-2">{{ formatPrice($order->data['total']) }}</td>
                  </tr>
                  @if(isset($order->data['discount'])&&$order->data['discount'] > 0)
                    <tr>
                      <td class="border p-2">
                        @if(isset($order->data['promocode']['code']))
                          Промокод ({{ $order->data['promocode']['code'] }})
                        @elseif(isset($order->data['voucher']['code']))
                          Подарочный сертификат ({{ $order->data['voucher']['code'] }})
                        @else
                          Скидка
                        @endif
                      </td>
                      <td class="border p-2">
                        -{{ formatPrice($order->data_shipping['price']+$order->data['total']-$order->amount) }}</td>
                    </tr>
                  @endif
                  @if(isset($order->data_shipping['price'])&&$order->data_shipping['price']>0)
                    <tr>
                      <td class="border p-2">{{ $order->data_shipping['shipping-method'] }}</td>
                      <td class="border p-2">{{ formatPrice($order->data_shipping['price']) }}</td>
                    </tr>
                  @endif
                  <tr>
                    <td class="border p-2">Итого:</td>
                    <td class="border p-2">{{ formatPrice($order->amount) }}</td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div id="tab-2-content" role="tabpanel">
      <ol class="relative border-l border-gray-200">
        <li class="ml-4">
          <div class="absolute w-3 h-3 bg-orange-400 rounded-full mt-1.5 -left-1.5 border border-white"></div>
          <div class="mb-2">
            <time
              class="mb-1 text-sm font-normal leading-none text-gray-400">{{ getRusDate(strtotime($order->created_at)) }}</time>
          </div>
          <div class="flex items-center justify-between mb-3">
            <span class="badge badge-gray">Заказ создан</span>
            <div
              class=" text-sm font-normal leading-none text-gray-400 ml-2">{{ date('H:i', strtotime($order->created_at)) }}</div>
          </div>
        </li>
        @php
          $this_date = null;
        @endphp
        @if(isset($order->status_history))
          @php
            $this_date = date('d.m.Y', strtotime($order->created_at));
          @endphp
          @foreach($order->status_history as $status)
            <li class="ml-4">
              @php
                $staus_obj = \App\Models\Status::where('key', $status->status)->first();
              @endphp
              @if(($this_date ?? null) != date('d.m.Y', strtotime($status->created_at)))
                @php
                  $this_date = date('d.m.Y', strtotime($status->created_at));
                @endphp
                <div class="absolute w-3 h-3 bg-orange-400 rounded-full mt-1.5 -left-1.5 border border-white"></div>
                <div class="mb-2">
                  <time
                    class="mb-1 text-sm font-normal leading-none text-gray-400">{{ getRusDate(strtotime($status->created_at)) }}</time>
                </div>
              @endif
              <div class="flex items-center justify-between mb-3">
                <span
                  class="badge badge-{{ getBootstrapColor($staus_obj->color) }}">{{ $staus_obj->name ?? $status->status }}</span>
                <div
                  class=" text-sm font-normal leading-none text-gray-400 ml-2">{{ date('H:i', strtotime($status->created_at)) }}</div>
              </div>
            </li>
          @endforeach
        @endif
      </ol>
    </div>
    @if($order->giftCoupons()->exists())
      <div id="tab-3-content" role="tabpanel">
        Ссылка на купоны {{ route('happy_coupon', $order->slug) }}
        <div class="mx-auto py-4">
          <div class="relative overflow-x-auto">
            <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
              <thead>
              <tr>
                <th class="bg-gray-100 border p-2 text-center">#</th>
                <th class="bg-gray-100 border p-2">Код</th>
                <th class="bg-gray-100 border p-2">Подарок</th>
                <th class="bg-gray-100 border p-2">Дата присвоения</th>
              </tr>
              </thead>
              <tbody>
              @foreach($order->giftCoupons()->get() as $i => $giftCoupon)
                <tr>
                  <td class="border p-2">{{ $i+1 }}</td>
                  <td class="border p-2">{{ $giftCoupon->code }}</td>
                  <td class="border p-2">{{ $giftCoupon->prize->name ?? '' }}</td>
                  <td class="border p-2">{{ \Carbon\Carbon::parse($giftCoupon->created_at)->format('d.m.Y H:i') }}</td>
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    @endif
    @if($catInBagSession && $catInBagSession->bags->count() > 0)
      <div id="tab-cat-in-bag-content" role="tabpanel">
        <div class="mx-auto py-4">
          <div class="relative overflow-x-auto">
            <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
              <thead>
              <tr>
                <th class="bg-gray-100 border p-2 text-center">#</th>
                <th class="bg-gray-100 border p-2">Код</th>
                <th class="bg-gray-100 border p-2">Подарок</th>
                <th class="bg-gray-100 border p-2">Присвоен</th>
                <th class="bg-gray-100 border p-2">Открыт</th>
                <th class="bg-gray-100 border p-2"></th>
              </tr>
              </thead>
              <tbody>
              @foreach($catInBagSession->bags->sortBy('position') as $i => $bag)
                @php
                  $isOpened = (bool)$bag->opened_at;
                  $giftCode = $bag->data['gift_code'] ?? ($bag->data['voucher_code'] ?? '');
                  $giftName = $bag->prize?->name ?? $bag->prize?->product?->name ?? $bag->product?->name ?? '';
                  if ($bag->prize_type === 'certificate' && empty($giftName) && !empty($bag->nominal)) {
                    $giftName = 'Сертификат на ' . number_format($bag->nominal, 0, '.', ' ') . ' ₽';
                  }
                  $displayGiftName = $giftName ?: 'Подарок';
                @endphp
                <tr>
                  <td class="border p-2">{{ $i + 1 }}</td>
                  <td class="border p-2">{{ $giftCode ?: '—' }}</td>
                  <td class="border p-2">{{ $displayGiftName }}</td>
                  <td class="border p-2">{{ \Carbon\Carbon::parse($bag->created_at)->format('d.m.Y H:i') }}</td>
                  <td class="border p-2">
                    {{ $isOpened ? \Carbon\Carbon::parse($bag->opened_at)->format('d.m.Y H:i') : '—' }}
                  </td>
                  <td class="border p-2">
                    @if($isOpened)
                      <i class="fas fa-eye text-green-600"></i>
                    @else
                      <i class="fas fa-eye-slash text-gray-400"></i>
                    @endif
                  </td>
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    @endif
    @if(auth()->id()==1)
      <div id="tab-99-content" role="tabpanel">
      <pre>
        {{ print_r($order->data, true) }}
      </pre>
        <pre>
        {{ print_r($order->data_shipping, true) }}
      </pre>
        <pre>
        {{ print_r($order->data_cart, true) }}
      </pre>
      </div>
    @endif
  </div>
</x-admin-layout>
