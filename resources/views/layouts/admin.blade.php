
  <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
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

  <!-- Libs -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"/>
  <link rel="stylesheet" href="{{ asset('libraries/air-datepicker/air-datepicker.css') }}">
  <link rel="stylesheet" href="{{ asset('libraries/@fancyapps/ui/dist/fancybox/fancybox.css') }}">
  <link rel="stylesheet" href="{{ asset('libraries/choices.js/public/assets/styles/choices.min.css') }}">
  <script src="{{ asset('libraries/choices.js/public/assets/scripts/choices.min.js') }}"></script>
  <script src="{{ asset('libraries/air-datepicker/air-datepicker.js') }}"></script>
  <script src="{{ asset('libraries/@fancyapps/ui/dist/fancybox/fancybox.umd.js') }}"></script>
  <script src="{{ asset('libraries/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>

  <script>
    window.filemanger = {
      video: @json(route('admin.unisharp.lfm.show')),
      image: @json(route('admin.unisharp.lfm.show')),
      file: @json(route('admin.unisharp.lfm.show')),
    };
  </script>
  <!-- Scripts -->
  @vite(['resources/css/admin.css', 'resources/js/admin.js'])
  @if (isset($style))
    {{ $style }}
  @endif
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
<body class="font-sans antialiased h-full w-full">
<div class="flex min-h-full bg-gray-100">
  @include('_parts.admin.sidebar')
  <div class="page-container transition-all container-fluid w-full mx-auto p-0 sm:max-w-[calc(100vw-200px)]">
    @include('_parts.admin.header')
    <div class="p-4 pb-0 flex flex-col md:flex-row justify-between items-center">
      <!-- Page Heading -->
      @if (isset($header))
        {{ $header }}
      @endif
    </div>
    <div class="bg-white rounded shadow-sm m-4 p-4 mt-0">
      {{ $slot }}
    </div>
  </div>
</div>
<div class="overflow-hidden max-w-full object-cover hidden object-center"></div>
{{--        <div class="min-h-screen bg-gray-100">--}}
{{--            @include('layouts.navigation')--}}

{{--            <!-- Page Heading -->--}}
{{--            @if (isset($header))--}}
{{--                <header class="bg-white shadow">--}}
{{--                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">--}}
{{--                        {{ $header }}--}}
{{--                    </div>--}}
{{--                </header>--}}
{{--            @endif--}}

{{--            <!-- Page Content -->--}}
{{--            <main>--}}
{{--                {{ $slot }}--}}
{{--            </main>--}}
{{--        </div>--}}
<script>

  var sidebarBtn = document.getElementById('sidebarBtn');
  var sidebar = document.getElementById('sidebar');
  var sidebarStatus = localStorage.getItem('sidebarStatus');
  document.addEventListener("DOMContentLoaded", function () {
    sidebarBtn = document.getElementById('sidebarBtn');
    sidebar = document.getElementById('sidebar');
    sidebarStatus = localStorage.getItem('sidebarStatus');

    sidebarBtn.addEventListener('click', (event) => {
      event.preventDefault();
      if (sidebar.classList.contains('sidebar-showed')) { // скрыт
        hideSidebar();
      } else { // показан
        showSidebar();
      }
    })
  });
  // window.addEventListener('resize', handleSidebar);
  handleSidebar();

  if (localStorage.getItem('sidebarStatus') == 0) {
    hideSidebar();
  } else {
    if (window.innerWidth >= 640) {
      sidebar.classList.add('sidebar-showed');
    }
  }

  function getElementPosition(element) {
    return {
      x: element.offsetLeft,
      y: element.offsetTop
    };
  }

  function handleSidebar() {
    if (window.innerWidth < 640) {
      sidebar.classList.add('-translate-x-full');
    } else {
      sidebar.classList.remove('-translate-x-full');
    }
  }

  function hideSidebar() {
    if (window.innerWidth < 640) {
      console.log('hide')
      sidebarBtn.style.position = 'relative';
      sidebarBtn.style.zIndex = '1';
      sidebarBtn.style.top = 'auto';
      sidebarBtn.style.right = 'auto';
      sidebarBtn.style.color = '#000';
      sidebar.classList.add('-translate-x-full');
      document.body.style.overflow = ''
    } else {
      sidebar.style.width = '0px';
      sidebar.style.minWidth = '0px';
      document.querySelector('.page-container').style.maxWidth = 'calc(100vw - 1rem)';
      sidebar.querySelector('.sidebarContent').classList.add('hidden');
    }
    sidebar.classList.remove('sidebar-showed');
    localStorage.setItem('sidebarStatus', 0);
  }

  function showSidebar() {
    if (window.innerWidth < 640) {
      console.log('show')
      document.body.style.overflow = 'hidden'
      let position = getElementPosition(sidebarBtn);
      sidebarBtn.style.position = 'fixed';
      sidebarBtn.style.zIndex = '1000';
      sidebarBtn.style.top = position.y + 'px';
      sidebarBtn.style.right = position.x + 'px';
      sidebarBtn.style.color = '#fff';
      sidebar.classList.remove('-translate-x-full');
    } else {
      sidebar.style.width = null;
      sidebar.style.minWidth = null;
      document.querySelector('.page-container').style.maxWidth = null;
      sidebar.querySelector('.sidebarContent').classList.remove('hidden');
    }
    sidebar.classList.add('sidebar-showed');
    localStorage.setItem('sidebarStatus', 1);
  }
</script>
<script>
  const navLinks = document.querySelectorAll("#sidebar .nav-link, .nav-link");
  const location2 = window.location.protocol + '//' + window.location.host + window.location.pathname;

  navLinks.forEach(link => {
    if (link.href === location2) {
      link.classList.add('bg-black/20');
      if (link.parentNode.parentNode.classList.contains('dropdown')) {
        link.parentNode.classList.remove('hidden');
        link.parentNode.parentNode.querySelector('.nav-parent').classList.add('bg-black/20');
      }
    }
  });
</script>

@include('_parts.toasts')

@if (isset($script))
  {{ $script }}
@endif
</body>
</html>
