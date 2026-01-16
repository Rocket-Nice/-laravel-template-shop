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
      <a href="{{ route('admin.prizes.create') }}" class="button button-success">Создать подарок</a>
    @endif
  </x-slot>
  <div class="mx-auto py-4">
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
        <thead>
        <tr>
          <th class="bg-gray-100 border p-2">#</th>
          <th class="bg-gray-100 border p-2">Наименоание</th>
          <th class="bg-gray-100 border p-2">Общее количество</th>
          <th class="bg-gray-100 border p-2">Аткивен</th>
          <th class="bg-gray-100 border p-2">Товар на сайте</th>
          <th class="bg-gray-100 border p-2" style="width:60px"></th>
        </tr>
        </thead>
        <tbody>
        @foreach($prizes as $prize)
          <tr>
            <td class="border p-2">{{ $prize->id }}</td>
            <td class="border p-2 text-left" style="max-width:220px">
              <div class="flex items-center space-x-4">
                @if(isset($prize->options['thumb']))
                  <a href="{{ $prize->options['img'] }}" data-fancybox>
                <img src="{{ $prize->options['thumb'] }}" alt="{{ $prize->name }}">
                  </a>
                @endif
                <div>{{ $prize->name }}</div>
              </div>
            </td>
            <td class="border p-2">{{ $prize->total }}</td>
            <td class="border p-2">{!! $prize->active ? '<span class="badge-green text-xs">Включен</span>' : '<span class="badge-red text-xs">Выключен</span>' !!}</td>
            <td class="border p-2">
              @if($prize->product)
                <a href="{{ route('admin.products.edit', $prize->product->slug) }}">{{ $prize->product->name }}</a>
              @endif
            </td>
            <td class="border p-2 text-right">
              <x-dropdown_menu>
                <x-slot name="content">
                  <div class="py-1" role="none">
                    <a href="{{ route('admin.prizes.edit', ['prize' => $prize->id]) }}" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1">Редактировать</a>
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
      {{ $prizes->appends(request()->input())->links('pagination::tailwind') }}
    </div>
  </div>
</x-admin-layout>


