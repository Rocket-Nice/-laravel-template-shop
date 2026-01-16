@section('title', $seo['title'] ?? config('app.name'))
<x-app-layout>
  <x-slot name="script">
    <link rel="stylesheet" href="{{ asset('libraries/choices.js/public/assets/styles/choices.min.css') }}">
    <script src="{{ asset('libraries/choices.js/public/assets/scripts/choices.min.js') }}"></script>
    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey=c7744407-82c1-4c00-a488-4ae90d1e64ef"
            type="text/javascript"></script>

    @if($alert)
      <script>
        document.addEventListener('DOMContentLoaded', function () {
          new Toast({
            message: '{!! $alert !!}',
            type: 'danger'
          });
        })
      </script>
    @endif
  </x-slot>
  <div class="md:flex p-0 md:flex-row-reverse">
    <div class="md:max-w-[40%] xl:max-w-[546px] w-full mx-auto flex flex-col">
      <div class="item-square orderImage square-0.91 w-full">
        @if(isset($content->image_data['mainImage']['size']))
          <input type="hidden" data-id="mainImage" class="json-image"
                 value="{{ e(json_encode($content->image_data['mainImage']['size'], JSON_UNESCAPED_UNICODE)) }}"
                 data-picture-class="w-full h-full block object-cover">
        @endif
      </div>
    </div>
    <div class="bg-myGreen flex-1 flex flex-col justify-between">
      <div class="w-full flex-1 flex items-center">
        <div class="w-full px-2 md:px-4 lg:pl-16 ">
          <div class="text-center md:text-left py-6 md:pb-0 md:pt-0 w-full">
            <h1 class="headline-1 md:mb-6">{{ $content->text_data['headline1'] ?? '' }}</h1>
            @if(isset($content->text_data['subtitle1']))
              <div class="m-text-body d-text-body">{!! nl2br($content->text_data['subtitle1']) !!}</div>
            @endif
            @if(getSettings('happyCoupon') && false)
              @if($cart->count())
                <div id="timer-container" style="opacity: 0;">
                  <div class="text-base md:text-xl mb-2">Содержимое корзины аннулируется через:</div>
                  <div id="timer" class="text-center flex justify-center md:justify-start space-x-2">
                    <div class="inline-block text-center mb-2" style="display: none;">
                      <div class="time text-4xl flex" id="days">
                        <div class="digit"></div>
                        <div class="digit"></div>
                      </div>
                      <div id="days-label" class="label cormorantGaramond">дней</div>
                    </div>
                    <div class="flex space-x-1" style="display: none;">
                      <div
                        class="inline-flex justify-center items-center text-center mb-2 bg-gradient-to-r from-[#C4C9A6] to-[#A0A584] rounded px-1 font-foglihten_no06 text-[28px] text-white"
                        style="width: 38px;height:50px;padding:2px">
                        <div
                          class="time text-[28px] w-full flex justify-center items-center border border-white rounded text-center digit"
                          id="hours-1"></div>
                      </div>
                      <div
                        class="inline-flex justify-center items-center text-center mb-2 bg-gradient-to-r from-[#C4C9A6] to-[#A0A584] rounded px-1 font-foglihten_no06 text-[28px] text-white"
                        style="width: 38px;height:50px;padding:2px">
                        <div
                          class="time text-[28px] w-full flex justify-center items-center border border-white rounded text-center digit"
                          id="hours-2"></div>
                      </div>
                    </div>
                    <div class="cormorantInfant border border-myDark rounded-2xl flex items-center text-[36px] px-2.5">
                      <div class="flex space-x-1">
                        <div class="time w-full flex justify-center items-center text-center digit"
                             id="minutes-1"></div>
                        <div class="time w-full flex justify-center items-center text-center digit"
                             id="minutes-2"></div>
                      </div>
                      <div class="font-foglihten_no06 inline-block mx-1">:</div>
                      <div class="flex space-x-1">
                        <div class="time w-full flex justify-center items-center text-center digit"
                             id="seconds-1"></div>
                        <div class="time w-full flex justify-center items-center text-center digit"
                             id="seconds-2"></div>
                      </div>
                    </div>
                  </div>
                </div>
                <script>
                  function redirect() {
                    localStorage.removeItem('deadTime');
                    window.location.href = '{{ route('cart.clear') }}';
                  }

                  function setDeadTime() {
                    const currentTime = new Date();
                    const deadTime = new Date(currentTime.getTime() + (20 * 60 * 1000) + 1000); // +20 min and 1 sec
                    // const deadTime = new Date(currentTime.getTime() + (10 * 1000) + 1000); // +20 min and 1 sec
                    localStorage.setItem('deadTime', deadTime.toString());
                  }

                  function getDeadTime() {
                    const deadTimeStr = localStorage.getItem('deadTime');
                    if (!deadTimeStr) {
                      return null; // Return null if deadTime doesn't exist
                    }

                    const deadTime = new Date(deadTimeStr);
                    const currentTime = new Date();

                    // If deadline has passed, redirect and return null
                    if (deadTime < currentTime) {
                      redirect();
                      return null;
                    }

                    return deadTime;
                  }

                  document.addEventListener('DOMContentLoaded', () => {
                    var countDownDate = getDeadTime();
                    if (countDownDate === null) {
                      setDeadTime();
                      countDownDate = getDeadTime();
                    }
                    if (document.getElementById("timer")) {
                      // var countDownDate = new Date("Nov 6, 2024 00:00:00").getTime();

                      // countDownDate.setMinutes(countDownDate.getMinutes() + 20);

                      function splitNumber(number) {
                        // Преобразуем число в строку и добавляем ведущий ноль если нужно
                        const paddedNumber = number.toString().padStart(2, '0');
                        // Возвращаем массив из двух цифр
                        return [paddedNumber[0], paddedNumber[1]];
                      }

                      var x = setInterval(function () {
                        document.getElementById('timer-container').style.opacity = '1';
                        var now = new Date().getTime();
                        var distance = countDownDate - now;

                        // Вычисляем значения
                        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                        // Разбиваем каждое значение на отдельные цифры
                        const [hours1, hours2] = splitNumber(hours);
                        const [minutes1, minutes2] = splitNumber(minutes);
                        const [seconds1, seconds2] = splitNumber(seconds);

                        // Обновляем значения в DOM
                        document.getElementById("hours-1").innerHTML = hours1;
                        document.getElementById("hours-2").innerHTML = hours2;
                        document.getElementById("minutes-1").innerHTML = minutes1;
                        document.getElementById("minutes-2").innerHTML = minutes2;
                        document.getElementById("seconds-1").innerHTML = seconds1;
                        document.getElementById("seconds-2").innerHTML = seconds2;

                        if (distance < 0) {
                          clearInterval(x);
                          redirect();
                          document.getElementById("timer").innerHTML = "Время истекло";
                        }
                      }, 1000);
                    }
                  })

                </script>
              @endif
            @endif
          </div>
        </div>
      </div>
      {{--      @if(getSettings('happyCoupon') && false)--}}
      {{--      <div class="w-full p-3 text-center text-base md:text-xl" style="background: #2C4B3A26;">--}}
      {{--        <span class="font-bold">Внимание!</span> До оформления заказа товар <br/>не резервируется и наличие может закончиться <br/>в любой момент--}}
      {{--      </div>--}}
      {{--      @endif--}}
      {{--      <div class="w-full px-1 py-3 text-center text-base md:text-xl" style="background: #2C4B3A26;">--}}
      {{--        <span class="font-bold">Внимание!</span><br/>Совершив покупку, вы становитесь участником акции!<br/>После оплаты заполните анкету, указав своё желание--}}
      {{--      </div>--}}
    </div>
  </div>

  <div id="order-form">
    @if(!$cart->count())
      <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-12 md:py-14 lg:py-16 xl:py-[86px]">
        <div class="text-center">
          <h3 class="d-headline-4 m-headline-3 text-center">Корзина пуста</h3>
        </div>
      </div>
    @endif
    <form action="{{ route('order.submit') }}" id="order" method="post"
          @if(!$cart->count()) style="display: none;"@endif>
      @csrf
      <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-12 md:py-14 lg:py-16 xl:py-[86px]">
        <div class="flex flex-col-reverse justify-between lg:flex-row max-w-[480px] mx-auto lg:max-w-none">
          <div class="w-full lg:max-w-[480px] space-y-12" id="order-form">
            <h3 class="d-headline-4 m-headline-3 text-center">Контактные данные</h3>
            <div class="space-y-12">
              <div>
                <x-public.text-input type="text" id="last_name" name="last_name" placeholder="Ваша фамилия"
                                     value="{{ old('last_name') }}" required/>
              </div>
              <div>
                <x-public.text-input type="text" id="first_name" name="first_name" placeholder="Ваше имя"
                                     value="{{ old('first_name') }}" required/>
              </div>
              <div>
                <x-public.text-input type="text" id="middle_name" name="middle_name" placeholder="Ваше отчество"
                                     value="{{ old('middle_name') }}"/>
              </div>
              <div>
                <x-public.text-input type="text" id="phone" name="phone" placeholder="Ваш телефон"
                                     value="{{ old('phone') }}" required/>
              </div>
              <div>
                <x-public.text-input type="text" id="email" name="email" placeholder="E-mail адрес"
                                     value="{{ old('email') }}" required/>
              </div>
              <div>
                <x-public.text-input type="text" id="email_confirmation" name="email_confirmation"
                                     placeholder="Повторите e-mail адрес" value="{{ old('email_confirmation') }}"
                                     required/>
              </div>
            </div>
            <h3 class="d-headline-4 m-headline-3 text-center">Доставка</h3>
            <div class="space-y-12">
              @if($delivery_status)
                <div>
                  <select name="country" id="country"
                          class="block w-full border-0 border-b border-b-myGray bg-transparent placeholder-myGray m-text-body d-text-body py-1.5 px-3 focus:ring-0 focus:border-b-black"
                          required>
                    <option value="-">Выберите страну</option>
                    @foreach($countries as $c)
                      <option value="{{ $c->id }}"
                              @isset($c->options['status']) data-shipping="{{ implode(',',$c->options['status']) }}@if($c->id==1),{{ implode(',',$pickups->pluck('code')->toArray()) }}@endif"
                              @endisset data-pochta="{{ $c->options['pochta_id'] }}" @if(old('country_code')==$c->options['pochta_id'])
                        {!! 'selected' !!}
                        @endif>{{ $c->name }}</option>
                    @endforeach
                  </select>
                </div>
              @else
                <input type="hidden" name="country" value="1">
              @endif
              <div>
                <select name="shipping-code" id="shipping-code"
                        class="block w-full border-0 border-b border-b-myGray bg-transparent placeholder-myGray m-text-body d-text-body py-1.5 px-3 focus:ring-0 focus:border-b-black">
                  <option value="-">Способ доставки</option>
                  @if($delivery_status)
                    @foreach($shipping_methods as $shipping_method)
                      {{--                  @if($shipping_method->code == 'x5post' && (auth()->check() || auth()->id()!=1))--}}
                      {{--                    @continue--}}
                      {{--                  @endif--}}
                      <option value="{{ $shipping_method->code }}"
                              @if(old('shipping-code') == $shipping_method->code) selected @endif>{{ $shipping_method->name }}</option>
                    @endforeach
                  @endif
                  {{--              @if($pickups->count() > 1)--}}
                  {{--                <optgroup label="Самовывоз">--}}
                  {{--                  @foreach($pickups as $pickup)--}}
                  {{--                    <option value="{{ $pickup->code }}">{{ $pickup->name }}</option>--}}
                  {{--                  @endforeach--}}
                  {{--                </optgroup>--}}
                  {{--              @elseif($pickups->count() == 1)--}}
                  {{--                @php($pickup = $pickups->first())--}}
                  {{--                <option value="{{ $pickup->code }}">{{ $pickup->name }}</option>--}}
                  {{--              @endif--}}
                </select>
              </div>
              <div id="pickupModule"></div>
              <div id="shippingModule"></div>
              <div class="space-y-12 pochta-address @if(old('shipping-method') != 'pochta') hidden @endif"
                   id="pochta-address">
                <div>
                  <x-public.text-input type="text" name="postcode" id="pochta-postcode" placeholder="Почтовый индекс"
                                       value="{{ old('postcode') }}" data-required/>
                </div>
                <div>
                  <x-public.text-input type="text" name="region" id="region" placeholder="Регион или область"
                                       value="{{ old('region') }}" data-required/>
                </div>
                <div>
                  <x-public.text-input type="text" name="city" id="city" placeholder="Название населенного пункта"
                                       value="{{ old('city') }}" data-required/>
                </div>
                <div>
                  <x-public.text-input type="text" name="street" id="street" placeholder="Название улицы"
                                       value="{{ old('street') }}" data-required/>
                </div>
                <div class="flex -mx-2">
                  <div class="w-1/2 px-2">
                    <x-public.text-input type="text" name="house" id="house" placeholder="Номер дома"
                                         value="{{ old('house') }}" data-required/>
                  </div>
                  <div class="w-1/2 px-2">
                    <x-public.text-input type="text" name="flat" id="flat" placeholder="Номер квартиры"
                                         value="{{ old('flat') }}"/>
                  </div>
                </div>
              </div>
              <div class="inn-address" style="display: none;" id="inn-address">
                <x-public.text-input type="text" id="inn" name="inn" placeholder="ИНН"
                                     value="{{ old('inn') }}"/>
              </div>
            </div>

            <h3 class="d-headline-4 m-headline-3 text-center">Скидка</h3>
            <div class="space-y-12">
              <div id="box-field-promocode" @if(getSettings('promo_1+1=3')) style="display:none;" @endif>
                <x-public.text-input type="text" id="promocode" name="promocode" placeholder="Промокод" value=""/>
              </div>
              <div id="box-field-voucher">
                <x-public.text-input type="text" id="voucher" name="voucher" placeholder="Подарочный сертификат"
                                     value=""/>
              </div>
              @if(getSettings('diamondPromo1'))
                <div class="hidden">
                  <x-public.checkbox type="checkbox" name="bonuses" id="bonuses" value="1"/>
                </div>
              @else
                @if(!getSettings('promo_1+1=3') && !getSettings('promo20') && !getSettings('promo30') && auth()->check() && ($user->getBonuses() || (getSettings('diamondPromo2') && $user->getSuperBonuses())))
                  <div id="box-field-bonuses" @if(getSettings('goldTicket')) style="display: none;" @endif>

                    <div class="text-black block d-text-body m-text-body">
                      @if(getSettings('diamondPromo2') && $user->getSuperBonuses())
                        <span class="whitespace-nowrap">Доступно {!! denum($user->getBonuses()+$user->getSuperBonuses(), ['<span class="cormorantInfant">%d</span> бонус', '<span class="cormorantInfant">%d</span> бонуса', '<span class="cormorantInfant">%d</span> бонусов']) !!}.</span>
                      @else
                        <span class="whitespace-nowrap">Доступно {!! denum($user->getBonuses(), ['<span class="cormorantInfant">%d</span> бонус', '<span class="cormorantInfant">%d</span> бонуса', '<span class="cormorantInfant">%d</span> бонусов']) !!}.</span>
                      @endif
                      Максимальный размер списания: <span class="cormorantInfant">50%</span> от суммы заказа<br/>
                    </div>
                    <div class="flex items-center justify-start space-x-4">
                      <x-public.checkbox type="checkbox" name="bonuses" id="bonuses" value="1"/>
                      <label for="bonuses" class="text-black block d-text-body m-text-body">Использовать</label>
                    </div>
                  </div>
                @elseif(!auth()->check())
                  <div class="hidden">
                    <x-public.checkbox type="checkbox" name="bonuses" id="bonuses" value="1"/>
                  </div>
                  <div class="text-black block d-text-body m-text-body"
                       @if(getSettings('goldTicket')) style="display: none;" @endif>
                    <a href="javascript:;" data-src="#authForm" data-fancybox-no-close-btn
                       class="shrink-0 underline hover:no-underline">Авторизуйтесь</a>, чтобы использовать бонусы
                  </div>

                @endif
              @endif

            </div>
            <div id="mobile-total" class="lg:hidden">

            </div>
          </div>
          <div class="w-full flex-1 lg:ml-12 xl:ml-[100px]">
            <div id="table-cart" class="space-y-9 lg:space-y-6 mb-14 lg:mb-12">
              @forelse($cart->content() as $item)
                @php($is_gift = false)
                @php($product = \App\Models\Product::query()->select('options->only_pickup as only_pickup')->where('id', $item->options->product_id)->first())
                @if($item->price <= 1)
                  @php($is_gift = true)
                @endif
                <div class="cart-item border-b border-black pb-6 mb-6" data-price="{{ $item->price }}"
                     data-row-id="{{ $item->rowId }}"
                     @if(!$is_gift) data-product="{{ $item->options->product_id ?? '' }}"
                     @endif data-shipping="{{ $item->options->shipping }}" data-quantity="{{ $item->qty }}">
                  <div class="flex">
                    <div class="w-[86px] mr-4 md:mr-6">
                      <div class="item-square">
                        <img src="{{ $item->options->image ?? '' }}" alt="{{ $item->name }}"
                             class="object-bottom object-cover block">
                      </div>
                    </div>
                    <div class="flex-1 flex flex-col lg:flex-row justify-between lg:space-x-6">
                      <div class="cart-item-name-{{ $item->rowId }} flex justify-between flex-1 max-w-full">
                        <div>
                          <h3 class="text-2xl lg:text-32 font-light">{{ $item->name }}</h3>
                          @if($item->options->subtitle&&$item->options->subtitle!='null')
                            <div class="text-myBrown d-text-body mt-2">{!! $item->options->subtitle !!}</div>
                          @endif
                          <div class="text-base lg:text-lg my-4">Артикул: {{ $item->options->sku }}</div>
                          @if($item->options->old_price)
                            <div data-da=".mobile-cart-info-{{ $item->rowId }},first,1023" class="text-base sm:text-md md:text-lg cormorantInfant italic font-semibold text-myGray line-through">{{ formatPrice($item->options->old_price * $item->qty) }}</div>
                          @endif
                          <div class="subtitle-1 text-myBrown cormorantInfant"
                               data-da=".mobile-cart-info-{{ $item->rowId }},last,1023">{{ formatPrice($item->price * $item->qty) }}</div>
                          @if($product->only_pickup)
                            <p class="text-myRed text-sm md:text-md lg:text-lg mt-1 !leading-none">Доступен только для
                              самовывоза г. Волгоград</p>
                          @endif
                        </div>
                      </div>
                      <div class="flex items-center space-x-6">
                        <div data-da=".mobile-cart-info-{{ $item->rowId }},first,1023"
                             class="flex justify-between items-center border border-black border-1 w-auto h-11 md:h-14">
                          <button class="product-sub bg-transparent border-0 outline-none p-3" data-field="productQty"
                                  @if($is_gift) style="pointer-events: none; opacity: .3" @endif>
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                              <path d="M3.5 7H10.5" stroke="#2C2E35" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round"/>
                            </svg>
                          </button>
                          <div class="flex-1 text-center mx-2 whitespace-nowrap text-xl md:text-2xl"><span
                              class="cormorantInfant">{{ $item->qty }}</span> шт.
                          </div>
                          <button class="product-add bg-transparent border-0 outline-none p-3" data-field="productQty"
                                  @if($is_gift) style="pointer-events: none; opacity: .3" @endif>
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                              <path d="M3.5 7H10.5" stroke="#2C2E35" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round"/>
                              <path d="M7 10.5V3.5" stroke="#2C2E35" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round"/>
                            </svg>
                          </button>
                        </div>
                        <div data-da=".cart-item-name-{{ $item->rowId }},last,1023">
                          <button type="button" tabindex="-1"
                                  class="product-remove text-center text-xl leading-none min-w-4 h-4 sm:min-w-5 md:min-w-6 max-w-4 h-4 sm:max-w-5 md:max-w-6 sm:h-5 md:h-6 flex justify-center items-center leading-none ml-5 lg:ml-6">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6 mx-auto" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                              <path d="M2 2L22 22M2 22L22 2" stroke="#2C2E35" stroke-linecap="round"
                                    stroke-linejoin="round"/>
                            </svg>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div
                    class="mt-4 lt:hidden flex justify-between items-center mobile-cart-info-{{ $item->rowId }}"></div>
                </div>
              @empty
                <div class="text-center text-2xl text-customBrown p-6">Корзина пуста</div>
              @endforelse
              @if(getSettings('promo20')&&$cart->content()&&$cart->subtotal(0, '.', '') >= 3599)
                <div class="border-b border-black pb-6 mb-6">
                  <div class="flex">
                    <div class="w-[86px] mr-4 md:mr-6">
                      <div class="item-square bg-springGreen">
                        <div class="flex justify-center-items-center p-4">
                          <img src="{{ asset('img/happy_coupon/cabinet-gift.png?1') }}" class="object-bottom object-cover block">
                        </div>
                      </div>
                    </div>
                    <div class="flex-1 flex flex-col lg:flex-row justify-between lg:space-x-6">
                      <div class="flex justify-between flex-1 max-w-full">
                        <div>
                          <h3 class="text-2xl lg:text-32 font-light">Подарок из ассортимента Le&nbsp;Mousse</h3>
                        </div>
                      </div>
                      <div class="flex items-center space-x-6">

                      </div>
                    </div>
                  </div>
                </div>
              @endif
              @if(getSettings('happyCoupon'))
                {{--          <div class="text-center">*для участия в акции сумма ваших товаров должна быть не менее 3 500₽</div>--}}
              @endif
            </div>
            <div class="mt-12 mb-12 d-text-body m-text-body" id="coupons-info" @if(!($need_to_keys ?? false)) style="display: none;" @endif>
              <div class="p-6 space-y-4 bg-newGreenPromo30" id="coupons-content">
                {!! $need_to_keys ?? '' !!}
              </div>
              <div class="px-6" style="font-size: .75em;">*не распространяется на доставку курьером СДЭК</div>
            </div>
            <div data-da="#mobile-total,first,1023">
              <div class="rounded-none lg:px-4 mb-6">
                <div class="relative overflow-x-auto">
                  <table id="table-total"
                         class="text-customBrown border-t border-black d-text-body m-text-body table-auto w-full border-collapse leading-none">
                    <tbody>
                    <tr id="shipping-price-field" style="display: none;">
                      <td class="text-left border-b border-black py-4" id="shipping-price-text">Доставка</td>
                      <td class="border-b border-black py-4 text-right"><span id="shipping-price-info"
                                                                              class="subtitle-1 text-myBrown">0</span>
                      </td>
                    </tr>
                    <tr>
                      <td class="text-left border-b border-black py-4">Товаров на сумму</td>
                      <td class="border-b border-black py-4 text-right"><span id="cart-total-info"
                                                                              class="subtitle-1 text-myBrown">{!! formatPrice($cart->subtotal(0, '.', ''), true)  !!}</span>
                      </td>
                    </tr>
                    <tr id="order-total-info">
                      <td class="text-left border-b border-black py-4">Итого к оплате</td>
                      <td class="border-b border-black py-4 text-right"><span id="order-amount"
                                                                              class="subtitle-1 text-myBrown">{!! formatPrice($cart->subtotal(0, '.', ''), true)  !!}</span>
                      </td>
                    </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <div class="space-y-4 text-black lg:px-4"
                   x-data="{oferta: true, politika: true, mailing: true, promo113: true, giftPolitika: true, giftDelivery: true}">
                <div class="flex items-center justify-start space-x-4">
                  <x-public.checkbox type="checkbox" name="oferta" x-model="oferta" id="oferta" value="1" required
                                     checked @change.prevent="oferta=true"/>
                  <label for="oferta" class="text-black block d-text-body m-text-body">Ознакомлен с условием <a
                      href="{{ route('page', ['page' => 'oferta']) }}" target="_blank"
                      class="underline hover:no-underline">оферты</a></label>
                </div>
                <div class="flex items-center justify-start space-x-4">
                  <x-public.checkbox type="checkbox" name="politika" x-model="politika" id="politika" value="1" required
                                     checked
                                     @change.prevent="if(!politika) window.alert('Согласно п.4 ст.16 Закон РФ от 07.02.1992 N 2300-1 «О защите прав потребителей». В случае отказа потребителя предоставить персональные данные, Продавец вправе отказать потребителю в заключении договора, так как обязанность предоставления таких данных непосредственно связана с исполнением договора с потребителем');politika=true"/>
                  <label for="politika" class="text-black block d-text-body m-text-body">Согласие на обработку <a
                      href="{{ route('page', ['page' => 'soglasie_na_obrabotku_personalnih_dannih']) }}" target="_blank"
                      class="underline hover:no-underline">персональных данных</a></label>
                </div>
                <div class="flex items-center justify-start space-x-4">
                  <x-public.checkbox type="checkbox" name="mailing" x-model="mailing" id="mailing" value="1" checked
                                     @change.prevent="if(!mailing) window.alert('Не соглашаясь на получение рассылок мы не сможем вам направить состав вашего заказа и информацию об отслеживание доставки')"/>
                  <label for="mailing" class="text-black block d-text-body m-text-body">Согласие на <a
                      href="{{ route('page', ['page' => 'soglasie_na_poluchenie_reklamnih_rassilok']) }}"
                      target="_blank" class="underline hover:no-underline">получение рассылок</a></label>
                </div>
                <div class="flex items-center justify-start space-x-4">
                  <x-public.checkbox type="checkbox" name="giftDelivery" x-model="giftDelivery" id="giftDelivery"
                                     value="1" checked @change.prevent="giftDelivery=true;"/>
                  <label for="giftDelivery" class="text-black block d-text-body m-text-body">Согласен со сроками
                    отправки продукции в течение <span class="cormorantInfant">10</span> рабочих дней</label>
                </div>
                {{--            <div class="flex items-center justify-start space-x-4">if(!giftDelivery) window.alert('Не соглашаясь на получение рассылок мы не сможем вам направить состав вашего заказа и информацию об отслеживание доставки')--}}
                {{--              <x-public.checkbox type="checkbox" name="goldpromo" id="goldpromo" value="1" required/>--}}
                {{--              <label for="goldpromo" class="text-black block d-text-body m-text-body">Я принимаю условия акции <a href="{{ route('page', ['page' => 'aktsiya_zolotoy_bilet']) }}" target="_blank" class="underline hover:no-underline">"Золотой билет"</a></label>--}}
                {{--            </div>--}}
                @if(getSettings('puzzlesStatus'))
                  <div class="flex items-center justify-start space-x-4">
                    <x-public.checkbox type="checkbox" name="promoPzl" id="promoPzl" value="1" required/>
                    <label for="promoPzl" class="text-black block d-text-body m-text-body">Ознакомлен, принимаю <a
                        href="{{ route('page', ['page' => 'pravila_provedeniya_reklamnoy_aktsii_soberi_kartinu']) }}"
                        target="_blank" class="underline hover:no-underline">правила проведения рекламной акции «собери
                        картину»</a></label>
                  </div>
                @endif
                @if(getSettings('diamondPromo1'))
                  <div class="flex items-center justify-start space-x-4">
                    <x-public.checkbox type="checkbox" name="promoPzl" id="promoPzl" value="1" required/>
                    <label for="promoPzl" class="text-black block d-text-body m-text-body">Ознакомлен, принимаю <a
                        href="{{ route('page', ['page' => 'usloviya_aktsii_bonusnoe_priklyuchenie_ot_brilliantovih_sutok_zolotim_chasam']) }}"
                        target="_blank" class="underline hover:no-underline">условия акции «Бонусное приключение – от
                        Бриллиантовых суток к Золотым часам»</a></label>
                  </div>
                @endif
                {{--            @if(getSettings('promo_1+1=3') || getSettings('happyCoupon'))--}}

                {{--              @endif--}}
                @if(getSettings('promo_1+1=3'))
                  <div class="flex items-center justify-start space-x-4">
                    <x-public.checkbox type="checkbox" name="promo113" x-model="promo113" id="promo113" value="1"
                                       required checked @change.prevent="promo113=true"/>
                    <label for="promo113" class="text-black block d-text-body m-text-body">Ознакомлен, принимаю <a
                        href="{{ route('page', ['page' => 'politika_promo_1+1=3']) }}" target="_blank"
                        class="underline hover:no-underline">правила проведения рекламной акции «1+1=3»</a></label>
                  </div>
                @endif
                @if(getSettings('happyCoupon'))
                  <div class="flex items-center justify-start space-x-4">
                    <x-public.checkbox type="checkbox" name="giftPolitika" x-model="giftPolitika" id="giftPolitika"
                                       value="1" required checked
                                       @change.prevent="if(!giftPolitika) window.alert('Не принимая Правила акции вы отказываетесь принимать участие в ней');giftPolitika=true"/>
                    <label for="giftPolitika" class="text-black block d-text-body m-text-body">Я соглашаюсь с условиями
                      акции <a
                        href="{{ route('page', 'politika_promo') }}" target="_blank"
                        class="underline hover:no-underline">«Счастливый купон»</a></label>
                  </div>
                @endif
                @if(getSettings('promo20'))
                  <div class="flex items-center justify-start space-x-4">
                    <x-public.checkbox type="checkbox" name="giftPolitika" x-model="giftPolitika" id="giftPolitika"
                                       value="1" required checked
                                       @change.prevent="if(!giftPolitika) window.alert('Не принимая Правила акции вы отказываетесь принимать участие в ней');giftPolitika=true"/>
                    <label for="giftPolitika" class="text-black block d-text-body m-text-body">Я соглашаюсь с условиями
                      акции <a
                        href="{{ route('page', 'promo20') }}" target="_blank"
                        class="underline hover:no-underline">«-20%»</a></label>
                  </div>
                @endif
{{--                @if(getSettings('promo30'))--}}
{{--                  <div class="flex items-center justify-start space-x-4">--}}
{{--                    <x-public.checkbox type="checkbox" name="giftPolitika" x-model="giftPolitika" id="giftPolitika"--}}
{{--                                       value="1" required checked--}}
{{--                                       @change.prevent="if(!giftPolitika) window.alert('Не принимая Правила акции вы отказываетесь принимать участие в ней');giftPolitika=true"/>--}}
{{--                    <label for="giftPolitika" class="text-black block d-text-body m-text-body">Я соглашаюсь с условиями--}}
{{--                      акции <a--}}
{{--                        href="{{ route('page', 'pravila_provedeniya_reklamnoy_aktsii_30') }}" target="_blank"--}}
{{--                        class="underline hover:no-underline">«-30%»</a></label>--}}
{{--                  </div>--}}
{{--                @endif--}}
                @if(getSettings('goldTicket'))
                  <div class="flex items-center justify-start space-x-4">
                    <x-public.checkbox type="checkbox" name="giftPolitika" x-model="giftPolitika" id="giftPolitika"
                                       value="1" required checked
                                       @change.prevent="if(!giftPolitika) window.alert('Не принимая Правила акции вы отказываетесь принимать участие в ней');giftPolitika=true"/>
                    <label for="giftPolitika" class="text-black block d-text-body m-text-body">Я соглашаюсь с условиями
                      акции <a
                        href="{{ route('page', 'aktsiya_zolotoy_bilet') }}" target="_blank"
                        class="underline hover:no-underline">«Золотой билет»</a></label>
                  </div>
                @endif
                <div class="text-center">
                  @if($cart->content()->firstWhere('options.preorder', true))
                    <x-public.primary-button href="javascript:;" data-fancybox data-src="#preorder"
                                             class="w-full !px-2 text-center">Оформить заказ
                    </x-public.primary-button>
                    <div id="preorder" class="d-text-body m-text-body p-2 sm:p-4 max-w-3xl" style="display: none;">
                      <div class="mb-4">
                        <h3 class="d-headline-4 m-headline-3">Внимание</h3>
                        <p>Ожидаемая отправка с 5 по 17 декабря<br/>После отгрузки товара, статус вашего заказа
                          изменится в личном кабинете</p>
                      </div>
                      <x-public.primary-button type="submit"
                                               class="md:h-14 md:w-full md:max-w-[285px] mx-auto mt-[60px]"
                                               form="order">Подтверждаю заказ
                      </x-public.primary-button>
                    </div>
                  @else
                    <x-public.primary-button type="submit" class="md:h-14 md:w-full md:max-w-[285px] mx-auto mt-[60px]">
                      Оформить заказ
                    </x-public.primary-button>
                  @endif
                  {{--                @if(auth()->check()&&!auth()->user()->tgChats()->where('active', true)->exists())--}}
                  {{--                  <div class="mt-12 mb-12 bg-myBeige p-6 m-text-body d-text-body text-center space-y-4">--}}
                  {{--                    <h1 class="text-xl uppercase font-medium leading-1.6">Подпишитесь на наш бот в Telegram и получайте уведомления</h1>--}}
                  {{--                    <p> • Изменения статусов заказа<br/>--}}
                  {{--                      • Эксклюзивные скидки и акции<br/>--}}
                  {{--                      • Важные напоминания--}}
                  {{--                    </p>--}}
                  {{--                    <x-public.primary-button href="https://t.me/lemousse_notifications_bot?start={{ auth()->user()->uuid }}" target="_blank" class="md:w-full max-w-[357px]">Подписаться</x-public.primary-button>--}}
                  {{--                  </div>--}}
                  {{--                @endif--}}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      @if(auth()->check())
        @if(getSettings('diamondPromo2'))
          <input type="hidden" name="user-bonuses" id="user-bonuses"
                 value="{{ $user->getBonuses() + $user->getSuperBonuses() }}">
        @else
          <input type="hidden" name="user-bonuses" id="user-bonuses" value="{{ $user->getBonuses() }}">
        @endif

      @endif
      <input type="hidden" name="cart-total" id="cart-total" value="{{ $cart->subtotal(0, '.', '') }}">
      <input type="hidden" name="total_for_discount" id="total_for_discount" value="{{ $total_for_discount }}">
      <input type="hidden" name="cart-count" id="cart-count" value="{{ $cart->count() }}">
      <input type="hidden" name="shipping-price" id="shipping-price" value="{{ old('shipping-price') ?? 0 }}">
      <input type="hidden" name="shipping" id="shipping-method" value="{{ old('shipping-code') }}">
      <input type="hidden" name="cdek-pvz-id" id="cdek-pvz-id" value="{{ old('cdek-pvz-id') }}">
      <input type="hidden" name="cdek-pvz-address" id="cdek-pvz-address" value="{{ old('cdek-pvz-address') }}">
      <input type="hidden" name="nt-pvz-id" id="nt-pvz-id" value="{{ old('nt-pvz-id') }}">
      <input type="hidden" name="nt-pvz-address" id="nt-pvz-address" value="{{ old('nt-pvz-address') }}">
      <input type="hidden" name="yandex-pvz-id" id="yandex-pvz-id" value="{{ old('yandex-pvz-id') }}">
      <input type="hidden" name="yandex-pvz-address" id="yandex-pvz-address"
             value="{{ old('yandex-pvz-address') }}">
      <input type="hidden" name="x5post-pvz-id" id="x5post-pvz-id" value="{{ old('x5post-pvz-id') }}">
      <input type="hidden" name="x5post-pvz-address" id="x5post-pvz-address" value="{{ old('x5post-pvz-address') }}">
      <input type="hidden" name="cdek_courier-form-region" id="cdek_courier-form-region"
             value="{{ old('cdek_courier-form-region') }}">
      <input type="hidden" name="cdek_courier-form-city" id="cdek_courier-form-city"
             value="{{ old('cdek_courier-form-city') }}">
      <input type="hidden" name="cdek_courier-form-street" id="cdek_courier-form-street"
             value="{{ old('cdek_courier-form-street') }}">
      <input type="hidden" name="cdek_courier-form-house" id="cdek_courier-form-house"
             value="{{ old('cdek_courier-form-house') }}">
      <input type="hidden" name="cdek_courier-form-flat" id="cdek_courier-form-flat"
             value="{{ old('cdek_courier-form-flat') }}">
      <input type="hidden" name="cdek_courier-form-address" id="cdek_courier-form-address"
             value="{{ old('cdek_courier-form-address') }}">
    </form>
  </div>
  @if($cart->count())
    <script>
      window.pickupsData = @json($pickups->toArray());

      document.addEventListener('DOMContentLoaded', function () {
        if (typeof ym !== 'undefined') {
          ym(98576494, 'reachGoal', 'order_open');
        }
      })
    </script>
    <input type="hidden" class="js_data shipping_route" id="route_cdek_courier_regions"
           value="{{ route('getCdekCourierRegions') }}">
    <input type="hidden" class="js_data shipping_route" id="route_cdek_courier_cities"
           value="{{ route('getCdekCourierCities') }}">
    <input type="hidden" class="js_data shipping_route" id="route_cdek_regions" value="{{ route('getCdekRegions') }}">
    <input type="hidden" class="js_data shipping_route" id="route_cdek_cities" value="{{ route('getCdekCities') }}">
    <input type="hidden" class="js_data shipping_route" id="route_cdek_pvz" value="{{ route('getCdekPvz') }}">
    <input type="hidden" class="js_data shipping_route" id="route_nt_regions" value="{{ route('getCdekRegions') }}">
    <input type="hidden" class="js_data shipping_route" id="route_nt_cities" value="{{ route('getCdekCities') }}">
    <input type="hidden" class="js_data shipping_route" id="route_nt_pvz" value="{{ route('getCdekPvz') }}">
    <input type="hidden" class="js_data shipping_route" id="route_yandex_regions" value="{{ route('getBoxberryRegions') }}">
    <input type="hidden" class="js_data shipping_route" id="route_yandex_cities" value="{{ route('getBoxberryCities') }}">
    <input type="hidden" class="js_data shipping_route" id="route_yandex_pvz" value="{{ route('getBoxberryPvz') }}">
    <input type="hidden" class="js_data shipping_route" id="route_calculate_cdek" value="{{ route('calculateCdek') }}">
    <input type="hidden" class="js_data shipping_route" id="route_calculate_nt" value="{{ route('calculateCdek') }}">
    <input type="hidden" class="js_data shipping_route" id="route_calculate_cdek_courier"
           value="{{ route('calculateCdek') }}">
    <input type="hidden" class="js_data shipping_route" id="route_calculate_yandex"
           value="{{ route('calculateBoxberry') }}">
    <input type="hidden" class="js_data shipping_route" id="route_calculate_pochta"
           value="{{ route('calculatePochta') }}">
    <input type="hidden" class="js_data shipping_route" id="route_x5post_regions"
           value="{{ route('getX5PostRegions') }}">
    <input type="hidden" class="js_data shipping_route" id="route_x5post_cities" value="{{ route('getX5PostCities') }}">
    <input type="hidden" class="js_data shipping_route" id="route_x5post_pvz" value="{{ route('getX5PostPvz') }}">
    <input type="hidden" class="js_data shipping_route" id="route_calculate_x5post"
           value="{{ route('calculateX5Post') }}">
    <input type="hidden" class="js_data discount_route" id="route_check_promocode"
           value="{{ route('checkPromocode') }}">
    <input type="hidden" class="js_data discount_route" id="route_check_voucher" value="{{ route('checkVoucher') }}">
    <div id="mapContainer" style="display: none"></div>
  @endif

</x-app-layout>
