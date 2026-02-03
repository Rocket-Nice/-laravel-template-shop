@section('title', $seo['title'] ?? config('app.name'))
<x-cabinet-layout>
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-6 sm:py-9 md:py-12">
    <div>
      <h1 class="flex-1 d-headline-1 m-headline-1 text-left md:text-center cormorantInfant">{{ $seo['title'] }}</h1>
    </div>
  </div>
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16">
    <div id="wrapper" class="flex relative">
      <div id="leftMenu"
           class="hidden lg:block min-w-[260px] md:w-[24.936061%] relative top-0 border-r border-r-myGreen sm:mr-[15px] md:mr-[45px] lg:mr-[74px] xl:mr-[104px] 2xl:mr-[148px]">
        <div id="leftMenu-content" class="relative">
          <div
            class="pb-6 sm:pb-9 md:pb-12 space-y-6 px-6 z-20 fixed top-0 right-0 w-full max-w-[390px] h-screen bg-myLightGray shadow-xl transform translate-x-full transition-transform duration-300 overflow-y-auto lg:shadow-none lg:relative lg:top-auto lg:right-auto lg:overflow-y-visible lg:translate-x-0 lg:bg-transparent">
            @include('_parts.cabinet.leftMenu')
          </div>
        </div>
      </div>
      <!-- Main Content -->
      <div class="flex-1">
        <div>
          <div class="flex flex-col justify-between min-h-screen d-text-body m-text-body">
            @if(!$order->confirm&&$order->status!='cancelled')
              <div class="text-center mt-6 sm:mt-9 lg:mt-12 lg:mb-12">
                <x-public.primary-button href="{{ route('order.robokassa', $order->slug) }}"
                                         class="h-11 md:h-[58px] px-5 mx-auto text-sm sm:text-md md:text-lg lg:text-xl">
                  Оплатить заказ
                </x-public.primary-button>
              </div>
            @endif
            @if(isset($order->data['store_coupon'])&&$order->data['store_coupon'])
              <div class="mb-12 bg-myGreen2 text-white p-6 m-text-body d-text-body text-center space-y-4">
                Данный заказ из оффлайн магазина. Заказ был создан для участия в акции «Счастливый купон» по
                купону {{ $order->storeCoupon?->code }}
              </div>
            @endif
            @if($order->giftCoupons()->exists())
              @if($order->giftCoupons()->where('data->position', null)->exists())
                <div class="mb-12 text-center">
                  <x-public.winter-button href="{{ route('happy_coupon', $order->slug) }}">Открыть купоны
                  </x-public.winter-button>
                </div>
              @else
                {{--                  <div class="mb-12">--}}
                {{--                    <h2 class="text-sm sm:text-base md:text-lg lg:text-xl xl:text-2xl uppercase mb-6">Подарки</h2>--}}
                {{--                    <table id="table-gifts" class=" border-t border-customBrown text-sm sm:text-md md:text-lg lg:text-xl table-auto w-full border-collapse leading-none">--}}
                {{--                      <tbody>--}}
                {{--                      @foreach($order->giftCoupons()->get() as $giftCoupon)--}}
                {{--                        @php($prize = $giftCoupon->prize)--}}
                {{--                        <tr>--}}
                {{--                          <td class="text-left border-b border-customBrown py-4" title="Наименование подарка"><span class="whitespace-nowrap">{{ $prize->name }}</span></td>--}}
                {{--                          <td class="border-b border-customBrown py-4 text-right" title="Код купона">{{ $giftCoupon->code }}</td>--}}
                {{--                        </tr>--}}
                {{--                      @endforeach--}}
                {{--                      </tbody>--}}
                {{--                    </table>--}}
                {{--                  </div>--}}
              @endif
            @endif
            @php($catInBagSession = (getSettings('catInBag') && $order->confirm)
                ? \App\Models\CatInBagSession::query()->with(['bags.prize.product'])->where('order_id', $order->id)->first()
                : null)
            @php($catInBagCategoryIds = $catInBagSession?->visible_category_ids ?? [])
            @php($catInBagBags = $catInBagSession?->bags ?? collect())
            @php($catInBagOpenedCount = $catInBagSession?->opened_count ?? $catInBagBags->whereNotNull('opened_at')->count())
            @php($catInBagOpenLimit = $catInBagSession?->open_limit ?? 0)
            @php($catInBagHasUnopened = $catInBagOpenLimit > $catInBagOpenedCount)
            @php($catInBagGiftBags = $catInBagBags->filter(function ($bag) {
                return $bag->opened_at && $bag->prize_id && $bag->prize_type !== 'empty';
            }))
            @php($catInBagGifts = $catInBagGiftBags->map(function ($bag) {
                $prize = $bag->prize;
                $image = $prize?->data['image']['img'] ?? $prize?->data['image']['thumb'] ?? $prize?->image ?? null;
                $price = $bag->nominal ?? $prize?->product?->price ?? 0;
                return [
                    'name' => $prize?->name ?? $prize?->product?->name ?? 'Подарок',
                    'image' => $image,
                    'price' => (int)$price,
                ];
            })->values())
            @php($catInBagGiftsTotal = $catInBagGifts->sum('price'))
            @if(getSettings('catInBag') && $order->confirm && $catInBagSession)
              @if($catInBagHasUnopened)
                <div class="mb-12">
                  <x-cat-get-benefit />
                </div>
              @endif
              <x-cat-bags :bag-count="$catInBagSession->bag_count" :open-limit="$catInBagSession->open_limit" :category-ids="$catInBagCategoryIds" :order-id="$order->id" :order-slug="$order->slug" />
            @endif
            @if(!isset($order->data['store_coupon'])||!$order->data['store_coupon'])
              <div class="w-full">
                <h2 class="text-sm sm:text-base md:text-lg lg:text-xl xl:text-2xl uppercase mb-6">Товары</h2>

                <div class="relative overflow-x-auto mb-12">
                  <table id="table-cart"
                         class="table-auto w-full text-center border-collapse border border-gray-200 !leading-none text-sm md:text-xl lg:text-2xl">
                    <thead>
                    <tr>
                      <th class="bg-gray-100 border p-2">Наименование</th>
                      <th class="bg-gray-100 border p-2 hidden sm:table-cell">Цена</th>
                      <th class="bg-gray-100 border p-2">Количество</th>
                      <th class="bg-gray-100 border p-2">Сумма</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($cart))
                    @foreach($cart as $item)
                      @if(isset($item['raffle']))
                        @php($raffle = \App\Models\GiftCoupon::query()->where('code', $item['raffle'])->where('data->position', '!=', null)->first())
                        @if(!$raffle)
                          @continue
                        @endif
                      @endif
                      <tr class="cart-item">
                        <td class="border p-2">
                          <span>{{ $item['name'] }}</span><br/><span
                            class="text-xs sm:text-md md:text-lg">Артикул: {{ $item['model'] }}</span>
                          @if(isset($item['vouchers']))
                            @foreach($item['vouchers'] as $voucher)
                              <br/><a href="{{ $voucher[2] }}" target="_blank">{{ $voucher[1] }}</a>
                            @endforeach
                          @endif
                          @if($order->canAddReview())
                            @php($product = \App\Models\Product::find($item['id']))
                            @if($product && $product->type_id == 5)
                              @php($product = $product->parent)
                            @endif
                            @if($product && $product->type_id == 1 && !$product->comments()->where('user_id', auth()->id())->exists())
                              <div>
                                <a href="javascript:;" data-rating-form data-fancybox-no-close-btn
                                   data-src="#review-form-{{ $item['id'] }}"
                                   class="underline hover:no-underline uppercase text-xs md:text-sm text-myGreen2 font-bold">Оставить
                                  отзыв</a>
                              </div>
                              <x-public.popup id="review-form-{{ $item['id'] }}">
                                <x-slot name="icon">
                                  <h4 class="d-headline-4 m-headline-3 leading-none lh-outline-none">Ваш отзыв</h4>
                                </x-slot>
                                <x-public.review-form action="{{ route('product.review', $product->slug) }}"/>
                              </x-public.popup>
{{--                              <div id="review-form-{{ $item['id'] }}"--}}
{{--                                   class="!px-4 !py-6 sm:!py-[60px] sm:!px-6 w-full max-w-[547px]"--}}
{{--                                   style="display: none">--}}
{{--                                <div class="mb-12 flex items-center justify-between">--}}
{{--                                  <h4 class="d-headline-4 m-headline-3 leading-none lh-outline-none">Ваш отзыв</h4>--}}
{{--                                  <button class="outline-none" onclick="Fancybox.close()" tabindex="-1"><img--}}
{{--                                      src="{{ asset('img/icons/close-circle.svg') }}" alt="" class="w-6 h-6"></button>--}}
{{--                                </div>--}}
{{--                                <x-public.review-form action="{{ route('product.review', $product->slug) }}"/>--}}
{{--                              </div>--}}
                            @endif
                          @endif
                        </td>
                        <td class="border p-2 hidden sm:table-cell">
                          {!! formatPrice($item['price'], true) !!}
                        </td>
                        <td class="border p-2 relative">
                          <div class="max-w-[220px] w-full py-[5px] box-border mx-auto">
                            <div class="h-[24px] flex justify-between items-center">
                              <div class="flex-1 text-center mx-2 whitespace-nowrap"><span
                                  class="cormorantInfant">{{ $item['qty'] }}</span> шт.
                              </div>
                            </div>
                          </div>
                        </td>
                        <td class="border p-2 relative">
                  <span class="item-total">
                    {!! formatPrice($item['price']*$item['qty'], true) !!}
                  </span>
                        </td>
                      </tr>
                    @endforeach
                    @else
                    <tr>
                      <td colspan="5" class="p-4 text-gray-300 d-headline-4 m-headline-3">
                        пусто
                      </td>
                    </tr>
                    @endif
                    </tbody>
                  </table>
                </div>
                @if(getSettings('catInBag') && $order->confirm && $catInBagSession && $catInBagGifts->isNotEmpty())
                  <div class="mb-12">
                    <x-cat-bag-gifts :gifts="$catInBagGifts" :original-total="$catInBagGiftsTotal" />
                  </div>
                @endif
                @if(isset($gifts)&&!empty($gifts))
                  {{--                  <div class="mt-16 lg:mt-20">--}}
                  {{--                    <h2 class="text-lg lg:text-2xl font-montserrat uppercase ml-[120px] lg:ml-[150px]">Подарки LE--}}
                  {{--                      MOUSSE</h2>--}}
                  {{--                  </div>--}}
                  <div id="prizes" old_style="scroll-margin-top: 180px;"
                       class="px-3 pb-4 pt-2 mb-14 lg:mb-12 relative bg-cabinetGreen">
                    {{--                    <img src="{{ asset('img/happy_coupon/cabinet-gift.png?1') }}" alt=""--}}
                    {{--                         class="max-w-[110px] md:max-w-[150px]"--}}
                    {{--                         style="z-index: -1;position:absolute;left:0;top:0;transform:translateY(-60%);">--}}
                    <div class="p-4">
                      <h2 class="text-center text-3xl text-white uppercase">Подарки</h2>
                    </div>
                    <div id="table-gift" class="mb-6 relative z-10 bg-white px-2">
                      @php($total = 0)
                      @php($hasExpensive = false)
                      @foreach($gifts as $key => $item)
                        @if(isset($item['raffle']))
                          @php($raffle = \App\Models\GiftCoupon::query()->where('code', $item['raffle'])->where(function($query){
                                $query->where('data->position->count', '!=', null);
                            })->first())
                          @if(!$raffle)
                            @continue
                          @endif
                        @endif
                        <div class="cart-item flex bg-white py-2 border-b border-myDark last:border-b-0">
                          <div
                            class="w-[86px] sm:w-[100px] md:w-[120px] lg:w-[140px] xl:w-[169px] mr-4 md:mr-6 relative">
                            {{--                    @if(isset($item['raffle']) && $item['raffle'])--}}
                            {{--                      <img src="{{ asset('img/treasure_island/gift2.png') }}" alt="" class="absolute top-0 left-0 z-10" style="width: 20%;">--}}
                            {{--                    @endif--}}
                            @if(isset($item['image']) && $item['image'])
                              <div class="item-square">
                                <div>
                                  <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}"
                                       class="object-bottom object-cover block h-full mx-auto">
                                  <img src="{{ asset('img/happy_coupon/cart-gift.png') }}" alt="" class="w-[30px] lg:w-[60px]"
                                       style="position: absolute;right: 0;bottom:0;transform:translate(50%,20%);">
                                </div>
                              </div>
                            @elseif(isset($item['raffle']) && $item['raffle'])
                              <div class="item-square">
                                <img src="{{ asset('img/def1.jpg') }}" alt="{{ $item['name'] }}"
                                     class="object-bottom object-cover block">
                              </div>
                            @endif
                          </div>
                          <div class="flex-1 flex flex-col justify-between">
                            <div class="flex justify-between flex-1 max-w-full">
                              <div>
                                <h3 class="text-xl">{{ $item['name'] }}</h3>
                                {{--                      <div class="text-sm my-4">Артикул: {{ $item['model'] }}</div>--}}
                                {{--                              @if(in_array($item['id'], [271,311,312]))--}}
                                {{--                                <div>Как распорядиться своим «Золотым купоном» Вы можете по номеру в whatapp <a href="https://wa.me/79275020043">wa.me/79275020043</a></div>--}}
                                {{--                              @endif--}}
                                <div class="text-sm sm:text-base md:text-lg">
                                  @if(in_array($item['id'], [1222,1226]) )
                                    <div>Код вашего сертификата: {{ $item['raffle'] }}</div>
                                  @elseif($item['id'] == 1246)
                                    <a href="{{ asset('guides/Гайд по отечности.pdf') }}" target="_blank"
                                       class="underline">Открыть гайд</a>
                                  @elseif($item['id'] == 1247)
                                    <a href="{{ asset('guides/Гайд Полезные рецепты.pdf') }}" target="_blank"
                                       class="underline">Открыть гайд</a>
                                  @endif
                                </div>
                              </div>
                            </div>
                            @if($item['old_price'] ?? false)
                              <div class="flex items-center justify-between ">
                                <div class="text-2xl ">
                                  {{--                                <span class="cormorantInfant">{{ $item['qty'] }}</span> шт--}}
                                </div>
                                <div class="text-2xl font-medium  ml-6 flex italic text-myBrown">
                                  @php($total += $item['old_price'])
                                  <div
                                    class="font-medium line-through mr-2">{!! formatPrice($item['old_price'], true) !!}</div>
                                  {!! formatPrice($item['price'], true) !!}
                                </div>
                              </div>
                            @else
                              @php($hasExpensive = true)
                            @endif
                          </div>
                        </div>
                      @endforeach
                    </div>
                    {{--                    <div class="text-center text-sm lg:text-xl font-montserrat"><span class="cormorantInfant-">24</span>--}}
                    {{--                      ЧАСА ДАРИМ <span class="cormorantInfant-">30 000</span> ПОДАРКОВ!--}}
                    {{--                    </div>--}}
                    {{--                    @if(!$hasExpensive && $total > 0)--}}
                    {{--                      <div>--}}
                    {{--                        <div class="flex items-center justify-between">--}}
                    {{--                          <div class="text-sm md:text-base lg:text-lg xl:text-xl text-white uppercase font-bold">--}}
                    {{--                            Всего--}}
                    {{--                          </div>--}}
                    {{--                          <div class="text-sm md:text-base lg:text-lg xl:text-xl  uppercase font-bold text-white ml-6 flex space-x-3">--}}
                    {{--                            <div class="opacity-75 line-through">{{ formatPrice($total) }}</div>--}}
                    {{--                            <div>{{ formatPrice(0) }}</div>--}}
                    {{--                          </div>--}}
                    {{--                        </div>--}}
                    {{--                      </div>--}}
                    {{--                    @endif--}}
                  </div>
                @endif
              </div>
            @endif
            <div class="flex-1 space-y-12">
              <div>
                <h2 class="text-sm sm:text-base md:text-lg lg:text-xl xl:text-2xl uppercase mb-6">Данные заказа</h2>
                <table id="table-total"
                       class=" border-t border-customBrown text-sm sm:text-md md:text-lg lg:text-xl table-auto w-full border-collapse leading-none">
                  <tbody>
                  <tr>
                    <td class="text-left border-b border-customBrown py-4"><span class="whitespace-nowrap">Номер</span>
                    </td>
                    <td
                      class="border-b border-customBrown py-4 text-right cormorantInfant">{{ $order->getOrderNumber() }}</td>
                  </tr>
                  <tr>
                    <td class="text-left border-b border-customBrown py-4"><span class="whitespace-nowrap">Дата</span>
                    </td>
                    <td
                      class="border-b border-customBrown py-4 text-right cormorantInfant">{{ date('d.m.Y H:i', strtotime($order->created_at)) }}</td>
                  </tr>
                  @if(!isset($order->data['store_coupon'])||!$order->data['store_coupon'])
                    <tr>
                      <td class="text-left border-b border-customBrown py-4"><span
                          class="whitespace-nowrap">Статус</span></td>
                      <td
                        class="border-b border-customBrown py-4 text-right text-black">{{ $order->getStatusText() }}</td>
                    </tr>
                  @endif
                  </tbody>
                </table>
              </div>
              @if(!isset($order->data['store_coupon'])||!$order->data['store_coupon'])
                <div>
                  <h2 class="text-sm sm:text-base md:text-lg lg:text-xl xl:text-2xl uppercase mb-6">Данные
                    получателя</h2>
                  <table id="table-total"
                         class=" border-t border-customBrown text-sm sm:text-md md:text-lg lg:text-xl table-auto w-full border-collapse leading-none">
                    <tbody>
                    <tr>
                      <td class="text-left border-b border-customBrown py-4"><span class="whitespace-nowrap">ФИО</span>
                      </td>
                      <td
                        class="border-b border-customBrown py-4 text-right">{{ $order->data['form']['full_name'] }}</td>
                    </tr>
                    <tr>
                      <td class="text-left border-b border-customBrown py-4"><span
                          class="whitespace-nowrap">Телефон</span></td>
                      <td
                        class="border-b border-customBrown py-4 text-right cormorantInfant">{{ $order->data['form']['phone'] }}</td>
                    </tr>
                    <tr>
                      <td class="text-left border-b border-customBrown py-4"><span
                          class="whitespace-nowrap">Email</span></td>
                      <td class="border-b border-customBrown py-4 text-right">{{ $order->data['form']['email'] }}</td>
                    </tr>
                    </tbody>
                  </table>
                </div>
              @endif
              @if(!isset($order->data['store_coupon'])||!$order->data['store_coupon'])
                @if((!isset($order->data['is_voucher'])||!$order->data['is_voucher'])&&(!isset($order->data['is_meeting'])||!$order->data['is_meeting']))
                  <div>
                    <h2 class="text-sm sm:text-base md:text-lg lg:text-xl xl:text-2xl uppercase mb-6">Информация о
                      доставке</h2>
                    <table id="table-total"
                           class=" border-t border-customBrown text-sm sm:text-md md:text-lg lg:text-xl table-auto w-full border-collapse leading-none">
                      <tbody>
                      <tr>
                        <td class="text-left border-b border-customBrown py-4"><span class="whitespace-nowrap">Способ доставки</span>
                        </td>
                        <td
                          class="border-b border-customBrown py-4 text-right">{{ $order->data_shipping['shipping-method'] }}</td>
                      </tr>
                      <tr>
                        <td class="text-left border-b border-customBrown py-4"><span
                            class="whitespace-nowrap">{{ $order->data_shipping['info']['address_name'] ?? '' }}</span>
                        </td>
                        <td
                          class="border-b border-customBrown py-4 text-right">{!! $order->data_shipping['info']['address'] ?? '' !!}</td>
                      </tr>
                      @if(isset($order->data_shipping['info']['track']) && $order->data_shipping['info']['track'])
                        <tr>
                          <td class="text-left border-b border-customBrown py-4"><span class="whitespace-nowrap">Трек номер</span>
                          </td>
                          <td class="border-b border-customBrown py-4 text-right">
                            @if($order->data_shipping['info']['track'])
                              <a href="{{ $order->data_shipping['info']['tracking_link'] }}"
                                 target="_blank" class="underline hover:no-underline">{{ $order->data_shipping['info']['track'] }}</a>
                            @else
                              {{ $order->data_shipping['info']['track'] }}
                            @endif
                          </td>
                        </tr>
                      @endif
                      </tbody>
                    </table>
                  </div>
                @endif
              @endif
              @if(!isset($order->data['store_coupon'])||!$order->data['store_coupon'])
                <div id="mobile-total">
                  <div class="relative overflow-x-auto">
                    <table id="table-total"
                           class=" border-t border-customBrown text-sm sm:text-md md:text-lg lg:text-xl table-auto w-full border-collapse leading-none">
                      <tbody>
                      <tr>
                        <td class="text-left border-b border-customBrown py-4">Товаров на сумму</td>
                        <td class="border-b border-customBrown py-4 text-right"><span
                            id="cart-total-info">{!! formatPrice($order->data['total'], true) !!}</span></td>
                      </tr>
                      @if(isset($order->data['discount'])&&$order->data['discount'] > 0)
                        <tr>
                          <td class="border-b border-customBrown py-4">
                            @if(isset($order->data['promocode']['code']))
                              Промокод ({{ $order->data['promocode']['code'] }})
                            @elseif(isset($order->data['voucher']['code']))
                              Подарочный сертификат ({{ $order->data['voucher']['code'] }})
                            @else
                              Скидка
                            @endif
                          </td>
                          <td class="border-b border-customBrown py-4 text-right">
                            -{!! formatPrice($order->data_shipping['price']+$order->data['total']-$order->amount, true) !!}</td>
                        </tr>
                      @endif
                      @if(isset($order->data_shipping['price'])&&$order->data_shipping['price']>0)
                        <tr>
                          <td
                            class="border-b border-customBrown py-4">{{ $order->data_shipping['shipping-method'] }}</td>
                          <td
                            class="border-b border-customBrown py-4 text-right">{!! formatPrice($order->data_shipping['price'], true) !!}</td>
                        </tr>
                      @endif
                      <tr id="order-total-info">
                        <td class="text-left border-b border-customBrown py-4">Итого</td>
                        <td class="border-b border-customBrown py-4 text-right"><span
                            id="order-amount">{!! formatPrice($order->amount, true) !!}</span></td>
                      </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              @endif
            </div>

          </div>

          <div class="text-center mt-6 sm:mt-9 lg:mt-12 mb-12">
            <x-public.primary-button href="{{ route('cabinet.order.index') }}"
                                     class="h-11 md:h-[58px] px-5 mx-auto text-sm sm:text-md md:text-lg lg:text-xl">
              Назад
            </x-public.primary-button>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-cabinet-layout>
