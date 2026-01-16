
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
  <link rel="canonical" href="{{ config('app.url') }}/" />
  <link rel="apple-touch-icon-precomposed" sizes="57x57" href="{{ config('app.url') }}/apple-touch-icon-57x57.png" />
  <link rel="apple-touch-icon-precomposed" sizes="114x114" href="{{ config('app.url') }}/apple-touch-icon-114x114.png" />
  <link rel="apple-touch-icon-precomposed" sizes="72x72" href="{{ config('app.url') }}/apple-touch-icon-72x72.png" />
  <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{ config('app.url') }}/apple-touch-icon-144x144.png" />
  <link rel="apple-touch-icon-precomposed" sizes="60x60" href="{{ config('app.url') }}/apple-touch-icon-60x60.png" />
  <link rel="apple-touch-icon-precomposed" sizes="120x120" href="{{ config('app.url') }}/apple-touch-icon-120x120.png" />
  <link rel="apple-touch-icon-precomposed" sizes="76x76" href="{{ config('app.url') }}/apple-touch-icon-76x76.png" />
  <link rel="apple-touch-icon-precomposed" sizes="152x152" href="{{ config('app.url') }}/apple-touch-icon-152x152.png" />
  <link rel="icon" type="image/png" href="{{ config('app.url') }}/favicon-196x196.png" sizes="196x196" />
  <link rel="icon" type="image/png" href="{{ config('app.url') }}/favicon-96x96.png" sizes="96x96" />
  <link rel="icon" type="image/png" href="{{ config('app.url') }}/favicon-32x32.png" sizes="32x32" />
  <link rel="icon" type="image/png" href="{{ config('app.url') }}/favicon-16x16.png" sizes="16x16" />
  <link rel="icon" type="image/png" href="{{ config('app.url') }}/favicon-128.png" sizes="128x128" />
  <meta name="application-name" content="Le Mousse"/>
  <meta name="msapplication-TileColor" content="#FFFFFF" />
  <meta name="msapplication-TileImage" content="{{ config('app.url') }}/mstile-144x144.png" />
  <meta name="msapplication-square70x70logo" content="{{ config('app.url') }}/mstile-70x70.png" />
  <meta name="msapplication-square150x150logo" content="{{ config('app.url') }}/mstile-150x150.png" />
  <meta name="msapplication-wide310x150logo" content="{{ config('app.url') }}/mstile-310x150.png" />
  <meta name="msapplication-square310x310logo" content="{{ config('app.url') }}/mstile-310x310.png" />

  <title>@yield('title', config('app.name')) – {{ config('app.name') }}</title>
  <meta name="description" content="Мы разрабатываем уникальные рецепты продуктов со сложными и эффективными составами. Главной отличительной чертой наших средств является процентное соотношение активной фазы в составе – более 80%." />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="{{ asset('libraries/@fancyapps/ui/dist/fancybox/fancybox.css') }}">
  <script src="{{ asset('libraries/@fancyapps/ui/dist/fancybox/fancybox.umd.js') }}"></script>
  <link
    href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300..700;1,300..700&family=Cormorant+Infant:ital,wght@0,300..700;1,300..700&family=Cormorant+SC:wght@300;400;500;600;700&family=Cormorant:ital,wght@0,300..700;1,300..700&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet"
  />

  <script src="{{ asset('libraries/inputmask.min.js') }}"></script>
  @if (isset($script))
    {{ $script }}
  @endif
  <!-- Scripts -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <script src="{{ asset('libraries/swiper/swiper-bundle.min.js') }}"></script>
  <script>
    window.cart = {
      init: @json(route('cart.init')),
      update: @json(route('cart.update')),
      remove: @json(route('cart.remove')),
    };
  </script>
</head>
<body class="antialiased font-roboto_font">

<header class="px-4 md:px-8 lg:px-14 xl:px-16 py-8 text-myDark">
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

<div class="pb-[24px] pt-[13px] lg:pb-[20px]">
  <p
    class="mb-[18px] text-center text-[12px] font-light leading-[1.24] text-[#3b413d] sm:mb-5 sm:text-12 lg:mb-6 lg:font-montserrat_font lg:text-[16px] lg:text-[#262625] xl:mb-[27px]"
  >
    Доступ закрыт. Техническое обслуживание.
  </p>
  <h1
    class="text-center font-cormorant_sc_font text-22 font-semibold uppercase leading-none tracking-[-0.04em] text-[#3b413d] md:text-28 lg:font-main-font lg:text-[32px] lg:font-medium"
  >
    АКЦИЯ ЗАВЕРШЕНА!
  </h1>
</div>

<main class="mb-[38px] xl:mb-14">
  <img
    class="mx-auto mb-5 block w-full max-w-[228px] lg:mb-10 xl:max-w-[271px]"
    src="{{ asset('img/apparat.png') }}"
    alt="Фото игрового автомата"
  />
  <p
    class="mx-auto max-w-[450px] text-center font-roboto_font text-16 font-light leading-[1.03] tracking-[-0.04em] text-[#3b413d] sm:text-22 md:text-28 lg:text-[30px]"
  >
    29 апреля в 11:00 мск сайт начнёт работать
    <span class="block lg:inline-block">в обычном режиме</span>
  </p>
</main>

<footer>
  <div
    class="mx-auto pb-[53px] text-center text-[12px] font-light leading-1.3 sm:max-w-56 md:max-w-none md:pb-[50px] lg:pb-[65px] lg:text-[16px] xl:pb-[60px]"
  >
    <p
      class="mx-auto mb-[9px] block text-center font-light opacity-60 xl:mb-[14px]"
    >
      Нужна помощь? Напишите в&nbsp;<a
        style="text-decoration-thickness: 1px"
        href="https://t.me/lemousse_support_bot"
        class="text-skip-ink-none text-center underline"
      >службу поддержки клиентов</a
      >
    </p>
    <div
      class="mx-auto flex justify-center gap-12 opacity-60 md:max-w-[220px]"
    >
      <a
        style="text-decoration-thickness: 1px"
        href="https://www.wildberries.ru/brands/le-mousse?utm_source=taplink&amp;utm_medium=cpc&amp;utm_campaign=1366629-id-brandLM"
        class="text-skip-ink-none text-center font-light underline md:w-fit"
      >
        Wildberries
      </a>
      <a
        style="text-decoration-thickness: 1px"
        href="https://ozon.onelink.me/SNMZ/0hbqhhfx"
        class="text-skip-ink-none text-center font-light underline md:w-fit"
      >
        Ozon
      </a>
    </div>
  </div>
</footer>
</main>
</body>
</html>
