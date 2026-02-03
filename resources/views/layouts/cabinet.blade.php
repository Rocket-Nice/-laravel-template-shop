
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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
      href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Cormorant+Infant:wght@400;600&family=Montserrat:wght@400;500&family=Playfair+Display:ital@1&display=swap"
      rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('libraries/swiper/swiper-bundle.min.css') }}">
{{--    <link rel="stylesheet" href="{{ asset('libraries/@fancyapps/ui/dist/fancybox/fancybox.css') }}">--}}
    <link href="{{ asset('libraries/Toast.js-master/dist/css/Toast.css') }}" rel="stylesheet">

    <script src="{{ asset('libraries/inputmask.min.js') }}"></script>
{{--    <script src="{{ asset('libraries/@fancyapps/ui/dist/fancybox/fancybox.umd.js') }}"></script>--}}
    <script src="{{ asset('libraries/Toast.js-master/dist/js/Toast.js') }}"></script>
    <script>
        window.cart = {
            init: @json(route('cart.init')),
            update: @json(route('cart.update')),
            remove: @json(route('cart.remove')),
        };
        window.order = {
            index: @json(route('order.index'))
        };

    </script>
    @if (isset($script))
        {{ $script }}
    @endif
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="{{ asset('libraries/swiper/swiper-bundle.min.js') }}"></script>
    @if(!auth()->check()||auth()->id()!=1)
        <style>
            .phpdebugbar {
                display: none;
            }
        </style>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const phpdebugbar = document.querySelector('.phpdebugbar');
                if (phpdebugbar) {
                    phpdebugbar.remove()
                }
            });
        </script>
    @endif
</head>
<body class="antialiased">
@if(isset($settings)&&$settings->where('key', 'maintenanceStatus')->first()->value == 0)
    @auth
        @if(auth()->user()->hasPermissionTo('Доступ к админпанели')||strpos(Route::currentRouteName(), "cabinet") !== false)
            @include('_parts.public.header')
            {{ $slot }}
            @include('_parts.public.footer')
        @else
            @php($message = $settings->where('key', 'maintenanceNotification')->first()->value)
            <div id="loader"
                 class="fixed left-0 top-0 right-0 bottom-0 w-full h-full bg-white/90 flex justify-center items-center"
                 style="z-index: 2000">
                <div class="text-center">
                    <div class="d-headline-4 m-headline-3">Доступ закрыт</div>
                    <div class="d-text-body m-text-body">
                        {!! $message ?? 'На сайте ведутся технические работы.' !!}
                    </div>
                </div>
            </div>
        @endif
    @else
        @php($message = $settings->where('key', 'maintenanceNotification')->first()->value)
        <div id="loader"
             class="fixed left-0 top-0 right-0 bottom-0 w-full h-full bg-white/90 flex justify-center items-center"
             style="z-index: 2000">
            <div class="text-center">
                <div class="d-headline-4 m-headline-3">Доступ закрыт</div>
                <div class="d-text-body m-text-body">
                    {!! $message ?? 'На сайте ведутся технические работы.' !!}
                </div>
            </div>
        </div>
    @endauth
@else
    @include('_parts.public.header')
    {{ $slot }}
    @include('_parts.public.footer')
@endif

<div
  class="max-w-[720px] max-w-[598px] rounded-md overflow-hidden w-20 m-2 hidden space-y-3 mt-3 pl-4 sm:relative sm:left-auto sm:top-auto bg-opacity-60 translate-x-0 mb-16 mb-12"></div>
<script src="{{ asset('libraries/dynamic-adapt.js') }}"></script>
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
