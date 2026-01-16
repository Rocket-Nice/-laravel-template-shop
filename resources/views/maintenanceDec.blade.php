
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  @php($currentDomain = parse_url(url('/'), PHP_URL_HOST))
  @if(trim($currentDomain) == 'le-mousse.ru')
    <meta name="robots" content="noindex, nofollow"/>
  @endif
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="canonical" href="https://lemousse.shop/" />
  <link rel="apple-touch-icon-precomposed" sizes="57x57" href="https://lemousse.shop/apple-touch-icon-57x57.png" />
  <link rel="apple-touch-icon-precomposed" sizes="114x114" href="https://lemousse.shop/apple-touch-icon-114x114.png" />
  <link rel="apple-touch-icon-precomposed" sizes="72x72" href="https://lemousse.shop/apple-touch-icon-72x72.png" />
  <link rel="apple-touch-icon-precomposed" sizes="144x144" href="https://lemousse.shop/apple-touch-icon-144x144.png" />
  <link rel="apple-touch-icon-precomposed" sizes="60x60" href="https://lemousse.shop/apple-touch-icon-60x60.png" />
  <link rel="apple-touch-icon-precomposed" sizes="120x120" href="https://lemousse.shop/apple-touch-icon-120x120.png" />
  <link rel="apple-touch-icon-precomposed" sizes="76x76" href="https://lemousse.shop/apple-touch-icon-76x76.png" />
  <link rel="apple-touch-icon-precomposed" sizes="152x152" href="https://lemousse.shop/apple-touch-icon-152x152.png" />
  <link rel="icon" type="image/png" href="https://lemousse.shop/favicon-196x196.png" sizes="196x196" />
  <link rel="icon" type="image/png" href="https://lemousse.shop/favicon-96x96.png" sizes="96x96" />
  <link rel="icon" type="image/png" href="https://lemousse.shop/favicon-32x32.png" sizes="32x32" />
  <link rel="icon" type="image/png" href="https://lemousse.shop/favicon-16x16.png" sizes="16x16" />
  <link rel="icon" type="image/png" href="https://lemousse.shop/favicon-128.png" sizes="128x128" />
  <meta name="application-name" content="Le Mousse"/>
  <meta name="msapplication-TileColor" content="#FFFFFF" />
  <meta name="msapplication-TileImage" content="https://lemousse.shop/mstile-144x144.png" />
  <meta name="msapplication-square70x70logo" content="https://lemousse.shop/mstile-70x70.png" />
  <meta name="msapplication-square150x150logo" content="https://lemousse.shop/mstile-150x150.png" />
  <meta name="msapplication-wide310x150logo" content="https://lemousse.shop/mstile-310x150.png" />
  <meta name="msapplication-square310x310logo" content="https://lemousse.shop/mstile-310x310.png" />

  <title>@yield('title', config('app.name')) – {{ config('app.name') }}</title>
  <meta name="description" content="Мы разрабатываем уникальные рецепты продуктов со сложными и эффективными составами. Главной отличительной чертой наших средств является процентное соотношение активной фазы в составе – более 80%." />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="{{ asset('libraries/@fancyapps/ui/dist/fancybox/fancybox.css') }}">
  <script src="{{ asset('libraries/@fancyapps/ui/dist/fancybox/fancybox.umd.js') }}"></script>
  <link
    href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Cormorant+Infant:wght@400;600&family=Montserrat:wght@400;500&family=Playfair+Display:ital@1&display=swap"
    rel="stylesheet">

  @if (isset($script))
    {{ $script }}
  @endif
  <!-- Scripts -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <script src="{{ asset('libraries/swiper/swiper-bundle.min.js') }}"></script>
</head>
<body class="antialiased">

<header class="px-4 md:px-8 lg:px-14 xl:px-16 py-8 bg-white text-myDark">
  <div class="flex justify-between items-center">
    <div class="flex-1 flex items-center">
      <div class="w-6 h-6"></div>
    </div>
    <div>
      <x-application-logo class="mx-auto w-[200px]"/>
    </div>
    <div class="flex-1 mobile-icons">
      <div class="flex justify-end space-x-4 sm:space-x-6">
        @auth
          <a href="{{ route('cabinet.order.index') }}" class="shrink-0">
            <svg class="w-6 h-6 lg:w-5 lg:h-5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M15.1171 18.0176C14.3838 18.2343 13.5171 18.3343 12.5005 18.3343H7.50045C6.48379 18.3343 5.61712 18.2343 4.88379 18.0176C5.06712 15.8509 7.29212 14.1426 10.0005 14.1426C12.7088 14.1426 14.9338 15.8509 15.1171 18.0176Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M12.5003 1.66602H7.50033C3.33366 1.66602 1.66699 3.33268 1.66699 7.49935V12.4993C1.66699 15.6493 2.61699 17.3743 4.88366 18.016C5.06699 15.8493 7.29199 14.141 10.0003 14.141C12.7087 14.141 14.9337 15.8493 15.117 18.016C17.3837 17.3743 18.3337 15.6493 18.3337 12.4993V7.49935C18.3337 3.33268 16.667 1.66602 12.5003 1.66602ZM10.0003 11.8077C8.35033 11.8077 7.01699 10.466 7.01699 8.81603C7.01699 7.16603 8.35033 5.83268 10.0003 5.83268C11.6503 5.83268 12.9837 7.16603 12.9837 8.81603C12.9837 10.466 11.6503 11.8077 10.0003 11.8077Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M12.9833 8.81733C12.9833 10.4673 11.6499 11.809 9.99994 11.809C8.34994 11.809 7.0166 10.4673 7.0166 8.81733C7.0166 7.16733 8.34994 5.83398 9.99994 5.83398C11.6499 5.83398 12.9833 7.16733 12.9833 8.81733Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </a>
        @else
          <a href="javascript:;" data-src="#authForm" data-fancybox-no-close-btn class="shrink-0"><svg class="w-6 h-6 lg:w-5 lg:h-5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M15.1171 18.0176C14.3838 18.2343 13.5171 18.3343 12.5005 18.3343H7.50045C6.48379 18.3343 5.61712 18.2343 4.88379 18.0176C5.06712 15.8509 7.29212 14.1426 10.0005 14.1426C12.7088 14.1426 14.9338 15.8509 15.1171 18.0176Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M12.5003 1.66602H7.50033C3.33366 1.66602 1.66699 3.33268 1.66699 7.49935V12.4993C1.66699 15.6493 2.61699 17.3743 4.88366 18.016C5.06699 15.8493 7.29199 14.141 10.0003 14.141C12.7087 14.141 14.9337 15.8493 15.117 18.016C17.3837 17.3743 18.3337 15.6493 18.3337 12.4993V7.49935C18.3337 3.33268 16.667 1.66602 12.5003 1.66602ZM10.0003 11.8077C8.35033 11.8077 7.01699 10.466 7.01699 8.81603C7.01699 7.16603 8.35033 5.83268 10.0003 5.83268C11.6503 5.83268 12.9837 7.16603 12.9837 8.81603C12.9837 10.466 11.6503 11.8077 10.0003 11.8077Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M12.9833 8.81733C12.9833 10.4673 11.6499 11.809 9.99994 11.809C8.34994 11.809 7.0166 10.4673 7.0166 8.81733C7.0166 7.16733 8.34994 5.83398 9.99994 5.83398C11.6499 5.83398 12.9833 7.16733 12.9833 8.81733Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
            </svg></a>
          <div class="hidden">
            <x-public.popup id="authForm">
              <x-slot name="icon">
                <h4 class="d-headline-4 m-headline-3 leading-none lh-outline-none">Авторизация</h4>
              </x-slot>
              <form method="POST" action="{{ route('login') }}">
                @csrf
                <input type="hidden" name="redirect" value="0">
                <div class="mb-6">
                  <x-public.order-input type="text" id="email" name="email" placeholder="Email" value="" required autofocus autocomplete="username"/>
                </div>
                <div class="mb-6">
                  <x-public.order-input type="password" id="password" name="password" placeholder="Пароль" required autocomplete="current-password"/>
                </div>
                <div class="text-center">
                  <x-public.green-button type="submit" class="md:w-full max-w-[193px] md:max-w-[223px] lg:max-w-[253px] xl:max-w-[283px] md:text-2xl md:h-14">
                    Войти
                  </x-public.green-button>
                </div>

                <div class="mt-6 text-center">
                  <a href="{{ route('password.request') }}">Забыли пароль?</a>
                </div>
              </form>
            </x-public.popup>
            @endauth
          </div>
      </div>
    </div>
  </div>
</header>

<main>
  <div class="relative">

    {{--    <img src="{{ asset('img/new-year-sk-192024/gift.png?1') }}" alt="" style="position:absolute;top: 10%;left:2%;width:50px;">--}}
    {{--    <img src="{{ asset('img/new-year-sk-192024/gift.png?1') }}" alt="" style="position:absolute;top: 45%;left:6%;width:120px;">--}}
    {{--    <img src="{{ asset('img/new-year-sk-192024/gift.png?1') }}" alt="" style="position:absolute;top: 95%;left:4%;width:50px;">--}}
    {{--    <img src="{{ asset('img/new-year-sk-192024/gift.png?1') }}" alt="" style="position:absolute;top: 10%;right:2%;width:50px;transform:rotate(30deg)">--}}
    {{--    <img src="{{ asset('img/new-year-sk-192024/gift.png?1') }}" alt="" style="position:absolute;top: 45%;right:6%;width:120px;transform:rotate(30deg)">--}}
    {{--    <img src="{{ asset('img/new-year-sk-192024/gift.png?1') }}" alt="" style="position:absolute;top: 95%;right:4%;width:50px;transform:rotate(30deg)">--}}
    <header>
      <div
        class="new-year-sk-linear-gradient font-medium text-[16px] sm:text-[19px] md:text-[22px] lg:text-[26px] xl:text-[32px] 2xl:text-[38px] leading-none uppercase text-center px-7 pt-12 pb-[76px] sm:pb-[80px] md:pb-[84px] lg:pb-[88px] xl:pb-[92px]"
      >
        <p>ДОСТУП ЗАКРЫТ</p>
        <p>ТЕХНИЧЕСКОЕ ОБСЛУЖИВАНИЕ</p>
      </div>
    </header>
    <p
      class="uppercase font-medium text-xl sm:text-[24px] md:text-[28px] lg:text-[32px] xl:text-[36px] 2xl:text-[40px] leading-tight text-center text-newYearSkBlack"
    >
      ПОДГОТОВКА К АКЦИИ
    </p>
    <h1
      class="font-semibold text-5xl sm:text-[72px] md:text-[78px] lg:text-[90px] xl:text-[96px] leading-none uppercase text-center new-year-sk-linear-gradient-2 mb-[41px] sm:mb-[53px] md:mb-[67px] lg:mb-[83px] xl:mb-[100px] 2xl:mb-[120px]"
    >
      СЧАСТЛИВЫЙ<br/>КУПОН
    </h1>
  </div>
  <div
    class="px-[13px] font-normal text-[15px] sm:text-[21px] md:text-[27px] lg:text-[33px] xl:text-[36px] leading-tight text-center text-newYearSkConiferousGreen mb-[14px] sm:mb-[23px] md:mb-[29px] lg:mb-[34px] xl:mb-[38px] 2xl:mb-[42px]"
  >
    <p>Приобретайте средства Le Mousse и получайте купоны,</p>
    <p>
      в которых спрятаны
      <span class="uppercase font-bold font-infant_font"
      >более 30 000 крутых подарков:</span
      >
    </p>
  </div>
  <section
    class="mb-[46px] sm:mb-[61px] md:mb-[71px] lg:mb-[81px] xl:mb-[91px] px-[22px] xl:max-w-[1090px] mx-auto"
  >
    <article
      style="
            background-image: url({{ asset('img/new-year-sk-192024/new-y-bg-green.png') }});
          "
      class="bg-right bg-new-year-sk rounded-[10px] flex justify-between gap-[30px] sm:gap-[45px] md:gap-[60px] lg:gap-[75px] xl:gap-[90px] px-[30px] sm:px-[50px] md:px-[70px] lg:px-[85px] xl:px-[100px] py-[16px] sm:py-[20px] md:py-[25px] lg:py-[31px] xl:py-[37px]"
    >
      <!-- Первый блок -->
      <div
        class="space-y-4 sm:space-y-5 md:space-y-6 lg:space-y-7 xl:space-y-8 max-w-[106px] sm:max-w-[150px] md:max-w-[200px] lg:max-w-[250px] xl:max-w-[400px] flex flex-col items-center justify-between"
      >
        <p
          class="font-normal text-[10px] sm:text-[13px] md:text-[17px] lg:text-[21px] xl:text-[26px] leading-none text-center text-white"
        >
          путёвки на Мальдивы
        </p>
        <img
          class="block w-full max-w-[100px] sm:max-w-[125px] md:max-w-[175px] lg:max-w-[225px] xl:max-w-[285px] self-end"
          src="{{ asset('img/new-year-sk-192024/plane.png') }}"
        />
      </div>
      <!-- Второй блок -->
      <div
        class="space-y-4 sm:space-y-5 md:space-y-6 lg:space-y-7 xl:space-y-8 max-w-[106px] sm:max-w-[150px] md:max-w-[200px] lg:max-w-[250px] xl:max-w-[400px] flex flex-col items-center justify-between"
      >
        <p
          class="font-normal text-[10px] sm:text-[13px] md:text-[17px] lg:text-[21px] xl:text-[26px] leading-none text-center text-white"
        >
          огромное количество продукции Le Mousse
        </p>
        <img
          class="block w-full max-w-[100px] sm:max-w-[125px] md:max-w-[175px] lg:max-w-[225px] xl:max-w-[285px] self-end"
          src="{{ asset('img/new-year-sk-192024/bags.png') }}"
        />
      </div>
      <!-- Третий блок -->
      <div
        class="space-y-4 sm:space-y-5 md:space-y-6 lg:space-y-7 xl:space-y-8 max-w-[106px] sm:max-w-[150px] md:max-w-[200px] lg:max-w-[250px] xl:max-w-[400px] flex flex-col items-center justify-between"
      >
        <p
          class="font-normal text-[10px] sm:text-[13px] md:text-[17px] lg:text-[21px] xl:text-[26px] leading-none text-center text-white"
        >
          много iPhone 16
        </p>
        <img
          class="block w-full max-w-[100px] sm:max-w-[125px] md:max-w-[175px] lg:max-w-[225px] xl:max-w-[285px] self-end"
          src="{{ asset('img/new-year-sk-192024/iphones.png') }}"
        />
      </div>
    </article>
  </section>

  <section>
    <p
      class="uppercase font-normal text-[16px] sm:text-[20px] md:text-[24px] lg:text-[28px] xl:text-[32px] leading-none text-center text-newYearSkDarkGreen text-center"
    >
      Старт акции
    </p>
    <h2
      class="font-infant_font font-bold text-[30px] sm:text-[48px] md:text-[54px] lg:text-[66px] xl:text-[72px] leading-none uppercase text-center text-newYearSkDarkGreen my-2"
    >
      19 декабря в 10:00 мск
    </h2>
    <p
      class="font-infant_font uppercase font-normal text-[16px] sm:text-[20px] md:text-[24px] lg:text-[28px] xl:text-[32px] leading-none text-center text-newYearSkDarkGreen mb-[35px] sm:mb-[49px] md:mb-[52px] lg:mb-[54px] xl:mb-[55px]"
    >
      только 24 часа!
    </p>
  </section>
  <section class="text-center">
    <p
      class="font-normal text-[15px] sm:text-[21px] md:text-[27px] lg:text-[33px] xl:text-[36px] leading-tight mb-[9px] sm:mb-[16px] md:mb-[24px] lg:mb-[28px] xl:mb-[32px]"
    >
      До нашумевшей акции осталось:
    </p>
    <div
      class="py-4 sm:py-[16px] md:py-[22px] lg:py-[26px] xl:py-[29px] px-[50px] sm:px-[65px] md:px-[79px] lg:px-[89px] xl:px-[93px] bg-myGreen w-fit mx-auto rounded-[10px] mb-[44px] sm:mb-[47px] md:mb-[51px] lg:mb-[53px] xl:mb-[56px]"
    >
      <p id="timer"
         class="font-bold font-infant_font text-[40px] sm:text-[58px] md:text-[62px] lg:text-[68px] xl:text-[72px] leading-none uppercase text-center text-newYearSkDarkGreen"
      >
        <span id="hours-1"></span>
        <span id="hours-2"></span>:
        <span id="minutes-1"></span>
        <span id="minutes-2"></span>:
        <span id="seconds-1"></span>
        <span id="seconds-2"></span>
      </p>
    </div>
    <script>
      if(document.getElementById("timer")) {
        // var countDownDate = new Date("Dec 19, 2024 10:00:00").getTime();
        var countDownDate = @json(strtotime("Dec 19, 2024 10:00:00") * 1000);
        function splitNumber(number) {
          const paddedNumber = number.toString().padStart(2, '0');
          return [paddedNumber[0], paddedNumber[1]];
        }

        var x = setInterval(function() {
          var now = new Date().getTime();
          var distance = countDownDate - now;

          // Вычисляем общее количество часов
          const totalHours = Math.floor(distance / (1000 * 60 * 60));
          const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
          const seconds = Math.floor((distance % (1000 * 60)) / 1000);

          // Разбиваем каждое значение на отдельные цифры
          const [hours1, hours2] = splitNumber(totalHours);
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
            document.getElementById("timer").innerHTML = "Время истекло";
          }
        }, 1000);
      }
    </script>
  </section>
  <footer>
    <div
      class="font-light text-[12px] sm:text-[16px] md:text-[20px] lg:text-[22px] xl:text-[24px] leading-1.3 mx-auto text-center pb-[41px] sm:pb-[73px] md:pb-[105px] lg:pb-[127px] xl:pb-[147px]"
    >
      <p class="block text-center mb-[17px]">
        Нужна помощь? Напишите в
        <a
          href="https://{{ getSettings('tg_support') }}"
          class="underline text-skip-ink-none text-center"
        >службу поддержки клиентов</a
        >
      </p>
      <div class="flex gap-3 justify-center">
        <a
          href="https://vk.com/le__mousse"
          class="underline text-skip-ink-none text-center"
        >
          ВКонтакте
        </a>
        <a
          href="https://www.ozon.ru/seller/le-mousse-1678980/products/?miniapp=seller_1678980"
          class="underline text-skip-ink-none text-center"
        >
          Озон
        </a>
      </div>
    </div>
  </footer>
</main>
</body>
</html>
