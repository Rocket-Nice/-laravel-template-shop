<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
      <div class="flex justify-end">
        <div class="flex flex-col md:flex-row -m-1">
          <a href="{{ route('admin.product-group.index') }}" class="m-1 button button-warning">Группы товаров</a>
          <a href="{{ route('admin.content.create') }}" class="m-1 button button-success">Добавить страницу</a>
        </div>
      </div>
    @endif
  </x-slot>
  <div class="mx-auto py-4">
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
        <thead>
          <tr>
            <th class="bg-gray-100 border p-2">#</th>
            <th class="bg-gray-100 border p-2">Заголовок</th>
            <th class="bg-gray-100 border p-2">Статус</th>
            <th class="bg-gray-100 border p-2" style="width:120px"></th>
          </tr>
        </thead>
        <tbody>
          @foreach($content as $page)
            <tr>
              <td class="border p-2">{{ $page->id }}</td>
              <td class="border p-2">{{ $page->title }}</td>
              <td class="border p-2">{!! $page->active ? '<span class="badge-green text-xs">Включен</span>' : '<span class="badge-red text-xs">Выключен</span>' !!}</td>
              <td>
                <x-dropdown_menu>
                  <x-slot name="content">
                    <div class="py-1" role="none">
                      <a href="{{ route('admin.content.edit', $page->id)  }}" class="hover:bg-gray-100 no-underline text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1">Редактировать</a>
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
      {{ $content->appends(request()->input())->links('pagination::tailwind') }}
    </div>
  </div>
</x-admin-layout>
