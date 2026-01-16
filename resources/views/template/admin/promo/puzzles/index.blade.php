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
      <a href="{{ route('admin.puzzles.create') }}" class="button button-success">Добавить подарок</a>
    @endif
  </x-slot>
  @if(isset($prizes)&&is_array($prizes))
  <div class="mx-auto py-4">
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
        <thead>
        <tr>
          <th class="bg-gray-100 border p-2">#</th>
          <th class="bg-gray-100 border p-2">Код</th>
          <th class="bg-gray-100 border p-2">Наименование</th>
          <th class="bg-gray-100 border p-2">Порядок</th>
          <th class="bg-gray-100 border p-2">Победитель</th>
          <th class="bg-gray-100 border p-2">Изображение</th>
          <th class="bg-gray-100 border p-2" style="width:60px"></th>
        </tr>
        </thead>
        <tbody>
        @foreach($prizes as $prize)
          <tr>
            <td class="border p-2">{{ $prize['id'] }}</td>
            <td class="border p-2">{{ $prize['code'] }}</td>
            <td class="border p-2">{{ $prize['name'] }}</td>
            <td class="border p-2">{{ $prize['order'] }}</td>
            <td class="border p-2">{{ $prize['user'] ?? '' }}</td>
            <td class="border p-2" style="width: 10%">
              @if(isset($prize['image_path']))
              <a href="{{ storageToAsset($prize['image_path']) }}" data-fancybox="comment-{{ $prize['id'] }}" class="image inline-block rounded border border-myGray">
                <img src="{{ storageToAsset($prize['thumb_path']) }}" alt="" class="block w-[100px] h-[100px] rounded">
              </a>
              @endif
            </td>
            </td>
            <td class="border p-2 text-right">
              <form action="{{ route('admin.puzzles.destroy', $prize['id']) }}" id="delete-page-{{ $prize['id'] }}" method="POST">
                @csrf
                @method('DELETE')
              </form>
              <x-dropdown_menu>
                <x-slot name="content">
                  <div class="py-1" role="none">
                    <a href="{{ route('admin.puzzles.edit', $prize['id']) }}" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1">Редактировать</a>
                    <a href="#" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1" onclick="if(confirm('Удалить запись «{{ $prize['name'] }}»?'))document.getElementById('delete-page-{{ $prize['id'] }}').submit();">Удалить</a>
                  </div>
                </x-slot>
              </x-dropdown_menu>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif
</x-admin-layout>


