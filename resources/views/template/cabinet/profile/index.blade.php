@section('title', $seo['title'] ?? config('app.name'))
<x-cabinet-layout>
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-6 sm:py-9 md:py-12">
    <div>
      <h1 class="flex-1 d-headline-1 m-headline-1 text-left md:text-center">{{ $seo['title'] }}</h1>
    </div>
  </div>
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16">
    <div id="wrapper" class="flex relative">
      <div id="leftMenu"
           class="hidden lg:block min-w-[260px] md:w-[24.936061%] relative top-0 border-r border-r-myGreen sm:mr-[15px] md:mr-[45px] lg:mr-[74px] xl:mr-[104px] 2xl:mr-[148px]">
        <div id="leftMenu-content" class="relative">
          <div class="pb-6 sm:pb-9 md:pb-12 space-y-6 px-6 z-20 fixed top-0 right-0 w-full max-w-[390px] h-screen bg-myLightGray shadow-xl transform translate-x-full transition-transform duration-300 overflow-y-auto lg:shadow-none lg:relative lg:top-auto lg:right-auto lg:overflow-y-visible lg:translate-x-0 lg:bg-transparent">
            @include('_parts.cabinet.leftMenu')
          </div>
        </div>
      </div>
      <!-- Main Content -->
      <div class="flex-1">
        <form action="{{ route('cabinet.profile.update') }}" method="POST">
          @csrf
          @method('PUT')
          <div class="mb-3">
            <x-public.order-input type="text" id="last_name" name="last_name" :label="__('Ваша фамилия')" value="{{ old('last_name') ?? $user->last_name }}" required/>
          </div>
          <div class="mb-3">
            <x-public.order-input type="text" id="first_name" name="first_name" :label="__('Ваше имя')" value="{{ old('first_name') ?? $user->first_name }}" required/>
          </div>
          <div class="mb-3">
            <x-public.order-input type="text" id="middle_name" name="middle_name" :label="__('Ваше отчество')" value="{{ old('middle_name') ?? $user->middle_name }}" />
          </div>
          <div class="mb-3">
            <x-public.order-input type="text" id="phone" name="phone" :label="__('Ваш телефон')" value="{{ old('phone') ?? $user->phone }}" class="cormorantInfant" required/>
          </div>
          <div class="mb-3">
            <x-public.order-input type="text" id="email" name="email" :label="__('E-mail адрес')" value="{{ old('email') ?? $user->email }}" required/>
          </div>
          <div class="mb-3">
            <x-public.order-input type="text" id="birthday" name="birthday" class="birthday" :label="__('Дата рождения')" value="{{ old('birthday') ?? $user->birthday?->format('d.m.Y') }}" :disabled="$user->birthday ? true : false"/>
          </div>
          <div class="d-headline-4 m-headline-3 mt-6 md:mt-12 mb-3">
            Изменить пароль
          </div>
          <div class="mb-3">
            <x-public.order-input type="text" id="password" name="password" :label="__('Пароль')" />
          </div>
          <div class="mb-3">
            <x-public.order-input type="text" id="password_confirmation" name="password_confirmation" :label="__('Повторите пароль')" />
          </div>
          <div class="text-center mt-6">
            <x-public.primary-button type="submit" class="md:h-14 md:w-full md:max-w-[285px] mx-auto">Сохранить</x-public.primary-button>
          </div>
        </form>
      </div>
    </div>
  </div>
</x-cabinet-layout>
