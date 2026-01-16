
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
<div class="relative py-12 w-full h-full bg-white/90 px-2">
  <div class="text-center">
    <div class="text-sm sm:text-base md:text-lg lg:text-xl xl:text-2xl uppercase mb-6">Доступ закрыт<br/>Техническое обслуживание</div>
    <div class="text-sm md:text-base lg:text-lg xl:text-xl">
      {!! $message ?? '' !!}
      <br/>
      <div>
        <br/>
        Поддержка <a href="https://{{ getSettings('tg_support') }}" target="_blank">{{ getSettings('tg_support') }}</a>
      </div>
    </div>
    <div class="flex space-x-4 justify-center mt-4" style="text-align: center;padding-top:16px;">
      <a href="https://vk.com/le__mousse" target="_blank">
        <img src="https://lemousse.shop/mailing/vk.png" alt="vk" style="width:50px;height:50px;" />
      </a>
      <a href="https://ozon.ru/t/qAJKbM4" target="_blank">
        <img src="https://lemousse.shop/mailing/ozon.png" alt="ozon" style="width:50px;height:50px;" />
      </a>
    </div>
    {{--    <div class="flex space-x-4 justify-center mt-4" style="text-align: center;padding-top:16px;">--}}
    {{--      --}}
    {{--    </div>--}}
  </div>
</div>

<footer class="bg-transparent py-6">
  <div class="text-center mb-6">
    <x-application-logo class="max-w-[200px] mx-auto" />
  </div>
  <div class="space-y-4 text-center mb-4 text-xl md:text-2xl">
    <div class="p-2">
      <span class="cormorantInfant">8 (8442) 51-50-05</span><br/><span class="text-sm md:text-lg lg:text-xl ">Режим работы горячей линии пн-пт<br/>с <span class="cormorantInfant">9:00</span> до <span class="cormorantInfant">18:00</span> по мск</span>
    </div>
  </div>
  <div class="text-center text-sm md:text-lg lg:text-xl opacity-26 mt-8 space-y-1.5">
    <div>ИП Нечаева Ольга Андреевна</div>
    <div>ИНН <span class="cormorantInfant">344115294608</span></div>
    <div><span class="cormorantInfant">400048</span>, г. Волгоград а/я 558</div>
  </div>
</footer>
</body>
</html>
