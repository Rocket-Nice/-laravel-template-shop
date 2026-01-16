
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
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">


        <meta name="description" content="Мы разрабатываем уникальные рецепты продуктов со сложными и эффективными составами. Главной отличительной чертой наших средств является процентное соотношение активной фазы в составе – более 80%." />
        <!-- Libs -->
        <link rel="stylesheet" href="{{ asset('libraries/air-datepicker/air-datepicker.css') }}">
        <link rel="stylesheet" href="{{ asset('libraries/choices.js/public/assets/styles/choices.min.css') }}">
        <script src="{{ asset('libraries/choices.js/public/assets/scripts/choices.min.js') }}"></script>
        <script src="{{ asset('libraries/air-datepicker/air-datepicker.js') }}"></script>

        <link rel="stylesheet" href="{{ asset('libraries/@fancyapps/ui/dist/fancybox/fancybox.css') }}">
        <script src="{{ asset('libraries/@fancyapps/ui/dist/fancybox/fancybox.umd.js') }}"></script>
        <script src="{{ asset('libraries/inputmask.min.js') }}"></script>
        <!-- Scripts -->
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
        </script>
        @if (isset($custom_vite))
            {{ $custom_vite }}
        @else
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        @yield('style')
        @if(!auth()->check()||auth()->id()!=1)
            <style>
                .phpdebugbar {
                    display: none;
                }
            </style>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const phpdebugbar = document.querySelector('.phpdebugbar');
                    if(phpdebugbar){
                        phpdebugbar.remove()
                    }
                });
            </script>
        @endif
    </head>
    <body class="antialiased h-full w-full">
        {{ $slot }}
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
    </body>
</html>
