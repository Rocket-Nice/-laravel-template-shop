@section('title', $seo['title'] ?? config('app.name'))
<x-app-layout>
  @include('_parts.public.pageTopBlock')

  <div id="order-form" >
    <form action="{{ route('order.voucher.submit', $voucher->slug) }}" id="order" method="post">
      @csrf
      <input type="hidden" name="products[]" value="{{ $voucher->id }}">
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-12 md:py-14 lg:py-16 xl:py-[86px]">
    <div class="flex flex-col-reverse justify-between lg:flex-row max-w-[480px] mx-auto lg:max-w-none">
      <div class="w-full lg:max-w-[480px] space-y-12" id="order-form">
        <h3 class="d-headline-4 m-headline-3 text-center">Контактные данные</h3>
        <div class="space-y-12">
          <div>
            <x-public.text-input type="text" id="last_name" name="last_name" placeholder="Ваша фамилия" value="{{ old('last_name') }}" required/>
          </div>
          <div>
            <x-public.text-input type="text" id="first_name" name="first_name" placeholder="Ваше имя" value="{{ old('first_name') }}" required/>
          </div>
          <div>
            <x-public.text-input type="text" id="middle_name" name="middle_name" placeholder="Ваше отчество" value="{{ old('middle_name') }}" />
          </div>
          <div>
            <x-public.text-input type="text" id="phone" name="phone" placeholder="Ваш телефон" value="{{ old('phone') }}" required/>
          </div>
          <div>
            <x-public.text-input type="text" id="email" name="email" placeholder="E-mail адрес" value="{{ old('email') }}" required/>
          </div>
          <div>
            <x-public.text-input type="text" id="email_confirmation" name="email_confirmation" placeholder="Повторите e-mail адрес" value="{{ old('email_confirmation') }}" required/>
          </div>
        </div>


        <div id="mobile-total" class="lg:hidden">

        </div>
      </div>
      <div class="w-full flex-1 lg:ml-12 xl:ml-[100px]">
        <div id="table-cart" class="space-y-9 lg:space-y-6 mb-14 lg:mb-12">
            <div class="cart-item border-b border-black pb-6 mb-6">
              <div class="flex">
                <div class="w-[200px] mr-4 md:mr-6">
                  <div class="item-square product_card_voucher">
                    <img src="{{ $voucher->image ?? '' }}" alt="{{ $voucher->name }}" class="object-bottom object-cover block">
                  </div>
                </div>
                <div class="flex-1 flex flex-col lg:flex-row justify-between lg:space-x-6">
                  <div class="flex justify-between flex-1 max-w-full">
                    <div>
                      <h3 class="text-2xl lg:text-32 font-light">{{ $voucher->name }}</h3>
                      <div class="text-base lg:text-lg my-4">Артикул: {{ $voucher->sku }}</div>
                      <div class="subtitle-1 text-myBrown cormorantInfant" data-da=".mobile-cart-info-{{ $voucher->id }},last,1023">{{ formatPrice($voucher->price) }}</div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="mt-4 lt:hidden flex justify-between items-center mobile-cart-info-{{ $voucher->id }}"></div>
            </div>
        </div>
        <div data-da="#mobile-total,first,1023">
          <div class="rounded-none lg:px-4 mb-6">
            <div class="relative overflow-x-auto">
              <table id="table-total" class="text-customBrown border-t border-black d-text-body m-text-body table-auto w-full border-collapse leading-none">
                <tbody>
                <tr id="order-total-info">
                  <td class="text-left border-b border-black py-4">Итого к оплате</td>
                  <td class="border-b border-black py-4 text-right"><span id="order-amount" class="subtitle-1 text-myBrown">{!! formatPrice($voucher->price)  !!}</span></td>
                </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="space-y-4 text-black lg:px-4" x-data="{oferta: true, politika: true, mailing: true}">
            <div class="flex items-center justify-start space-x-4">
              <x-public.checkbox type="checkbox" name="oferta" x-model="oferta" id="oferta" value="1" required checked @change.prevent="oferta=true"/>
              <label for="oferta" class="text-black block d-text-body m-text-body">Ознакомлен с условием <a href="{{ route('page', ['page' => 'oferta']) }}" target="_blank" class="underline hover:no-underline">оферты</a></label>
            </div>
            <div class="flex items-center justify-start space-x-4">
              <x-public.checkbox type="checkbox" name="politika" x-model="politika" id="politika" value="1" required checked @change.prevent="if(!politika) window.alert('Согласно п.4 ст.16 Закон РФ от 07.02.1992 N 2300-1 «О защите прав потребителей». В случае отказа потребителя предоставить персональные данные, Продавец вправе отказать потребителю в заключении договора, так как обязанность предоставления таких данных непосредственно связана с исполнением договора с потребителем');politika=true"/>
              <label for="politika" class="text-black block d-text-body m-text-body">Согласие на обработку <a href="{{ route('page', ['page' => 'soglasie_na_obrabotku_personalnih_dannih']) }}" target="_blank" class="underline hover:no-underline">персональных данных</a></label>
            </div>
            <div class="flex items-center justify-start space-x-4">
              <x-public.checkbox type="checkbox" name="mailing" x-model="mailing" id="mailing" value="1" checked @change.prevent="if(!mailing) window.alert('Не соглашаясь на получение рассылок мы не сможем вам направить состав вашего заказа и информацию об отслеживание доставки')"/>
              <label for="mailing" class="text-black block d-text-body m-text-body">Согласие на <a href="{{ route('page', ['page' => 'soglasie_na_poluchenie_reklamnih_rassilok']) }}" target="_blank" class="underline hover:no-underline">получение рассылок</a></label>
            </div>
            @if(config('happy-coupone.active'))
              <div class="flex items-center justify-start space-x-4">
                <x-public.checkbox type="checkbox" name="gift-delivery" id="gift-delivery" value="promocode"/>
                <label for="gift-delivery" class="text-black block d-text-body m-text-body">Согласен со сроками отправки продукции в течение 30 рабочих дней</label>
              </div>
              <div class="flex items-center justify-start space-x-4">
                <x-public.checkbox type="checkbox" name="gift-politika" id="gift-politika" value="promocode"/>
                <label for="gift-politika" class="text-black block d-text-body m-text-body">Я соглашаюсь с условиями акции <a
                    href="{{ route('page', 'politika_promo') }}" target="_blank" class="underline hover:no-underline">«Счастливый купон»</a></label>
              </div>
            @endif
            <div class="text-center">
              <x-public.primary-button type="submit" class="md:h-14 md:w-full md:max-w-[285px] mx-auto mt-[60px]">Оформить заказ</x-public.primary-button>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
    </form>
  </div>
</x-app-layout>
