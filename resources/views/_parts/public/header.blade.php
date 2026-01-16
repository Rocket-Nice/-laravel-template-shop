@if(getSettings('winterMode'))
  @php($bgColor = 'bg-winterGreen')
  @php($textColor = 'text-white')
  @php($placeholderColor = 'text-white')
  @php($borderColor = 'border-white')
@elseif(getSettings('springMode'))
  @php($bgColor = 'bg-springGreen')
  @php($textColor = 'text-white')
  @php($placeholderColor = 'placeholder:text-white')
  @php($borderColor = 'border-white')
@else
  @php($bgColor = 'bg-myLightGray')
  @php($textColor = 'text-myDark')
  @php($placeholderColor = 'text-myDark')
  @php($borderColor = 'border-myDark')
@endif
<style>
  [x-cloak] {
    display: none !important;
  }
</style>
<div x-cloak x-data="{open: false}" :class="{
          'translate-y-0': open,
          '-translate-y-full': !open
        }" @toggle-menu.window="open = !open"
     class="{{ $bgColor }} {{ $textColor }} z-50 flex flex-col justify-between fixed top-0 left-0 w-full h-screen transform transition-transform duration-300 overflow-y-auto">
  <!-- Кнопка закрытия -->
  <div class="flex justify-end p-9">
    <button type="button" @click="open = false">
      <svg class="w-6 h-6 lg:w-5 lg:h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M2 2L22 22M2 22L22 2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </button>
  </div>
  <div class="mobile-search px-6 mb-4"></div>
  <div class="flex-1 px-6">
    <!-- Ссылки в меню -->
    <ul id="mobile-menu-nav" class="space-y-4 text-xl pb-12"></ul>
  </div>
  {{--  <div class="p-12 text-center">--}}
  {{--    icons--}}
  {{--  </div>--}}
</div>
<div x-cloak x-data="headerData()" :class="{
          'translate-y-0': showHeader,
          '-translate-y-full': !showHeader,
          'fixed top-0 left-0 right-0': fixedHeader,
          'absolute top-0 left-0 right-0': !fixedHeader
        }"
     class="sticky-header w-full z-30 px-6 md:px-8 lg:px-14 xl:px-21.5 py-4 {{ $bgColor }} {{ $textColor }} text-customBlack transition ease-out duration-300">
  <div class="flex justify-between items-center">
    <div class="flex-1 flex items-center">
      <button x-data @click="$dispatch('toggle-menu')" type="button">
        <svg class="w-6 h-6 lg:w-5 lg:h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M3 7H21" stroke="currentColor" stroke-linecap="round"/>
          <path d="M3 12H21" stroke="currentColor" stroke-linecap="round"/>
          <path d="M3 17H21" stroke="currentColor" stroke-linecap="round"/>
        </svg>
      </button>
    </div>
    <div>
      <a href="{{ route('page.index') }}">
        {{--                <x-application-logo class="mx-auto max-w-[39.74vw] sm:max-w-none w-full"/>--}}
        <img src="{{ asset('img/season/logo-winter.png?2')  }}" alt="" style="width: 300px;" class="mx-auto max-w-[39.74vw] sm:max-w-none w-full">
{{--        <img src="{{ asset('img/lm-spring-logo.png?1')  }}" alt="" style="width: 300px;" class="mx-auto max-w-[39.74vw] sm:max-w-none w-full">--}}
        {{--        <img src="{{ asset('winter-logo.png')  }}" alt="" style="width: 300px;" class="mx-auto max-w-[39.74vw] sm:max-w-none w-full">--}}
      </a>
    </div>
    <div class="flex-1">
      <div class="flex justify-end space-x-4 sm:space-x-6">
        <a href="{{ route('order.index') }}" class="shrink-0 cart-icon relative inline-block">
          <span
            class="cart-counter font-infant_font">{{ \Gloudemans\Shoppingcart\Facades\Cart::instance('cart')->count() }}</span>
          <svg class="w-6 h-6 lg:w-5 lg:h-5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M7.08301 11.875C7.08301 13.475 8.39967 14.7917 9.99967 14.7917C11.5997 14.7917 12.9163 13.475 12.9163 11.875"
              stroke="currentColor" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M7.34186 1.66602L4.3252 4.69102" stroke="currentColor" stroke-miterlimit="10"
                  stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M12.6582 1.66602L15.6749 4.69102" stroke="currentColor" stroke-miterlimit="10"
                  stroke-linecap="round" stroke-linejoin="round"/>
            <path
              d="M1.66699 6.54167C1.66699 5 2.49199 4.875 3.51699 4.875H16.4837C17.5087 4.875 18.3337 5 18.3337 6.54167C18.3337 8.33333 17.5087 8.20833 16.4837 8.20833H3.51699C2.49199 8.20833 1.66699 8.33333 1.66699 6.54167Z"
              stroke="currentColor"/>
            <path
              d="M2.91699 8.33398L4.09199 15.534C4.35866 17.1507 5.00033 18.334 7.38366 18.334H12.4087C15.0003 18.334 15.3837 17.2007 15.6837 15.634L17.0837 8.33398"
              stroke="currentColor" stroke-linecap="round"/>
          </svg>
        </a>
        @auth
          <a href="{{ route('cabinet.order.index') }}" class="shrink-0">
            <svg class="w-6 h-6 lg:w-5 lg:h-5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                d="M15.1171 18.0176C14.3838 18.2343 13.5171 18.3343 12.5005 18.3343H7.50045C6.48379 18.3343 5.61712 18.2343 4.88379 18.0176C5.06712 15.8509 7.29212 14.1426 10.0005 14.1426C12.7088 14.1426 14.9338 15.8509 15.1171 18.0176Z"
                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
              <path
                d="M12.5003 1.66602H7.50033C3.33366 1.66602 1.66699 3.33268 1.66699 7.49935V12.4993C1.66699 15.6493 2.61699 17.3743 4.88366 18.016C5.06699 15.8493 7.29199 14.141 10.0003 14.141C12.7087 14.141 14.9337 15.8493 15.117 18.016C17.3837 17.3743 18.3337 15.6493 18.3337 12.4993V7.49935C18.3337 3.33268 16.667 1.66602 12.5003 1.66602ZM10.0003 11.8077C8.35033 11.8077 7.01699 10.466 7.01699 8.81603C7.01699 7.16603 8.35033 5.83268 10.0003 5.83268C11.6503 5.83268 12.9837 7.16603 12.9837 8.81603C12.9837 10.466 11.6503 11.8077 10.0003 11.8077Z"
                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
              <path
                d="M12.9833 8.81733C12.9833 10.4673 11.6499 11.809 9.99994 11.809C8.34994 11.809 7.0166 10.4673 7.0166 8.81733C7.0166 7.16733 8.34994 5.83398 9.99994 5.83398C11.6499 5.83398 12.9833 7.16733 12.9833 8.81733Z"
                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </a>
        @else
          <a href="javascript:;" data-src="#authForm" data-fancybox-no-close-btn class="shrink-0">
            <svg class="w-6 h-6 lg:w-5 lg:h-5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                d="M15.1171 18.0176C14.3838 18.2343 13.5171 18.3343 12.5005 18.3343H7.50045C6.48379 18.3343 5.61712 18.2343 4.88379 18.0176C5.06712 15.8509 7.29212 14.1426 10.0005 14.1426C12.7088 14.1426 14.9338 15.8509 15.1171 18.0176Z"
                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
              <path
                d="M12.5003 1.66602H7.50033C3.33366 1.66602 1.66699 3.33268 1.66699 7.49935V12.4993C1.66699 15.6493 2.61699 17.3743 4.88366 18.016C5.06699 15.8493 7.29199 14.141 10.0003 14.141C12.7087 14.141 14.9337 15.8493 15.117 18.016C17.3837 17.3743 18.3337 15.6493 18.3337 12.4993V7.49935C18.3337 3.33268 16.667 1.66602 12.5003 1.66602ZM10.0003 11.8077C8.35033 11.8077 7.01699 10.466 7.01699 8.81603C7.01699 7.16603 8.35033 5.83268 10.0003 5.83268C11.6503 5.83268 12.9837 7.16603 12.9837 8.81603C12.9837 10.466 11.6503 11.8077 10.0003 11.8077Z"
                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
              <path
                d="M12.9833 8.81733C12.9833 10.4673 11.6499 11.809 9.99994 11.809C8.34994 11.809 7.0166 10.4673 7.0166 8.81733C7.0166 7.16733 8.34994 5.83398 9.99994 5.83398C11.6499 5.83398 12.9833 7.16733 12.9833 8.81733Z"
                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </a>
        @endauth
      </div>
    </div>
  </div>
</div>
{{--<div class=" hidden"></div>--}}
<header class="px-4 md:px-8 lg:px-14 xl:px-16 py-6 lg:pt-14 lg:pb-20 {{ $bgColor }} {{ $textColor }}">
  {{--<header class="opacity-0 sm:opacity-100 px-4 md:px-8 lg:px-14 xl:px-16 pb-4 lg:pb-20 {{ $bgColor }} {{ $textColor }} @if(mb_strtolower(Route::currentRouteName())=='product.index') headerAbsolute @endif">--}}
  <div class="flex justify-between items-center">
    <div class="flex-1 flex items-center">
      <button x-data @click="$dispatch('toggle-menu')" type="button" class="lg:hidden">
        <svg class="w-6 h-6 lg:w-5 lg:h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M3 7H21" stroke="currentColor" stroke-linecap="round"/>
          <path d="M3 12H21" stroke="currentColor" stroke-linecap="round"/>
          <path d="M3 17H21" stroke="currentColor" stroke-linecap="round"/>
        </svg>
      </button>
    </div>
    <div><a href="{{ route('page.index') }}">
        {{--                <x-application-logo class="mx-auto max-w-[39.74vw] sm:max-w-none w-full"/>--}}
        <img src="{{ asset('img/season/logo-winter.png?2')  }}" alt="" style="width: 300px;" class="mx-auto max-w-[39.74vw] sm:max-w-none w-full">
{{--        <img src="{{ asset('img/lm-spring-logo.png?1')  }}" alt="" style="width: 300px;" class="mx-auto max-w-[39.74vw] sm:max-w-none w-full">--}}
        {{--        <img src="{{ asset('winter-logo.png')  }}" alt="" style="width: 300px;" class="mx-auto max-w-[39.74vw] sm:max-w-none w-full">--}}
      </a></div>
    <div class="flex-1 mobile-icons">
      <div class="flex justify-end space-x-4 sm:space-x-6 lg:hidden">
        <a href="{{ route('order.index') }}" class="shrink-0 cart-icon relative inline-block">
          <span
            class="cart-counter font-infant_font">{{ \Gloudemans\Shoppingcart\Facades\Cart::instance('cart')->count() }}</span>
          <svg class="w-6 h-6 lg:w-5 lg:h-5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M7.08301 11.875C7.08301 13.475 8.39967 14.7917 9.99967 14.7917C11.5997 14.7917 12.9163 13.475 12.9163 11.875"
              stroke="currentColor" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M7.34186 1.66602L4.3252 4.69102" stroke="currentColor" stroke-miterlimit="10"
                  stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M12.6582 1.66602L15.6749 4.69102" stroke="currentColor" stroke-miterlimit="10"
                  stroke-linecap="round" stroke-linejoin="round"/>
            <path
              d="M1.66699 6.54167C1.66699 5 2.49199 4.875 3.51699 4.875H16.4837C17.5087 4.875 18.3337 5 18.3337 6.54167C18.3337 8.33333 17.5087 8.20833 16.4837 8.20833H3.51699C2.49199 8.20833 1.66699 8.33333 1.66699 6.54167Z"
              stroke="currentColor"/>
            <path
              d="M2.91699 8.33398L4.09199 15.534C4.35866 17.1507 5.00033 18.334 7.38366 18.334H12.4087C15.0003 18.334 15.3837 17.2007 15.6837 15.634L17.0837 8.33398"
              stroke="currentColor" stroke-linecap="round"/>
          </svg>
        </a>
        @auth
          <a href="{{ route('cabinet.order.index') }}" class="shrink-0">
            <svg class="w-6 h-6 lg:w-5 lg:h-5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                d="M15.1171 18.0176C14.3838 18.2343 13.5171 18.3343 12.5005 18.3343H7.50045C6.48379 18.3343 5.61712 18.2343 4.88379 18.0176C5.06712 15.8509 7.29212 14.1426 10.0005 14.1426C12.7088 14.1426 14.9338 15.8509 15.1171 18.0176Z"
                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
              <path
                d="M12.5003 1.66602H7.50033C3.33366 1.66602 1.66699 3.33268 1.66699 7.49935V12.4993C1.66699 15.6493 2.61699 17.3743 4.88366 18.016C5.06699 15.8493 7.29199 14.141 10.0003 14.141C12.7087 14.141 14.9337 15.8493 15.117 18.016C17.3837 17.3743 18.3337 15.6493 18.3337 12.4993V7.49935C18.3337 3.33268 16.667 1.66602 12.5003 1.66602ZM10.0003 11.8077C8.35033 11.8077 7.01699 10.466 7.01699 8.81603C7.01699 7.16603 8.35033 5.83268 10.0003 5.83268C11.6503 5.83268 12.9837 7.16603 12.9837 8.81603C12.9837 10.466 11.6503 11.8077 10.0003 11.8077Z"
                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
              <path
                d="M12.9833 8.81733C12.9833 10.4673 11.6499 11.809 9.99994 11.809C8.34994 11.809 7.0166 10.4673 7.0166 8.81733C7.0166 7.16733 8.34994 5.83398 9.99994 5.83398C11.6499 5.83398 12.9833 7.16733 12.9833 8.81733Z"
                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </a>
        @else
          <a href="javascript:;" data-src="#authForm" data-fancybox-no-close-btn class="shrink-0">
            <svg class="w-6 h-6 lg:w-5 lg:h-5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                d="M15.1171 18.0176C14.3838 18.2343 13.5171 18.3343 12.5005 18.3343H7.50045C6.48379 18.3343 5.61712 18.2343 4.88379 18.0176C5.06712 15.8509 7.29212 14.1426 10.0005 14.1426C12.7088 14.1426 14.9338 15.8509 15.1171 18.0176Z"
                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
              <path
                d="M12.5003 1.66602H7.50033C3.33366 1.66602 1.66699 3.33268 1.66699 7.49935V12.4993C1.66699 15.6493 2.61699 17.3743 4.88366 18.016C5.06699 15.8493 7.29199 14.141 10.0003 14.141C12.7087 14.141 14.9337 15.8493 15.117 18.016C17.3837 17.3743 18.3337 15.6493 18.3337 12.4993V7.49935C18.3337 3.33268 16.667 1.66602 12.5003 1.66602ZM10.0003 11.8077C8.35033 11.8077 7.01699 10.466 7.01699 8.81603C7.01699 7.16603 8.35033 5.83268 10.0003 5.83268C11.6503 5.83268 12.9837 7.16603 12.9837 8.81603C12.9837 10.466 11.6503 11.8077 10.0003 11.8077Z"
                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
              <path
                d="M12.9833 8.81733C12.9833 10.4673 11.6499 11.809 9.99994 11.809C8.34994 11.809 7.0166 10.4673 7.0166 8.81733C7.0166 7.16733 8.34994 5.83398 9.99994 5.83398C11.6499 5.83398 12.9833 7.16733 12.9833 8.81733Z"
                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </a>
        @endauth
      </div>
    </div>
  </div>
  <div class="justify-between items-center mt-12 space-x-8 hidden lg:flex">
    <div class="md:w-1/4">
      <form action="{{ route('product.catalog') }}"
            class="border {{ $borderColor }} inline-flex items-center px-4 py-[5px] max-w-[248px]"
            data-da=".mobile-search,1,1023" id="search-form">
        <input type="text" name="search" id="search-field" placeholder="Поиск"
               class="h-5 w-full text-xl border-0 p-0 {{ $placeholderColor }} bg-transparent focus:ring-0 lh-none d-subtitle-2"
               required value="{{ request()->get('search') }}">
        <button type="button" data-field="search-field" class="shrink-0 label-button">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M7.66634 14.0007C11.1641 14.0007 13.9997 11.1651 13.9997 7.66732C13.9997 4.16951 11.1641 1.33398 7.66634 1.33398C4.16854 1.33398 1.33301 4.16951 1.33301 7.66732C1.33301 11.1651 4.16854 14.0007 7.66634 14.0007Z"
              stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M14.6663 14.6673L13.333 13.334" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                  stroke-linejoin="round"/>
          </svg>
        </button>
      </form>

    </div>
    <div>
      <nav id="main-menu" class="flex space-x-12 d-subtitle-2 m-text-body">
        <a href="{{ route('page.index') }}" class="whitespace-nowrap">Главная</a>

        <div class="relative dropdown">
          <a href="" class="whitespace-nowrap flex items-center dropdown-toggle">
            <span>Категории</span>
            <svg class="ml-[6px]" width="16" height="16" viewBox="0 0 16 16" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
              <g filter="url(#filter0_b_1918_1487)">
                <path d="M3 5.96202L8 10.916L13 5.96202L12.1125 5.08268L8 9.15735L3.8875 5.08268L3 5.96202Z"
                      fill="currentColor"/>
              </g>
              <defs>
                <filter id="filter0_b_1918_1487" x="-32" y="-32" width="80" height="80" filterUnits="userSpaceOnUse"
                        color-interpolation-filters="sRGB">
                  <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                  <feGaussianBlur in="BackgroundImageFix" stdDeviation="16"/>
                  <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_1918_1487"/>
                  <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_1918_1487" result="shape"/>
                </filter>
              </defs>
            </svg>
          </a>
          <div class="dropdown-menu absolute left-0 mt-2 w-48 bg-white shadow-lg z-40 text-myDark">

            {{--            <a href="{{ config('app.url') }}/catalog?preorder=on" class="block lg:px-4 py-2">Оформить предзаказ</a>--}}

            <a href="{{ config('app.url') }}/catalog?in_stock=on" class="block lg:px-4 py-2">В наличии</a>
            @foreach(\App\Models\Category::catalog()->get() as $category)
              <a href="{{ route('catalog.category', $category->slug) }}"
                 class="block lg:px-4 py-2">{{ $category->title }}</a>
            @endforeach
            @if(!getSettings('promo20'))
              <a href="{{ route('product.vouchers') }}" class="block lg:px-4 py-2">Подарочные сертификаты</a>
            @endif
            <a href="{{ config('app.url') }}/catalog" class="block lg:px-4 py-2">Смотреть все</a>
          </div>
        </div>

        <a href="{{ route('product.catalog') }}" class="">Каталог</a>

        {{--        <div class="relative dropdown">--}}
        {{--          <a href="" class="whitespace-nowrap flex items-center dropdown-toggle"><span>О нас</span> <img src="{{ asset('img/icons/arrow-bottom.svg') }}" alt="Категорииs" class="ml-[6px]"></a>--}}
        {{--          <div class="dropdown-menu absolute left-0 mt-2 w-48 bg-white shadow-lg z-10">--}}
        {{--            <a href="#" class="block px-4 py-2">История</a>--}}
        {{--            <a href="#" class="block px-4 py-2">Команда</a>--}}
        {{--            <a href="#" class="block px-4 py-2">Миссия</a>--}}
        {{--          </div>--}}
        {{--        </div>--}}

        <a href="{{ route('page.about') }}" class="whitespace-nowrap">О нас</a>
        <a href="{{ route('product.presents') }}" class="whitespace-nowrap">Наши презенты</a>
        <a href="{{ route('page.delivery_and_payment') }}" class="whitespace-nowrap">Доставка и оплата</a>
        @auth
          <div class="relative dropdown hidden">
            <a href="" class="whitespace-nowrap flex items-center dropdown-toggle"><span>Личный кабинет</span>
              <svg class="ml-[6px]" width="16" height="16" viewBox="0 0 16 16" fill="none"
                   xmlns="http://www.w3.org/2000/svg">
                <g filter="url(#filter0_b_1918_1487)">
                  <path d="M3 5.96202L8 10.916L13 5.96202L12.1125 5.08268L8 9.15735L3.8875 5.08268L3 5.96202Z"
                        fill="currentColor"/>
                </g>
                <defs>
                  <filter id="filter0_b_1918_1487" x="-32" y="-32" width="80" height="80" filterUnits="userSpaceOnUse"
                          color-interpolation-filters="sRGB">
                    <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                    <feGaussianBlur in="BackgroundImageFix" stdDeviation="16"/>
                    <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_1918_1487"/>
                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_1918_1487" result="shape"/>
                  </filter>
                </defs>
              </svg>
            </a>
            <div class="dropdown-menu absolute left-0 mt-2 w-48 bg-white shadow-lg z-10">
              @if(auth()->user()->hasPermissionTo('Доступ к админпанели'))
                <a href="{{ route('admin.page.index') }}" class="block lg:px-4 py-2">Админпанель</a>
              @endif

              <a href="http://t.me/dermatolog_lm_bot" target="_blank" class="block lg:px-4 py-2">Консультация
                врача-дерматолога</a>
              <a href="{{ route('cabinet.order.index') }}" class="block lg:px-4 py-2">Мои заказы</a>
              <a href="{{ route('cabinet.profile.index') }}" class="block lg:px-4 py-2">Мои данные</a>
              @if(auth()->user()->promocodes()->exists())
                <a href="{{ route('cabinet.discounts') }}" class="block lg:px-4 py-2">Мои скидки</a>
              @endif
              <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();"
                 class="block lg:px-4 py-2">Выход</a>

              <form id="logout-form-header" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
              </form>
            </div>
          </div>
        @endauth
      </nav>
    </div>
    <div class="md:w-1/4 text-right">
      <div class="flex justify-end space-x-4 sm:space-x-6">
        <a href="{{ route('order.index') }}" class="shrink-0 cart-icon relative inline-block">
          <span
            class="cart-counter font-infant_font">{{ \Gloudemans\Shoppingcart\Facades\Cart::instance('cart')->count() }}</span>
          <svg class="w-6 h-6 lg:w-5 lg:h-5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M7.08301 11.875C7.08301 13.475 8.39967 14.7917 9.99967 14.7917C11.5997 14.7917 12.9163 13.475 12.9163 11.875"
              stroke="currentColor" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M7.34186 1.66602L4.3252 4.69102" stroke="currentColor" stroke-miterlimit="10"
                  stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M12.6582 1.66602L15.6749 4.69102" stroke="currentColor" stroke-miterlimit="10"
                  stroke-linecap="round" stroke-linejoin="round"/>
            <path
              d="M1.66699 6.54167C1.66699 5 2.49199 4.875 3.51699 4.875H16.4837C17.5087 4.875 18.3337 5 18.3337 6.54167C18.3337 8.33333 17.5087 8.20833 16.4837 8.20833H3.51699C2.49199 8.20833 1.66699 8.33333 1.66699 6.54167Z"
              stroke="currentColor"/>
            <path
              d="M2.91699 8.33398L4.09199 15.534C4.35866 17.1507 5.00033 18.334 7.38366 18.334H12.4087C15.0003 18.334 15.3837 17.2007 15.6837 15.634L17.0837 8.33398"
              stroke="currentColor" stroke-linecap="round"/>
          </svg>
        </a>
        @auth
          <a href="{{ route('cabinet.order.index') }}" class="shrink-0">
            <svg class="w-6 h-6 lg:w-5 lg:h-5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                d="M15.1171 18.0176C14.3838 18.2343 13.5171 18.3343 12.5005 18.3343H7.50045C6.48379 18.3343 5.61712 18.2343 4.88379 18.0176C5.06712 15.8509 7.29212 14.1426 10.0005 14.1426C12.7088 14.1426 14.9338 15.8509 15.1171 18.0176Z"
                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
              <path
                d="M12.5003 1.66602H7.50033C3.33366 1.66602 1.66699 3.33268 1.66699 7.49935V12.4993C1.66699 15.6493 2.61699 17.3743 4.88366 18.016C5.06699 15.8493 7.29199 14.141 10.0003 14.141C12.7087 14.141 14.9337 15.8493 15.117 18.016C17.3837 17.3743 18.3337 15.6493 18.3337 12.4993V7.49935C18.3337 3.33268 16.667 1.66602 12.5003 1.66602ZM10.0003 11.8077C8.35033 11.8077 7.01699 10.466 7.01699 8.81603C7.01699 7.16603 8.35033 5.83268 10.0003 5.83268C11.6503 5.83268 12.9837 7.16603 12.9837 8.81603C12.9837 10.466 11.6503 11.8077 10.0003 11.8077Z"
                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
              <path
                d="M12.9833 8.81733C12.9833 10.4673 11.6499 11.809 9.99994 11.809C8.34994 11.809 7.0166 10.4673 7.0166 8.81733C7.0166 7.16733 8.34994 5.83398 9.99994 5.83398C11.6499 5.83398 12.9833 7.16733 12.9833 8.81733Z"
                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </a>
        @else
          <a href="javascript:;" data-src="#authForm" data-fancybox-no-close-btn class="shrink-0">
            <svg class="w-6 h-6 lg:w-5 lg:h-5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                d="M15.1171 18.0176C14.3838 18.2343 13.5171 18.3343 12.5005 18.3343H7.50045C6.48379 18.3343 5.61712 18.2343 4.88379 18.0176C5.06712 15.8509 7.29212 14.1426 10.0005 14.1426C12.7088 14.1426 14.9338 15.8509 15.1171 18.0176Z"
                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
              <path
                d="M12.5003 1.66602H7.50033C3.33366 1.66602 1.66699 3.33268 1.66699 7.49935V12.4993C1.66699 15.6493 2.61699 17.3743 4.88366 18.016C5.06699 15.8493 7.29199 14.141 10.0003 14.141C12.7087 14.141 14.9337 15.8493 15.117 18.016C17.3837 17.3743 18.3337 15.6493 18.3337 12.4993V7.49935C18.3337 3.33268 16.667 1.66602 12.5003 1.66602ZM10.0003 11.8077C8.35033 11.8077 7.01699 10.466 7.01699 8.81603C7.01699 7.16603 8.35033 5.83268 10.0003 5.83268C11.6503 5.83268 12.9837 7.16603 12.9837 8.81603C12.9837 10.466 11.6503 11.8077 10.0003 11.8077Z"
                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
              <path
                d="M12.9833 8.81733C12.9833 10.4673 11.6499 11.809 9.99994 11.809C8.34994 11.809 7.0166 10.4673 7.0166 8.81733C7.0166 7.16733 8.34994 5.83398 9.99994 5.83398C11.6499 5.83398 12.9833 7.16733 12.9833 8.81733Z"
                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </a>
          <div class="hidden">
            <x-public.popup id="authForm">
              <x-slot name="icon">
                <h4 class="d-headline-4 m-headline-3 leading-none lh-outline-none">Авторизация</h4>
              </x-slot>
              <form method="POST" action="{{ route('login') }}">
                @csrf
                <input type="hidden" name="redirect" value="0">
                <div class="mb-6">
                  <x-public.order-input type="text" id="email" name="email" placeholder="Email" value="" required
                                        autofocus autocomplete="username"/>
                </div>
                <div class="mb-6">
                  <x-public.order-input type="password" id="password" name="password" placeholder="Пароль" required
                                        autocomplete="current-password"/>
                </div>
                <div class="text-center">
                  <x-public.green-button type="submit"
                                         class="md:w-full max-w-[193px] md:max-w-[223px] lg:max-w-[253px] xl:max-w-[283px] md:text-2xl md:h-14">
                    Войти
                  </x-public.green-button>
                </div>

                <div class="mt-6 text-center">
                  <a href="{{ route('password.request') }}">Забыли пароль?</a>
                </div>
                <div class="mt-2 text-center">
                  <a href="{{ route('register') }}">Зарегистрироваться</a>
                </div>
              </form>
            </x-public.popup>
            {{--            <div id="authForm" class="!px-4 !py-6 sm:!py-[60px] sm:!px-6 w-full max-w-[547px]" style="display: none">--}}
            {{--              <div class="mb-12 flex items-center justify-between">--}}
            {{--                <h4 class="d-headline-4 m-headline-3 leading-none lh-outline-none">Авторизация</h4>--}}
            {{--                <button class="outline-none" onclick="Fancybox.close()" tabindex="-1"><img src="{{ asset('img/icons/close-circle.svg') }}" alt="" class="w-6 h-6"></button>--}}
            {{--              </div>--}}
            {{--              <form method="POST" action="{{ route('login') }}">--}}
            {{--                @csrf--}}
            {{--                <input type="hidden" name="redirect" value="0">--}}
            {{--                <div class="mb-6">--}}
            {{--                  <x-public.order-input type="text" id="email" name="email" placeholder="Email" value="" required autofocus autocomplete="username"/>--}}
            {{--                </div>--}}
            {{--                <div class="mb-6">--}}
            {{--                  <x-public.order-input type="password" id="password" name="password" placeholder="Пароль" required autocomplete="current-password"/>--}}
            {{--                </div>--}}
            {{--                <div class="text-center">--}}
            {{--                  <x-public.primary-button type="submit" class="md:w-full max-w-[193px] md:max-w-[223px] lg:max-w-[253px] xl:max-w-[283px] md:text-2xl md:h-14">--}}
            {{--                    Войти--}}
            {{--                  </x-public.primary-button>--}}
            {{--                </div>--}}

            {{--                <div class="mt-6 text-center">--}}
            {{--                  <a href="{{ route('password.request') }}">Забыли пароль?</a>--}}
            {{--                </div>--}}
            {{--                <div class="mt-2 text-center">--}}
            {{--                  <a href="{{ route('register') }}">Зарегистрироваться</a>--}}
            {{--                </div>--}}
            {{--              </form>--}}
            {{--            </div>--}}
          </div>
        @endauth
      </div>
    </div>
  </div>
</header>
@if(!auth()->check())
  <div id="dermatologists-modal" class="!px-4 !py-6 sm:!py-[60px] sm:!px-6 w-full max-w-[547px]" style="display: none">
    <div class="flex items-start justify-between">
      <h4 class="d-headline-4 m-headline-3 leading-none lh-outline-none">Чтобы получить консультацию дерматолога войдите
        в личный кабинет или зарегистрируйтесь.</h4>
      <button class="outline-none shrink-0" onclick="Fancybox.close()" tabindex="-1">
        <svg class="w-6 h-6" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path
            d="M10.0003 18.3327C14.5837 18.3327 18.3337 14.5827 18.3337 9.99935C18.3337 5.41602 14.5837 1.66602 10.0003 1.66602C5.41699 1.66602 1.66699 5.41602 1.66699 9.99935C1.66699 14.5827 5.41699 18.3327 10.0003 18.3327Z"
            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M7.6416 12.3592L12.3583 7.64258" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                stroke-linejoin="round"/>
          <path d="M12.3583 12.3592L7.6416 7.64258" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                stroke-linejoin="round"/>
        </svg>
      </button>
    </div>
    <div class="p-6 d-text-body m-text-body text-center">
      <a href="javascript:;" data-fancybox-no-close-btn data-src="#authForm"
         class="w-full md:text-2xl md:h-14 h-11 inline-flex items-center justify-center px-3 md:px-4 px-7 border border-black text-xl leading-none font-medium">
        Войти в личный кабинет
      </a>
    </div>
  </div>
@endif
<style>
  .dropdown-menu {
    display: none;
  }
</style>
