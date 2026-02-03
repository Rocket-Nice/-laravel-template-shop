
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
    <meta name="mailru-verification" content="56b0f2187cb7acf5" />
    <meta name="yandex-verification" content="91da326f6c38c39d" />
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

{{--    --}}
    @if(getSettings('promo20'))
        <title>Акция «-20%» на весь ассортимент + ПОДАРКИ!</title>
    @elseif(getSettings('promo30'))
        <title>Акция «-30%» на сеты + ПОДАРКИ!</title>
    @else
        <title>@yield('title', config('app.name')) – {{ config('app.name') }}</title>
    @endif
    <meta name="description" content="Мы разрабатываем уникальные рецепты продуктов со сложными и эффективными составами. Главной отличительной чертой наших средств является процентное соотношение активной фазы в составе – более 80%." />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
      href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Cormorant+Infant:wght@400;600&family=Montserrat:wght@400;500&family=Playfair+Display:ital@1&family=Roboto+Condensed:wght@300&display=swap"
      rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('libraries/swiper/swiper-bundle.min.css') }}">
{{--    <link rel="stylesheet" href="{{ asset('libraries/@fancyapps/ui/dist/fancybox/fancybox.css') }}">--}}
    <link href="{{ asset('libraries/Toast.js-master/dist/css/Toast.css') }}" rel="stylesheet">

    <script src="{{ asset('libraries/inputmask.min.js') }}"></script>
{{--    <script src="{{ asset('libraries/@fancyapps/ui/dist/fancybox/fancybox.umd.js') }}"></script>--}}
    <script src="{{ asset('libraries/Toast.js-master/dist/js/Toast.js?5') }}"></script>
    <script src="{{ asset('libraries/dynamic-adapt.js?2') }}"></script>

    <script>
        window.auth = {
            check: @json(auth()->check())
        };
        window.products = {
            notification: @json(route('product.notification'))
        }
        window.cart = {
            init: @json(route('cart.init')),
            update: @json(route('cart.update')),
            remove: @json(route('cart.remove')),
        };
        window.order = {
            index: @json(route('order.index'))
        };
        @if(getSettings('promo_1+1=3'))
          window.promo113 = true;
        @endif
        @if(getSettings('promo20'))
          window.promo20 = true;
        @endif
        @if(getSettings('promo30'))
          window.promo30 = true;
        @endif
        @if(getSettings('puzzlesStatus'))
          window.puzzles = true;
        @endif
        @if(getSettings('goldTicket'))
          window.goldTicket = true;
        @else
          window.goldTicket = false;
        @endif
    </script>
    @if (isset($script))
        {{ $script }}
    @endif
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="{{ asset('libraries/swiper/swiper-bundle.min.js') }}"></script>
</head>
<body class="antialiased">
@include('_parts.public.header')
{{ $slot }}
@include('_parts.public.footer')

<div
  class="md:list-inside md:text-center max-w-[720px] backdrop-blur-sm max-w-[598px] rounded-md overflow-hidden w-20 m-2 hidden space-y-3 mt-3 pl-4 sm:relative sm:left-auto sm:top-auto bg-opacity-60 translate-x-0 mb-16 mb-12 pb-4"></div>

@if($errors->any()||session('success')||session('status')||session('warning'))
    <div class="fixed bottom-0 right-0 m-6 space-y-2 max-w-xl d-text-body m-text-body z-50">
        @if($errors->any())
            @foreach($errors->all() as $error)
                <div class="toast-item border border-black border-l-8 border-l-red-600 bg-white py-2 px-3 mb-2">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-bold">Ошибка</h3>
                            <p class="">{!! $error !!}</p>
                        </div>
                        <button class="text-black" tabindex="0" data-event="close">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            @endforeach
        @endif
        @if(session('success'))
            <div class="toast-item border border-black border-l-8 border-l-green-600 bg-white py-2 px-3 mb-2">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="">{{ session('success') }}</p>
                    </div>
                    <button class="text-black" tabindex="0" data-event="close">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                             class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif
        @if(session('warning'))
            <div class="toast-item border border-black border-l-8 border-l-yellow-500 bg-white py-2 px-3 mb-2">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-bold">Внимание</h3>
                        <p class="">{{ session('warning') }}</p>
                    </div>
                    <button class="text-black" tabindex="0" data-event="close">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                             class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif
        @if(session('status'))
            <div class="toast-item border border-black border-l-8 border-l-blue-600 bg-white py-2 px-3 mb-2">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="">{{ session('status') }}</p>
                    </div>
                    <button class="text-black" tabindex="0" data-event="close">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                             class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

    </div>
@endif
@if(getSettings('catInBag'))
    <x-cat-popup />
@endif
</body>
</html>
