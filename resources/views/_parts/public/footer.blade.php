@if(getSettings('winterMode'))
  @php($bgColor = 'bg-winterGreen')
  @php($textColor = 'text-white')
@else
  @php($bgColor = 'bg-transparent')
  @php($textColor = 'text-myDark')
@endif
<footer class="{{ $bgColor }} {{ $textColor }} space-y-6 md:space-y-8 py-12" @if(getSettings('winterMode')) style="background: url('{{ asset('img/footer-bg.jpg?1') }}');background-size: cover;" @endif>
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16">
    <div class="flex justify-start">
      <div>
        <x-application-logo class="mx-auto w-[200px]"/>
      </div>
    </div>
    <div class="flex">
      <div class="flex-1">
        <div class="mt-6 md:mt-12 space-x-8">
          <nav id="footer-menu" class="md:text-2xl text-xl font-medium">
            <ul class="grid grid-cols-2 md:grid-cols-3 gap-2">
              <li class="w-full">
                <a href="{{ route('page.about') }}" class="block w-full">
                  О нас
                </a>
              </li>
              <li class="w-full">
                <a href="{{ route('page.delivery_and_payment') }}" class="block w-full">
                  Доставка и оплата
                </a>
              </li>
              <li class="w-full">
                <a href="{{ route('page.contacts') }}" class="block w-full">
                  Контакты
                </a>
              </li>
              <li class="w-full">
                <a href="{{ route('page.certificates') }}" class="block w-full">
                  Сертификаты
                </a>
              </li>
              <li class="w-full">
                <a href="https://lemousse-regions.ru/glavnaya_stranitsa" target="_blank" class="block w-full">
                  Наши дилеры
                </a>
              </li>
              <li class="w-full">
                <a href="https://lemousse-regions.ru/dealers_form" target="_blank" class="block w-full">
                  Стать дилером
                </a>
              </li>
            </ul>
          </nav>
        </div>
        <div class="mt-9 md:mt-12 md:text-2xl text-xl lh-none font-medium">
          <div>Поддержка <a href="https://{{ getSettings('tg_support') }}">{{ getSettings('tg_support') }}</a></div>
          <div class="hidden md:block mt-6 text-[10px] sm:text-xs text-myGray">*продукт компании Meta, признана экстремистской организацией в России</div>
        </div>
      </div>
      <div>
        <nav class="hidden md:flex mt-6 md:mt-12 md:text-2xl text-xl md:flex-col">
          <a href="https://www.youtube.com/@Le_mousse1" class="underline hover:no-underline" target="_blank">YouTube</a>
          <a href="https://www.wildberries.ru/brands/le-mousse?utm_source=website&utm_medium=cpc&utm_campaign=1366629-id-brandLM" class="underline hover:no-underline" target="_blank">Wildberries</a>
          <a href="https://ozon.onelink.me/SNMZ/se924c84" class="underline hover:no-underline" target="_blank">Ozon</a>
          <a href="https://www.letu.ru/merchant/189500011?utm_source=website&utm_medium=integration&utm_content=&utm_term=lemousse&utm_campaign=cp_vendor_Lemousse_media_new_march_24&srcid=cp_vendor_Lemousse_media_new_march_24" class="underline hover:no-underline" target="_blank">Лэтуаль</a>
          <a href="https://goldapple.ru/brands/le-mousse-by-nechaeva-olga" class="underline hover:no-underline" target="_blank">Золотое яблоко</a>
          <span><a href="https://instagram.com/le__mousse" class="underline hover:no-underline" target="_blank">Instagram</a>*</span>
          <a href="https://vk.com/le__mousse" class="underline hover:no-underline" target="_blank">VK</a>
        </nav>
      </div>
    </div>
  </div>
  <div>
    <div class="border-t border-t-myGreen mx-2 md:mx-auto mt-6 mt:pb-10"></div>
  </div>
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 flex flex-col space-y-9">
    <div>
      <nav class="flex flex-wrap md:hidden justify-center mt-6 md:mt-12 md:text-2xl text-xl">
        <div class="p-1"><a href="https://www.youtube.com/@Le_mousse1" class="underline hover:no-underline" target="_blank">YouTube</a></div>
          <div class="p-1"><a href="https://www.wildberries.ru/brands/le-mousse?utm_source=website&utm_medium=cpc&utm_campaign=1366629-id-brandLM" class="underline hover:no-underline" target="_blank">Wildberries</a></div>
          <div class="p-1"><a href="https://ozon.onelink.me/SNMZ/se924c84" class="underline hover:no-underline" target="_blank">Ozon</a></div>
          <div class="p-1"><a href="https://www.letu.ru/merchant/189500011?utm_source=website&utm_medium=integration&utm_content=&utm_term=lemousse&utm_campaign=cp_vendor_Lemousse_media_new_march_24&srcid=cp_vendor_Lemousse_media_new_march_24" class="underline hover:no-underline" target="_blank">Лэтуаль</a></div>
          <div class="p-1"><a href="https://goldapple.ru/brands/le-mousse-by-nechaeva-olga" class="underline hover:no-underline" target="_blank">Золотое яблоко</a></div>
          <div class="p-1"><span><a href="https://instagram.com/le__mousse" class="underline hover:no-underline" target="_blank">Instagram</a>*</span></div>
        <div class="p-1"><a href="https://vk.com/le__mousse" class="underline hover:no-underline" target="_blank">VK</a></div>
      </nav>
      <div class="md:hidden mt-2 text-[10px] sm:text-xs text-myGray">*продукт компании Meta, признана экстремистской организацией в России</div>
    </div>
    <div class="md:text-2xl text-xl font-medium opacity-26 ">
      <nav class="lg:space-x-12 flex flex-col lg:flex-row ">
        <a href="{{ route('page', 'oferta') }}">Публичная оферта</a>
        <a href="{{ route('page', 'politika') }}">Политика обработки персональных данных</a>
        <a href="{{ route('page', 'polzovatelskoe_soglashenie') }}">Пользовательское соглашение</a>
        @if(config('happy-coupone.active'))
          <a href="{{ route('page', 'politika_promo') }}">Правила проведения рекламной акции «Счастливый купон»</a>
        @endif
        @if(getSettings('goldTicket'))
          <a href="{{ route('page', 'aktsiya_zolotoy_bilet') }}">Правила проведения рекламной акции «Золотой билет»</a>
        @endif
        <a onclick="window.dispatchEvent(new CustomEvent('open-popup-own'))" class="transition cursor-pointer">Информация об интеллектуальной собственности</a>
      </nav>
      <div class="mt-6 flex flex-col lg:flex-row lg:space-x-12">
        <div>
          ИП Нечаева Ольга Андреевна
        </div>
        <div>
          ОГРНИП <span class="cormorantInfant">320344300076171</span>
        </div>
        <div>
          <div class="cormorantInfant">8 (8442) 51-50-05</div>
        </div>
      </div>
    </div>
  </div>
</footer>
<x-public.popup id="custom-alert">
  <div class="m-text-body d-text-body text-center">
    <div class="mb-8" id="custom-alert__title"></div>
  </div>
</x-public.popup>
<x-popup-own />
<div id="goldticket-alert" class="relative border-none !px-3 !py-3 sm:!px-5 sm:!py-5 w-full !max-w-[340px] sm:!max-w-[547px] !rounded-4xl" style="display: none;background: #F2E2DA">
  <div class="flex items-center sm:items-start justify-between flex-col-reverse sm:flex-row">
    <div class="w-7"></div>
    <div class="goldticket-top">
      <div class="text-center">
        <img src="{{ asset('/img/goldTicketPopup.png') }}" style="max-width: 240px;" class="block mx-auto" alt="">
      </div>
    </div>
    <button class="outline-none" onclick="Fancybox.close()" tabindex="-1">
      <svg width="28" height="28" viewBox="0 0 28 28" fill="none" stroke="currentColor" xmlns="http://www.w3.org/2000/svg">
        <path d="M14 25.6667C20.4167 25.6667 25.6667 20.4167 25.6667 14C25.6667 7.58333 20.4167 2.33333 14 2.33333C7.58333 2.33333 2.33333 7.58333 2.33333 14C2.33333 20.4167 7.58333 25.6667 14 25.6667Z" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M10.6983 17.3017L17.3017 10.6983" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M17.3017 17.3017L10.6983 10.6983" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </button>
  </div>
  <div class="relative z-10 goldticket-bottom">
    <div class="text-center mx-auto" style="max-width: 471px"  data-da=".goldticket-top,first,640">
      <div class="text-[17px] md:text-lg lg:text-2xl xl:text-[26px] mb-3">ПОЛУЧИ СВОЙ <br class="sm:hidden"/>ЗОЛОТОЙ БИЛЕТ
        <br class="hidden sm:inline"/>С ПОДАРКОМ <br class="sm:hidden"/>ЗА ПОКУПКУ СЕТОВ</div>
    </div>
  </div>
  <div class="flex justify-center">
    <a href="{{ route('catalog.category', 'zolotoy_bilet') }}" class="h-14 uppercase inline-flex items-center justify-center px-8 md:px-12 lg:px-16 leading-none rounded-2xl text-center goldticket-button text-2xl font-semibold" style="background: url('{{ asset('img/button-bg.png') }}') no-repeat cover;">
      выбрать сет
    </a>
  </div>
</div>

<div id="cookie_note" class="hidden fixed p-2 bottom-0 right-0 z-50">
  <div class="d-text-body m-text-body border border-px p-2 bg-white flex flex-col md:flex-row md:space-x-4 justify-between">
    <p>Мы используем файлы cookies для улучшения работы сайта. <br/>Оставаясь на нашем сайте, вы соглашаетесь с условиями
      использования файлов cookies.</p>
    <x-public.primary-button class="cookie_accept whitespace-nowrap mt-4">Я согласен</x-public.primary-button>
  </div>
</div>
{{--<div id="prloader" class="bg-white fixed w-full h-full top-0 left-0" style="z-index: 999999"></div>--}}
<script>
  // document.addEventListener('DOMContentLoaded', ()=>{
  //   setTimeout(()=> {
  //     document.getElementById('prloader').remove();
  //   })
  // })
  function setCookie(name, value, days) {
    let expires = "";
    if (days) {
      let date = new Date();
      date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
      expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
  }

  function getCookie(name) {
    let matches = document.cookie.match(new RegExp("(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"));
    return matches ? decodeURIComponent(matches[1]) : undefined;
  }


  function checkCookies() {
    let cookieNote = document.getElementById('cookie_note');
    let cookieBtnAccept = cookieNote.querySelector('.cookie_accept');

    // Если куки cookies_policy нет или она просрочена, то показываем уведомление
    if (!getCookie('cookies_policy')) {
      cookieNote.classList.remove('hidden');
    }
    // При клике на кнопку устанавливаем куку cookies_policy на один год
    cookieBtnAccept.addEventListener('click', function () {
      setCookie('cookies_policy', 'true', 1);
      cookieNote.remove();
    });
  }

  checkCookies();

</script>
<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
  (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
    m[i].l=1*new Date();
    for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
    k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
  (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

  ym(98576494, "init", {
    clickmap:true,
    trackLinks:true,
    accurateTrackBounce:true,
    webvisor:true
  });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/98576494" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
<!-- Top.Mail.Ru counter -->
<script type="text/javascript">
  var _tmr = window._tmr || (window._tmr = []);
  _tmr.push({id: "3581417", type: "pageView", start: (new Date()).getTime()});
  (function (d, w, id) {
    if (d.getElementById(id)) return;
    var ts = d.createElement("script"); ts.type = "text/javascript"; ts.async = true; ts.id = id;
    ts.src = "https://top-fwz1.mail.ru/js/code.js";
    var f = function () {var s = d.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ts, s);};
    if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); }
  })(document, window, "tmr-code");
</script>
<noscript><div><img src="https://top-fwz1.mail.ru/counter?id=3581417;js=na" style="position:absolute;left:-9999px;" alt="Top.Mail.Ru" /></div></noscript>
<!-- /Top.Mail.Ru counter -->
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-EQJT3KGN04"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-EQJT3KGN04');
</script>
