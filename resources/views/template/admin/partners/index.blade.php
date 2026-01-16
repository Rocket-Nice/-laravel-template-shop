@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="title">
    {{ $seo['title'] ?? false }}
  </x-slot>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>

      <a href="javascript:;" data-fancybox data-src="partners-cabinet-settings" class="button button-warning">Настройка кабинетов</a>
      <a href="{{ route('admin.partners.create') }}" class="button button-success">Добавить партнера</a>
    @endif
  </x-slot>
  <div id="partners-cabinet-settings">
    <form action="" method="POST">
      @csrf
      <div class="form-group">
        <x-input-label for="date_from" :value="__('Дата отображения заказов')"/>
        <x-text-input type="text" name="date_from" id="date_from" value="{{ request()->get('date_from') }}" data-minDate="false" placeholder="{{ now()->startOfMonth()->format('d.m.Y H:i') }}" class="mt-1 block w-full datepicker"/>
      </div>
      <x-primary-button>Сохранить</x-primary-button>
    </form>
  </div>
{{--  <x-admin.search-form :route="route('admin.partners.index')">--}}
{{--    <div class="p-1 w-full">--}}
{{--      <div class="form-group">--}}
{{--        <x-input-label for="order_id" :value="__('ID заказа')" />--}}
{{--        <x-text-input type="text" name="order_id" id="order_id" value="{{ request()->get('order_id') }}" class="mt-1 block w-full" />--}}
{{--      </div>--}}
{{--    </div>--}}
{{--    <div class="p-1 w-full">--}}
{{--      <div class="form-group">--}}
{{--        <x-input-label for="code" :value="__('Код купона')" />--}}
{{--        <x-text-input type="text" name="code" id="code" value="{{ request()->get('code') }}" class="mt-1 block w-full" />--}}
{{--      </div>--}}
{{--    </div>--}}
{{--    <div class="p-1 w-full">--}}
{{--      <div class="form-group">--}}
{{--        <x-input-label for="keyword" :value="__('Имя, email или телефон')" />--}}
{{--        <x-text-input type="text" name="keyword" id="keyword" value="{{ request()->get('keyword') }}" class="mt-1 block w-full" />--}}
{{--      </div>--}}
{{--    </div>--}}
{{--  </x-admin.search-form>--}}
  <div class="mx-auto py-4">
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
        <thead>
        <tr>
          <th class="bg-gray-100 border p-2">#</th>
          <th class="bg-gray-100 border p-2">Наименоание</th>
          <th class="bg-gray-100 border p-2">Ссылка</th>
          <th class="bg-gray-100 border p-2">Пользователь</th>
          <th class="bg-gray-100 border p-2">Промокод</th>
          <th class="bg-gray-100 border p-2" style="width:60px"></th>
        </tr>
        </thead>
        <tbody>
        @foreach($partners as $partner)
          <tr>
            <td class="border p-2">{{ $partner->id }}</td>
            <td class="border p-2">
              {{ $partner->name }}
            </td>
            <td class="border p-2">
              {{ route('partner', $partner->slug) }}
            </td>
            <td class="border p-2">
              {{ $partner->user->email ?? '' }} (id {{ $partner->user_id ?? '' }})
            </td>
            <td class="border p-2">
              @if($partner->coupon_code)
                {{ $partner->coupon_code ?? '' }} ({{ $partner->amount ?? '' }}@if($partner->type == 2){{ '%' }}@else{{ ' руб' }}@endif)
              @endif

            </td>
            <td class="border p-2 text-right">
              <x-dropdown_menu>
                <x-slot name="content">
                  <div class="py-1" role="none">
                    <a href="{{ route('admin.partners.edit', $partner->slug) }}" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1">Редактировать</a>
                    <a href="{{ route('partner.cabinet.index', ['u' => $partner->user_id]) }}" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1" target="_blank">Открыть кабинет</a>
                  </div>
                </x-slot>
              </x-dropdown_menu>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    <div class="p-2">
      {{ $partners->appends(request()->input())->links('pagination::tailwind') }}
    </div>
  </div>
</x-admin-layout>


