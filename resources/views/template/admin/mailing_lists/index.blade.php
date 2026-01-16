@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
      <a href="{{ route('admin.mailing_lists.create') }}" class="button button-success">Создать новую рассылку</a>
    @endif
  </x-slot>
  <div class="mx-auto py-4">
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
        <thead>
        <tr>
          <th class="bg-gray-100 border p-2" style="width: 5%">#</th>
          <th class="bg-gray-100 border p-2">Наименование</th>
          <th class="bg-gray-100 border p-2">Способ рассылки</th>
          <th class="bg-gray-100 border p-2">Количество адресов</th>
          <th class="bg-gray-100 border p-2">Дата</th>
          <th class="bg-gray-100 border p-2" style="width:120px"></th>
        </tr>
        </thead>
        <tbody>
        @foreach($mailing_lists as $mailing_list)
          <tr>
            <td class="border p-2">{{ $mailing_list->id }}</td>
            <td class="border p-2">{{ $mailing_list->name }}</td>
            <td class="border p-2">{{ $mailing_list->method }}</td>
            <td class="border p-2">{{ $mailing_list->users()->count() }}</td>
            <td class="border p-2">{{ $mailing_list->sending_date ? $mailing_list->sending_date->format('d.m.Y H:i') : '' }}</td>
            <td class="border p-2">
              <form action="{{ route('admin.mailing_lists.destroy', $mailing_list->id) }}" id="delete-mailing_list-{{ $mailing_list->id }}" method="POST">
                @csrf
                @method('DELETE')
              </form>
              <x-dropdown_menu>
                <x-slot name="content">
                  <div class="py-1" role="none">
                    <a href="{{ route('admin.mailing_lists.edit', $mailing_list->id) }}" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1">Редактировать</a>
                    <a href="#" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1" onclick="if(confirm('Удалить запись «{{ $mailing_list->name }}»?'))document.getElementById('delete-mailing_list-{{ $mailing_list->id }}').submit();">Удалить</a>
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
      {{ $mailing_lists->appends(request()->input())->links('pagination::tailwind') }}
    </div>
  </div>
</x-admin-layout>

