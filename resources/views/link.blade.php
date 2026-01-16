<!doctype html>
<html lang="ru">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300..700;1,300..700&family=Cormorant+Infant:ital,wght@0,300..700;1,300..700&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet"
  />
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"
  />
  <script src="{{ asset('libraries/inputmask.min.js') }}"></script>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <title>
    КОСМЕЦЕВТИКА 3-ПОКОЛЕНИЯ, БОЛЕЕ 80% АКТИВНЫХ КОМПОНЕНТОВ В СОСТАВАХ
  </title>
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
      @if(getSettings('puzzlesStatus'))
      window.puzzles = true;
    @endif
      @if(getSettings('goldTicket'))
      window.goldTicket = true;
    @else
      window.goldTicket = false;
    @endif
  </script>
  <style type="text/css">
    .swiper-horizontal>.swiper-pagination-bullets, .swiper-pagination-bullets.swiper-pagination-horizontal, .swiper-pagination-custom, .swiper-pagination-fraction {
      height: auto;
      bottom: auto;
    }
    .swiper-pagination {
      position: static;
      top: auto;
      bottom: auto;
    }
    .swiper-pagination-bullet {
      position: relative;
      width: 0.5rem;
      height: 0.5rem;
      border-radius: 9999px;
      background-color: #acc2bd;
    }
    .swiper-pagination-bullet-active {
      background-color: #cdd7d2;
    }

    /* Small screen (sm) */
    @media (min-width: 640px) {
      .swiper-pagination-bullet {
        width: 0.75rem;
        height: 0.75rem;
      }
    }

    /* Medium screen (md) */
    @media (min-width: 768px) {
      .swiper-pagination-bullet {
        width: 1.25rem;
        height: 1.25rem;
      }
    }
  </style>
</head>
<body class="font-roboto_font">
<header
  class="flex flex-col gap-[18px] bg-taplinkGreen bg-opacity-15 px-4 pb-[21px] pt-3 md:gap-11 md:pb-[50px] md:pt-[27px] xl:gap-14 xl:pb-[55px]"
>
  <div class="mx-auto flex gap-[5px] md:gap-[7px] xl:gap-[10px]">
    <a
      href="https://www.wildberries.ru/brands/le-mousse?utm_source=TaplinkBrand&utm_medium=cpc&utm_campaign=1366629-id-brandLM"
    ><img
        class="mx-auto block w-full max-w-[30px] md:max-w-[71px] xl:max-w-[55px]"
        src="{{ asset('img/link/logo-wb.png') }}"
        alt="Логотип WB"
      /></a>
    <a href="https://ozon.onelink.me/SNMZ/pbox4958"
    ><img
        class="mx-auto block w-full max-w-[30px] md:max-w-[71px] xl:max-w-[55px]"
        src="{{ asset('img/link/logo-ozon.png') }}"
        alt="Логотип Ozon"
      /></a>
  </div>
  <img
    class="mx-auto block w-full max-w-[162px] md:max-w-[384px]"
    src="{{ asset('img/link/le-mousse-brand.png') }}"
    alt="Логотип Le Mousse"
  />
</header>
<main>
  <section
    class="mx-auto grid grid-cols-2 items-center gap-x-[14px] px-3 py-4 text-[15px] font-light leading-1.2 text-[#242421] md:gap-[33px] md:px-6 md:py-10 md:text-[36px] xl:max-w-[800px]"
  >
    <img
      class="col-start-1 col-end-2 row-start-1 row-end-3 mx-auto block w-full max-w-[410px]"
      src="{{ asset('img/link/photo-product-1.png') }}"
      alt="Косметическая продукция Le Mousse"
    />
    <h3 class="col-start-2 col-end-3 row-start-1 row-end-2">
      КОСМЕЦЕВТИКА <br />
      <span class="font-normal">3-ГО ПОКОЛЕНИЯ</span>
    </h3>
    <h3 class="col-start-2 col-end-3 row-start-2 row-end-3">
      БОЛЕЕ&nbsp;80% <br />
      <span class="font-normal">АКТИВНЫХ КОМПОНЕНТОВ</span>
      В&nbsp;СОСТАВАХ
    </h3>
  </section>

  <section class="bg-taplinkGreen bg-opacity-15">
    <article
      class="mx-auto px-3 py-[14px] sm:px-5 md:px-[30px] md:py-[32px] md:pb-[37px] xl:max-w-[800px]"
    >
      <h1
        class="font-main-font text-[23px] font-light uppercase tracking-[0.01em] sm:text-32 md:text-[56px]"
      >
        LE MOUSSE
      </h1>
      <p
        class="text-xs font-light leading-1.1 tracking-[0.03em] sm:text-xl md:text-[29px]"
      >
        — первый в&nbsp;мире бренд, который
        <span class="font-normal"
        >применяет запатентованную технологию ввода инертного газа
              в&nbsp;косметику</span
        >
        для&nbsp;повышения биодоступности компонентов.
      </p>
    </article>
  </section>
  <section
    class="mx-auto px-3 pb-[18px] pt-[14px] sm:px-5 sm:pb-[24px] sm:pt-7 md:px-7 md:pb-[44px] md:pt-[38px] xl:max-w-[800px]"
  >
    <h2
      class="font-main-font text-[23px] font-light uppercase tracking-[0.01em] sm:text-32 md:mb-3 md:text-[56px]"
    >
      почему мы?
    </h2>
    <ul
      class="flex flex-col gap-[7px] font-light leading-[1.05] sm:gap-4 md:gap-[18px]"
    >
      <li>
        <p
          class="text-[13px] text-taplinkLimitterGreen sm:text-[20px] md:text-[32px]"
        >
          01
        </p>
        <p class="text-[12px] sm:text-[20px] md:text-[29px]">
          передовые рецептуры средств созданы
          <span class="whitespace-nowrap">химиками-технологами</span>
          и&nbsp;врачами-дерматологами
        </p>
        <div
          class="mx-auto mt-[7px] h-[0.42px] w-full bg-taplinkLimitterGreen sm:mt-[14px] md:mt-[20px]"
        ></div>
      </li>
      <li>
        <p
          class="text-[13px] text-taplinkLimitterGreen sm:text-[20px] md:text-[32px]"
        >
          02
        </p>
        <p class="text-[12px] sm:text-[20px] md:text-[29px]">
          производство в&nbsp;собственной лаборатории в&nbsp;России
        </p>
        <div
          class="mx-auto mt-[7px] h-[0.42px] w-full bg-taplinkLimitterGreen sm:mt-[14px] md:mt-[20px]"
        ></div>
      </li>
      <li>
        <p
          class="text-[13px] text-taplinkLimitterGreen sm:text-[20px] md:text-[32px]"
        >
          03
        </p>
        <p class="text-[12px] sm:text-[20px] md:text-[29px]">
          трёхэтапный контроль качества продукции
        </p>
        <div
          class="mx-auto mt-[7px] h-[0.42px] w-full bg-taplinkLimitterGreen sm:mt-[14px] md:mt-[20px]"
        ></div>
      </li>
      <li>
        <p
          class="text-[13px] text-taplinkLimitterGreen sm:text-[20px] md:text-[32px]"
        >
          04
        </p>
        <p class="text-[12px] sm:text-[20px] md:text-[29px]">
          средства способствуют восстановлению собственных ресурсов кожи
        </p>
        <div
          class="mx-auto mt-[7px] h-[0.42px] w-full bg-taplinkLimitterGreen sm:mt-[14px] md:mt-[20px]"
        ></div>
      </li>
      <li>
        <p
          class="text-[13px] text-taplinkLimitterGreen sm:text-[20px] md:text-[32px]"
        >
          05
        </p>
        <p class="text-[12px] sm:text-[20px] md:text-[28px]">
          более 170&nbsp;000 девушек убедились <br />
          в&nbsp;высоком качестве и&nbsp;эффективности нашей продукции
        </p>
        <div
          class="mx-auto mt-[7px] h-[0.42px] w-full bg-taplinkLimitterGreen sm:mt-[14px] md:mt-[20px]"
        ></div>
      </li>
    </ul>
  </section>

  <section
    class="mx-auto bg-taplinkGreen bg-opacity-15 px-[14px] pb-7 pt-5 leading-[1.05] sm:px-6 sm:pb-10 sm:pt-7 md:px-[39px] md:pb-16 md:pt-[50px] xl:max-w-[1500px] xl:pb-[70px]"
  >
    <h2
      class="mb-4 text-center font-main-font text-[23px] font-light uppercase tracking-[0.01em] sm:mb-6 sm:text-32 md:mb-10 md:text-[56px]"
    >
      ГДЕ ЗАКАЗАТЬ?
    </h2>
    <ul
      class="mb-7 flex flex-col gap-[11px] sm:mb-12 sm:gap-5 md:mb-[67px] md:gap-[26px]"
    >
      <li>
        <a
          class="flex items-center justify-between bg-taplinkGreen bg-opacity-[0.36] px-[26px] py-[10px] transition-colors duration-300 ease-linear hover:bg-taplinkGreen4 sm:px-[40px] sm:py-[16px] md:px-[63px] md:py-[23px]"
          href="https://www.wildberries.ru/brands/le-mousse?utm_source=TaplinkBrand&utm_medium=cpc&utm_campaign=1366629-id-brandLM"
        >
          <img
            class="block w-full max-w-[30px] sm:max-w-11 md:max-w-[74px]"
            src="{{ asset('img/link/logo-wb.png') }}"
            alt="Логотип wildberries"
          />
          <p
            class="text-center text-[15px] font-light uppercase leading-[1.05] tracking-[0.03em] text-[#242421] sm:text-22 md:text-[36px]"
          >
            wildberries
          </p>
          <div
            class="w-full max-w-[30px] sm:max-w-11 md:max-w-[74px]"
          ></div>
        </a>
      </li>
      <li>
        <a
          class="flex items-center justify-between bg-taplinkGreen bg-opacity-[0.36] px-[26px] py-[10px] transition-colors duration-300 ease-linear hover:bg-taplinkGreen4 sm:px-[40px] sm:py-[16px] md:px-[63px] md:py-[23px]"
          href="https://ozon.onelink.me/SNMZ/pbox4958"
        >
          <img
            class="block w-full max-w-[30px] sm:max-w-11 md:max-w-[74px]"
            src="{{ asset('img/link/logo-ozon.png') }}"
            alt="Логотип ozon"
          />
          <p
            class="text-center text-[15px] font-light uppercase leading-[1.05] tracking-[0.03em] text-[#242421] sm:text-22 md:text-[36px]"
          >
            ozon
          </p>
          <div
            class="w-full max-w-[30px] sm:max-w-11 md:max-w-[74px]"
          ></div>
        </a>
      </li>
      <li>
        <a
          class="flex items-center justify-between bg-taplinkGreen bg-opacity-[0.36] px-[26px] py-[10px] transition-colors duration-300 ease-linear hover:bg-taplinkGreen4 sm:px-[40px] sm:py-[16px] md:px-[63px] md:py-[23px]"
          href="https://www.letu.ru/brand/le-mousse-by-nechaeva-olga?utm_source=website&utm_medium=integration&utm_content=&utm_term=lemousse&utm_campaign=cp_vendor_Lemousse_media_new_march_24&srcid=cp_vendor_Lemousse_media_new_march_24"
        >
          <img
            class="block w-full max-w-[30px] sm:max-w-11 md:max-w-[74px]"
            src="{{ asset('img/link/logo-letual.png') }}"
            alt="Логотип ЛЭТУАЛЬ"
          />
          <p
            class="text-center text-[15px] font-light uppercase leading-[1.05] tracking-[0.03em] text-[#242421] sm:text-22 md:text-[36px]"
          >
            ЛЭТУАЛЬ
          </p>
          <div
            class="w-full max-w-[30px] sm:max-w-11 md:max-w-[74px]"
          ></div>
        </a>
      </li>
      <li>
        <a
          class="flex items-center justify-between bg-taplinkGreen bg-opacity-[0.36] px-[26px] py-[10px] transition-colors duration-300 ease-linear hover:bg-taplinkGreen4 sm:px-[40px] sm:py-[16px] md:px-[63px] md:py-[23px]"
          href="https://goldapple.ru/brands/le-mousse-by-nechaeva-olga"
        >
          <img
            class="block w-full max-w-[30px] sm:max-w-11 md:max-w-[74px]"
            src="{{ asset('img/link/logo-zolot-yabloko.png') }}"
            alt="Логотип Золотое Яблоко"
          />
          <p
            class="text-center text-[15px] font-light uppercase leading-[1.05] tracking-[0.03em] text-[#242421] sm:text-22 md:text-[36px]"
          >
            золотое яблоко
          </p>
          <div
            class="w-full max-w-[30px] sm:max-w-11 md:max-w-[74px]"
          ></div>
        </a>
      </li>
      <li>
        <a
          class="flex items-center justify-between bg-taplinkGreen bg-opacity-[0.36] px-[26px] py-[10px] transition-colors duration-300 ease-linear hover:bg-taplinkGreen4 sm:px-[40px] sm:py-[16px] md:px-[63px] md:py-[23px]"
          href="https://lemousse.shop/r/taplink"
        >
          <img
            class="block w-full max-w-[30px] sm:max-w-11 md:max-w-[74px]"
            src="{{ asset('img/link/logo-lm.png') }}"
            alt="Логотип сайта LE MOUSSE"
          />
          <p
            class="text-center text-[15px] font-light uppercase leading-[1.05] tracking-[0.03em] text-[#242421] sm:text-22 md:text-[36px]"
          >
            сайт LE&nbsp;MOUSSE
          </p>
          <div
            class="w-full max-w-[30px] sm:max-w-11 md:max-w-[74px]"
          ></div>
        </a>
      </li>
    </ul>

    <h2
      class="mb-3 text-center font-main-font text-[23px] font-light uppercase tracking-[0.01em] sm:mb-5 sm:text-32 md:mb-[26px] md:text-[56px]"
    >
      БЕСТСЕЛЛЕРЫ ПРОДАЖ
    </h2>
    <!-- Slider main container -->
    <div id="bestsallers" class="swiper">
      <!-- Additional required wrapper -->
      <div class="swiper-wrapper mb-2 sm:mb-6 md:mb-[33px]">
        @foreach($products as $product)
          <div class="swiper-slide">
            <div style="box-shadow: 0 0px 2px 0 rgba(0, 0, 0, 0.25)" class="mx-auto max-w-[203px] rounded-md bg-white px-[6px] py-3 sm:max-w-80 sm:px-5 sm:py-[21px] md:max-w-[484px]">
              <img
                class="mx-auto block w-full"
                src="{{ $product['img'] }}"
                alt="Косметический продукт"
              />
              <p
                class="mb-4 text-center text-[11px] font-light leading-[1.05] tracking-[0.03em] sm:mb-6 sm:text-22 md:mb-8 md:text-28"
              >
                {!! $product['name'] !!}
              </p>
              <a
                href="{{ $product['link'] }}"
                class="mx-auto inline-block w-full bg-taplinkGreen2 px-2 py-3 text-center text-[11px] font-light leading-[1.05] tracking-[0.03em] text-[#242421] transition-colors duration-300 ease-linear hover:bg-taplinkGreen4 sm:py-5 sm:text-22 md:py-6 md:text-28"
              >
                ПОДРОБНЕЕ
              </a>
            </div>
          </div>
        @endforeach
      </div>
      <!-- If we need pagination -->
      <div class="swiper-pagination relative flex w-full justify-center gap-1 sm:gap-2 md:gap-[10px]"></div>
    </div>

  </section>
  <section
    class="mx-auto px-[13px] pb-6 pt-[14px] sm:pb-10 md:pb-[60px] md:pt-[33px] xl:max-w-[800px] xl:pt-[37px]"
  >
    <article
      class="border-[0.42px] border-solid border-black px-[9px] pb-[9px] pt-[13px] sm:px-[18px] sm:pb-4 sm:pt-5 md:px-[21px] md:pb-[22px] md:pt-7"
    >
      <p
        class="text-center text-[14px] font-light uppercase leading-1.1 tracking-wide sm:text-22 md:text-[31px]"
      >
        Получите бесплатную
      </p>
      <p
        class="mx-auto mb-[10px] text-center text-[12px] font-light lowercase sm:mb-[18px] sm:text-22 md:mb-[28px] md:max-w-[650px] md:text-[29px]"
      >
        онлайн-консультацию от&nbsp;квалифицированного врача-дерматолога
      </p>
      <a
        href="https://t.me/dermatolog_lm_bot"
        class="mx-auto inline-block w-full bg-taplinkGreen2 px-3 py-[15px] text-center text-[15px] font-light leading-[1.05] tracking-[0.03em] text-[#242421] transition-colors duration-300 ease-linear hover:bg-taplinkGreen4 sm:py-[28px] sm:text-[24px] md:py-[34px] md:text-[36px]"
      >
        ПРОКОНСУЛЬТИРОВАТЬСЯ
      </a>
    </article>
  </section>

  <section class="mx-auto px-[13px] sm:px-5 md:px-8 xl:max-w-[1500px]">
    <div
      class="mx-auto mb-[13px] h-[0.42px] w-full bg-taplinkLimitterGreen xl:max-w-[1100px]"
    ></div>
    <h2
      class="mb-4 text-center font-main-font text-[23px] font-normal uppercase tracking-[0.01em] text-[#242421] sm:text-32 md:text-[56px]"
    >
      Служба заботы
    </h2>

    <ul
      class="mb-[34px] flex flex-col gap-[11px] sm:mb-14 sm:gap-5 md:mb-20 md:gap-[25px]"
    >
      <li>
        <a
          class="flex items-center justify-center bg-taplinkGreen bg-opacity-[0.36] px-[26px] py-[15px] transition-colors duration-300 ease-linear hover:bg-taplinkGreen4 sm:py-6 md:py-[33px]"
          href="https://t.me/lemousse_support_bot"
        >
          <p
            class="text-center text-[15px] font-light uppercase leading-[1.05] tracking-[0.03em] sm:text-22 md:text-[36px]"
          >
            LE&nbsp;MOUSSE
          </p>
        </a>
      </li>
      <li>
        <a
          class="flex items-center justify-center bg-taplinkGreen bg-opacity-[0.36] px-[26px] py-[15px] transition-colors duration-300 ease-linear hover:bg-taplinkGreen4 sm:py-6 md:py-[33px]"
          href="https://t.me/Cosmetic_service_bot"
        >
          <p
            class="max-w-64 text-center text-[15px] font-light uppercase leading-[1.05] tracking-[0.03em] text-[#242421] sm:max-w-[400px] sm:text-22 md:text-[36px]"
          >
            для заказов с маркетплейсов
          </p>
        </a>
      </li>
    </ul>
{{--    <p--}}
{{--      class="mb-6 text-center font-montserrat_font text-[10px] font-light leading-1.2 opacity-50 sm:mb-10 sm:text-22 md:mb-12 md:text-2xl"--}}
{{--    >--}}
{{--      *продукт компании Meta, <br />--}}
{{--      признана экстремистской организацией в&nbsp;России--}}
{{--    </p>--}}
  </section>
</main>
<footer class="mx-auto pb-[100px] md:pb-[150px] xl:pb-[95px]">
  <div
    class="mb-[35px] h-[3px] w-full bg-taplinkGrey3 sm:h-1 md:h-2 xl:px-10"
  ></div>
  <section
    class="text-center font-montserrat_font text-[10px] font-light leading-none text-taplinkGrey4 sm:text-22 md:text-2xl"
  >
    <p>ИП Нечаева Ольга Андреевна</p>
    <p>ИНН 344115294608</p>
    <p>400048, г.&nbsp;Волгоград а/я 558</p>
  </section>
</footer>
<script type="module">
  import Swiper from 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.mjs'

  const swiper = new Swiper('#bestsallers', {
    cssMode: true,
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
  })
</script>
</body>
</html>
