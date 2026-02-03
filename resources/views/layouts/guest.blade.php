
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
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <script src="{{ asset('libraries/inputmask.min.js') }}"></script>
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
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
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <a href="/">
                    <x-application-logo class="fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
        @if(getSettings('catInBag'))
            <x-cat-popup />
        @endif
    </body>
</html>
