@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
    <a href="{{ route('admin.cat-in-bag.categories.create') }}" class="button button-success">Добавить категорию</a>
  </x-slot>
  <x-admin.search-form :route="route('admin.cat-in-bag.categories.index')">
    <div class="p-1 w-full">
      <div class="form-group">
        <x-input-label for="keyword" :value="__('Наименование')" />
        <x-text-input type="text" name="keyword" id="keyword" value="{{ request()->get('keyword') }}" class="mt-1 block w-full" />
      </div>
    </div>
  </x-admin.search-form>
  <div class="mx-auto py-4">
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
        <thead>
        <tr>
          <th class="bg-gray-100 border p-2 text-sm" style="width: 5%;">#</th>
          <th class="bg-gray-100 border p-2" style="width: 80px;">Картинка</th>
          <th class="bg-gray-100 border p-2">Наименование</th>
          <th class="bg-gray-100 border p-2">Статус</th>
          <th class="bg-gray-100 border p-2" style="width:60px"></th>
        </tr>
        </thead>
        <tbody>
        @foreach($categories as $category)
          <tr>
            <td class="border p-2">{{ $category->id }}</td>
            <td class="border p-2">
              @if(isset($category->data['image']))
                <a href="javascript:;" data-fancybox="true" data-src="{{ $category->data['image']['img'] ?? '' }}" style="display: block;">
                  <img src="{{ $category->data['image']['thumb'] ?? $category->data['image']['img'] ?? '' }}" class="overflow-hidden max-w-full object-cover object-center mx-auto">
                </a>
              @else
                <span class="text-gray-400">—</span>
              @endif
            </td>
            <td class="border p-2" style="max-width:220px">
              {{ $category->name }}
            </td>
            <td class="border p-2" style="max-width:220px">
              @if($category->is_enabled)
                <span class="badge-green text-xs">Включена</span>
              @else
                <span class="badge-gray text-xs">Выключена</span>
              @endif
            </td>
            <td class="border p-2 text-right">
              <a class="button button-light-secondary button-sm" href="{{ route('admin.cat-in-bag.categories.edit', $category->id) }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pencil" width="24" height="24" viewBox="0 0 24 24" stroke-width="1" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                  <path d="M4 20h4l10.5 -10.5a1.5 1.5 0 0 0 -4 -4l-10.5 10.5v4" />
                  <path d="M13.5 6.5l4 4" />
                </svg>
              </a>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    <div class="p-2">
      {{ $categories->appends(request()->input())->links('pagination::tailwind') }}
    </div>
  </div>
</x-admin-layout>
