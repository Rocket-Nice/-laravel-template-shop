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
    @endif
  </x-slot>
  <div class="flex justify-end">
    <a href="{{ route('admin.product-group.create') }}" class="button button-success">Добавить группу товаров</a>
  </div>
  <div class="mx-auto py-4">
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
        <thead>
        <tr>
          <th class="bg-gray-100 border p-2">#</th>
          <th class="bg-gray-100 border p-2">Наименоание</th>
          <th class="bg-gray-100 border p-2">Связанная страница</th>
          <th class="bg-gray-100 border p-2">Количество товаров</th>
          <th class="bg-gray-100 border p-2" style="width:60px"></th>
        </tr>
        </thead>
        <tbody>
        @foreach($productGroups as $productGroup)
          <tr>
            <td class="border p-2">{{ $productGroup->id }}</td>
            <td class="border p-2 text-left" style="max-width:220px">{{ $productGroup->name }}</td>
            <td class="border p-2">
              @foreach($productGroup->pages as $page)
                <span class="badge-blue text-xs">{{ $page->title }}</span>
              @endforeach
            </td>
            <td class="border p-2">{{ $productGroup->products()->count() }}</td>
            <td class="border p-2 text-right">
              <form action="{{ route('admin.product-group.destroy', $productGroup->id) }}" id="delete-product-group-{{ $productGroup->id }}" method="POST">
                @csrf
                @method('DELETE')
              </form>
              <x-ui.dropdown :trigger="'...'">
                <x-ui.dropdown-link href="{{ route('admin.product-group.edit', $productGroup->id) }}">Редактировать</x-ui.dropdown-link>
                <x-ui.dropdown-link href="#" onclick="if(confirm('Удалить группу «{{ $productGroup->name }}»?'))document.getElementById('delete-product-group-{{ $productGroup->id }}').submit();">Удалить</x-ui.dropdown-link>
              </x-ui.dropdown>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    <div class="p-2">
      {{ $productGroups->appends(request()->input())->links('pagination::tailwind') }}
    </div>
  </div>
</x-admin-layout>


