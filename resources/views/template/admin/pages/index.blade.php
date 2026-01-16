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
      <a href="{{ route('admin.pages.create') }}" class="button button-success">Добавить страницу</a>
    @endif
  </x-slot>
  <div class="mx-auto py-4">
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
        <thead>
        <tr>
          <th class="bg-gray-100 border p-2">#</th>
          <th class="bg-gray-100 border p-2">Наименоание</th>
          <th class="bg-gray-100 border p-2">Ссылка</th>
          <th class="bg-gray-100 border p-2">Дата создания</th>
          <th class="bg-gray-100 border p-2">Дата изменения</th>
          <th class="bg-gray-100 border p-2" style="width:60px"></th>
        </tr>
        </thead>
        <tbody>
        @foreach($pages as $page)
          <tr>
            <td class="border p-2">{{ $page->id }}</td>
            <td class="border p-2 text-left" style="max-width:220px">{{ $page->title }}</td>
            <td class="border p-2">{{ route('page', $page->slug) }}</td>
            <td class="border p-2">{{ $page->created_at ? $page->created_at->format('d.m.Y H:i') : null }}</td>
            <td class="border p-2">{{ $page->updated_at ? $page->updated_at->format('d.m.Y H:i') : null }}</td>
            <td class="border p-2 text-right">
              <form action="{{ route('admin.pages.destroy', $page->slug) }}" id="delete-page-{{ $page->id }}" method="POST">
                @csrf
                @method('DELETE')
              </form>
              <x-dropdown_menu>
                <x-slot name="content">
                  <div class="py-1" role="none">
                    <a href="{{ route('admin.pages.edit', ['page' => $page->slug]) }}" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1">Редактировать</a>
                    <a href="#" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1" onclick="if(confirm('Удалить запись «{{ $page->key }}»?'))document.getElementById('delete-page-{{ $page->id }}').submit();">Удалить</a>
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
      {{ $pages->appends(request()->input())->links('pagination::tailwind') }}
    </div>
  </div>
</x-admin-layout>


