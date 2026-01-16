@section('title', $seo['title'] ?? config('app.name'))
@section('title', $seo['title'] ?? config('app.name'))
<x-app-layout>
  <div class="text-center">
    <div class="h-[50vh] flex flex-col justify-center items-center">
      <div>
        <h2 class="uppercase text-sm sm:text-base md:text-lg lg:text-2xl xl:text-4xl my-5 md:my-9 lg:my-12 text-customBrown text-center">{{ $message['title'] }}</h2>
        <div class="text-customBrown text-sm sm:text-base md:text-lg lg:text-xl xl:text-2xl !leading-1.8">{!! $message['text'] !!}</div>
        @if(getSettings('happyCoupon')&&isset($order)&&$order->giftCoupons()->exists())
          <div class="text-customBrown text-sm sm:text-base md:text-lg lg:text-xl xl:text-2xl !leading-1.8 mt-4">
            Вам доступно участие в акции «Счастливый купон»!
            {{--            Вам доступно участие в акции!--}}
            <form action="{{ route('happy_coupon', [$order->slug]) }}" method="GET">
              <div class="text-center mt-6 sm:mt-9 lg:mt-12 lg:mb-12">
                <x-public.winter-button type="submit" class="h-11 md:h-[58px] px-5 mx-auto text-sm sm:text-md md:text-lg lg:text-xl">Открыть купоны</x-public.winter-button>
              </div>
            </form>
          </div>
        @elseif(getSettings('promo20')&&isset($order)&&$order->giftCoupons()->exists())
          <div class="text-customBrown text-sm sm:text-base md:text-lg lg:text-xl xl:text-2xl !leading-1.8 mt-4">
            Перейдите в личный кабинет, чтобы узнать детали о заказе и подарке.
            <div class="text-center mt-6 sm:mt-9 lg:mt-12 lg:mb-12">
              <x-public.winter-button href="{{ route('cabinet.order.show', $order->slug) }}" class="h-11 md:h-[58px] px-5 mx-auto text-sm sm:text-md md:text-lg lg:text-xl uppercase">Посмотреть подарок</x-public.winter-button>
            </div>
          </div>
        @elseif(getSettings('promo_1+1=3'))
          <div class="bg-springGreen text-white w-screen text-center text-sm sm:text-base md:text-lg lg:text-xl xl:text-2xl !leading-1.8 mt-12 mb-6">
            <div class="flex justify-center items-end">
              <div class="relative flex-1">
                <img src="{{ asset('img/success-page/gift-lg.png?1') }}" alt="" class="absolute bottom-0 right-0 max-w-[192px] w-[25vw]">
              </div>
              <div class="p-4">
                Для вас подарок!<br/>
                @if($coupon)
                Промокод на {{ $coupon->amount }} ₽ на следующую покупку<br/>и
                @endif <span class="font-bold">Моно-масло «Le Mousse»</span>!
              </div>
              <div class="relative flex-1">
                <img src="{{ asset('img/success-page/gift-sm.png?1') }}" alt="" class="absolute bottom-0 left-0 max-w-[138px] w-[18vw]">
              </div>
            </div>
          </div>
          <div class="text-center text-sm sm:text-base md:text-lg lg:text-xl xl:text-2xl !leading-1.8 mb-6">
            Переходите в личный кабинет за подробностями.
          </div>
          <x-public.winter-button href="{{ route('cabinet.order.index') }}" class="h-11 md:h-[58px] px-5 mx-auto text-base sm:text-lg md:text-xl lg:text-2xl">Перейти</x-public.winter-button>
        @endif
      </div>
    </div>
  </div>

  @if(!getSettings('happyCoupon')&&!getSettings('promo20')&&!getSettings('promo_1+1=3'))

    @if(auth()->check()&&!auth()->user()->tgChats()->where('active', true)->exists())
      <div class="mt-12 mb-12 bg-myBeige p-6 m-text-body d-text-body text-center space-y-4">
        <h1 class="text-xl uppercase font-medium leading-1.6">Дарим <span class="cormorantInfant">250</span> бонусов (<span class="cormorantInfant">1</span> бонус = <span class="cormorantInfant">1</span> рубль)</h1>
        <div>
          Для этого подпишитесь на наш БОТ В TELEGRAM<br/>
          * (для начала нужно авторизоваться на сайте)<br/><br/>

          В нем будете получать:<br/><br/>

          ✔️ эксклюзивные скидки и акции<br/>
          ✔️ изменения статуса заказа<br/><br/>

          *бонусы будут начислены в течении <span class="cormorantInfant">7</span> дней
        </div>
        <x-public.primary-button href="https://t.me/lemousse_notifications_bot?start={{ auth()->user()->uuid }}" target="_blank" class="md:w-full max-w-[357px]">Подписаться</x-public.primary-button>
      </div>
    @endif
  <div id="review-alert" class="!px-4 !py-6 sm:!py-[60px] sm:!px-6 w-full max-w-[547px]" style="display: none">
    <div class="m-text-body d-text-body text-center">
      <div class="mb-6"><span class="cormorantInfant">250</span> бонусов на следующий заказ за отзыв на нашем сайте.</div>
      <div>
        <x-public.primary-button type="button" onclick="Fancybox.close()">
          спасибо, обязательно напишу
        </x-public.primary-button>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      Fancybox.show(
        [
          {
            src: '#review-alert',
            width: "900px",
            height: "700px",
          },
        ],
        {
          closeButton: false,
          Toolbar: {
            display: {
              left: [],
              middle: [],
              right: [],
            },
          },
          loop: false,
          touch: false,
          contentClick: false,
          dragToClose: false,
        }
      );
    })

  </script>
  @endif
{{--  <script>--}}
{{--    window.setCookie('goldticketShown', 'true', 1);--}}
{{--  </script>--}}
</x-app-layout>
