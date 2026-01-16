<x-cabinet-layout>
{{--  <x-slot name="custom_vite">--}}
{{--    @vite(['resources/css/app.css', 'resources/js/helper.js', 'resources/js/app.js'])--}}
{{--  </x-slot>--}}
  <style>
    .fancybox__backdrop {
      background: rgba(28, 44, 35, 0.8); /* Полупрозрачный темный фон */
    }
  </style>
  <x-slot name="style">
    <link rel="stylesheet" href="{{ asset('libraries/@fancyapps/ui/dist/fancybox/fancybox.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  </x-slot>
  <x-slot name="script">
    <script src="{{ asset('libraries/@fancyapps/ui/dist/fancybox/fancybox.umd.js') }}"></script>
  </x-slot>
{{--  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-3 sm:py-4 md:py-12">--}}
{{--    <div>--}}
{{--      <h1 class="flex-1 d-headline-1 m-headline-1 text-center">{{ $seo['title'] }}</h1>--}}
{{--    </div>--}}
{{--  </div>--}}
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-3 sm:py-4 md:py-12" style="background: url('{{ asset('img/form-bg.jpg') }}');background-size: cover;">
    <div x-data="formHandler">
      <div class="text-lg md:text-[32px] leading-normal uppercase text-center">ИСПОЛНЯЕМ ЗАВЕТНЫЕ<br class="inline sm:hidden"/> ЖЕЛАНИЯ 3-Х ПОКУПАТЕЛЕЙ<br/>
        <span class="font-bold">В РОЖДЕСТВЕНСКУЮ НОЧЬ!</span>
      </div>
      <div class="text-center text-sm md:text-2xl">Заполните все поля анкеты и нажмите кнопку «Отправить».<br/>
        7 января мы объявим имена 3-х покупателей, чьи желания исполним!</div>
      <form action="{{ route('cabinet.form.save', $form->slug) }}" @submit.prevent="submitForm($el)" method="POST" style="max-width: 900px;" class="mx-auto">
        @foreach($form->fields as $field)
          <div class="mb-4 md:mb-8">
            <label class="text-sm md:text-2xl" for="{{ translit($field->key) }}">{{ $field->key }}</label>
            <div>
              <input type="text" name="form[{{ $field->id }}]" id="{{ translit($field->key) }}" class="text-sm md:text-2xl bg-transparent border border-formGreen h-[36px] md:h-[60px] w-full px-3 leading-none focus:ring-0 focus:border-black @if($field->id==2) phone-mask @endif" @if($field->id==2) placeholder="+7 (999) 999-99-99" @endif required>
            </div>
          </div>
        @endforeach
          <div class="mb-12 text-center">
            <button type="submit" :disabled="isSubmitting" x-text="isSubmitting ? 'Отправка...' : 'Отправить'" class="uppercase h-11 inline-flex items-center justify-center md:px-4 px-7 bg-formGreen text-white text-xl leading-none font-medium">
              Отправить
            </button>
          </div>
      </form>
    </div>
  </div>
{{--  @if(auth()->id()==1)--}}
{{--    <script>--}}
{{--      document.addEventListener('DOMContentLoaded', ()=>{--}}
{{--        Fancybox.show(--}}
{{--          [{--}}
{{--            src: '#form-success'--}}
{{--          }],--}}
{{--          {--}}
{{--            closeButton: false,--}}
{{--            loop: false,--}}
{{--            touch: false,--}}
{{--            contentClick: false,--}}
{{--            dragToClose: false,--}}
{{--          }--}}
{{--        );--}}
{{--      })--}}
{{--    </script>--}}
{{--  @endif--}}
  <div class="hidden">
    <div id="form-success" class="text-myDark relative border-none !px-3 !py-3 !sm:px-5 !sm:py-5 w-full max-w-[340px] sm:max-w-[547px] !rounded-4xl bg-myGreen " style="display: none;">
      <div class="mb-2 sm:mb-4 flex flex-col-reverse sm:flex-row items-start justify-center sm:justify-between">
        <div class="w-7"></div>
        <div class="mx-auto">
          <h3 class="uppercase text-xl md:text-2xl font-semibold text-center sm:mb-3">Ваше желание принято!</h3>
          <div class="text-base md:text-lg text-center">
            В Рождественскую ночь мы объявим <br class="sm:hidden"/>имена 3-х участников,<br class="hidden sm:inline"/>
            для которых <br class="sm:hidden"/>2025 год начнётся с большого <br class="sm:hidden"/>и настоящего чуда!<br/><br/>
            <span class="font-bold">Все свои покупки вы можете увидеть <br class="sm:hidden"/>в личном кабинете</span>
          </div>
        </div>
        <button class="outline-none ml-auto" onclick="Fancybox.close()" tabindex="-1">
          <svg width="28" height="28" viewBox="0 0 28 28" fill="none" stroke="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path d="M14 25.6667C20.4167 25.6667 25.6667 20.4167 25.6667 14C25.6667 7.58333 20.4167 2.33333 14 2.33333C7.58333 2.33333 2.33333 7.58333 2.33333 14C2.33333 20.4167 7.58333 25.6667 14 25.6667Z" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M10.6983 17.3017L17.3017 10.6983" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M17.3017 17.3017L10.6983 10.6983" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
      </div>

      <div class="relative z-10">
        <div class="text-center">
          <img src="{{ asset('img/form-star.png') }}" alt="" class="bold mx-auto" style="width: 70px;">
        </div>
      </div>
      <div class="text-center mt-4">
        <a href="{{ route('cabinet.order.index') }}" class="uppercase h-11 inline-flex items-center justify-center md:px-4 px-7 bg-formGreen text-white text-xl leading-none font-medium">
          ЛИЧНЫЙ КАБИНЕТ
        </a>
      </div>
    </div>
    <x-public.popup id="custom-alert">
      <div class="m-text-body d-text-body text-center">
        <div class="mb-8" id="custom-alert__title"></div>
      </div>
    </x-public.popup>
  </div>
</x-cabinet-layout>
