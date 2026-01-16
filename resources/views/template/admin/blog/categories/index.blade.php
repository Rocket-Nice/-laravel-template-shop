@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>

    @endif
      <a href="{{ route('admin.blog.categories.create') }}" class="button button-success">Добавить раздел</a>
  </x-slot>
  <x-admin.search-form :route="route('admin.blog.categories.index')">
    <div class="p-1 w-full">
      <div class="form-group">
        <x-input-label for="keyword" :value="__('Наименоване')" />
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
          <th class="bg-gray-100 border p-2">Наименоание</th>
          <th class="bg-gray-100 border p-2">Статус</th>
          <th class="bg-gray-100 border p-2">URL</th>
          <th class="bg-gray-100 border p-2" style="width:60px"></th>
        </tr>
        </thead>
        <tbody>
        @foreach($categories as $category)
          <tr>
            <td class="border p-2">{{ $category->id }}</td>
            <td class="border p-2" style="max-width:220px">
              {{ $category->name }}
            </td>
            <td class="border p-2" style="max-width:220px">
              @if(!$category->status)
                <span class="badge-red text-xs">Скрыт</span>
              @elseif($category->status == 1)
                <span class="badge-green text-xs">Активен</span>
              @elseif($category->status == 2)
                <span class="badge-gray text-xs">Неактивен</span>
              @endif
            </td>
            <td class="border p-2">
              {{ $category->slug }}
            </td>
            <td class="border p-2 text-right">
              <a class="button button-light-secondary button-sm" href="{{ route('admin.blog.categories.edit', $category->slug) }}">
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
