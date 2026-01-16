@section('title', $seo['title'] ?? config('app.name'))
@section('title', $seo['title'] ?? config('app.name'))
<x-app-layout>
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-4 md:py-12">
    <div>
      <h1 class="flex-1 d-headline-1 text-2xl uppercase text-center">БЛАГОДАРИМ ЗА ПОКУПКУ</h1>
    </div>
  </div>
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-3 sm:py-4 md:py-12" style="background: url('{{ asset('img/form-bg.jpg') }}');background-size: cover;">
    <div class="pb-4">
      <div class="text-lg md:text-[32px] leading-normal uppercase text-center mb-5">ИСПОЛНЯЕМ ЗАВЕТНЫЕ<br class="inline sm:hidden"/> ЖЕЛАНИЯ 3-Х ПОКУПАТЕЛЕЙ<br/>
        <span class="font-bold">В РОЖДЕСТВЕНСКУЮ НОЧЬ!</span>
      </div>
      <div class="text-center text-sm md:text-2xl">В личном кабинете вас ждёт <span class="font-bold">анкета</span>. <br class="sm:hidden"/>Опишите в ней ту самую <span class="font-bold">заветную мечту</span>.<br/><br/>
        <span class="font-bold">В Рождественскую ночь</span> мы объявим имена 3-х участников, <br class="sm:hidden"/>для которых<br class="hidden sm:inline"/>
        2025 год начнётся с большого и настоящего чуда!
      </div>

      <div class="relative z-10">
        <div class="text-center">
          <img src="{{ asset('img/form-star.png') }}" alt="" class="bold mx-auto w-[76px] sm:w-[125px]">
        </div>
      </div>
      <div class="text-center mt-4">
        <a href="{{ route('cabinet.order.index') }}" class="uppercase h-11 inline-flex items-center justify-center md:px-4 px-7 bg-formGreen text-white text-xl leading-none font-medium">
          ЛИЧНЫЙ КАБИНЕТ
        </a>
      </div>
    </div>
  </div>
{{--  <script>--}}
{{--    window.setCookie('goldticketShown', 'true', 1);--}}
{{--  </script>--}}
</x-app-layout>
