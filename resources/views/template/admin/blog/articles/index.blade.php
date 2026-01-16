@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>

    @endif
      <a href="{{ route('admin.blog.articles.create') }}" class="button button-success">Добавить публикацию</a>
  </x-slot>
  <x-admin.search-form :route="route('admin.blog.articles.index')">
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
        @foreach($articles as $article)
          <tr>
            <td class="border p-2">{{ $article->id }}</td>
            <td class="border p-2" style="max-width:220px">
              {{ $article->title }}
            </td>
            <td class="border p-2" style="max-width:220px">
              @if(!$article->status)
                <span class="badge-red text-xs">Скрыта</span>
              @elseif($article->status == 1)
                <span class="badge-green text-xs">Активна</span>
              @endif
            </td>
            <td class="border p-2">
              {{ $article->slug }}
            </td>
            <td class="border p-2 text-right">
              <x-dropdown_menu>
                <x-slot name="content">
                  <div class="py-1" role="none">
                    <a href="{{ route('admin.blog.articles.edit', $article->slug) }}" class="hover:bg-gray-100 no-underline text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1">Редактировать</a>
                    <a href="{{ route('blog.article', $article->slug) }}" class="hover:bg-gray-100 no-underline text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1" target="_blank">Просмотр</a>
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
      {{ $articles->appends(request()->input())->links('pagination::tailwind') }}
    </div>
  </div>
</x-admin-layout>
