@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <x-slot name="script">
    <script>
      window.filemanger.working_dir = @json($working_dir);
    </script>
  </x-slot>
  <form action="{{ route('admin.blog.categories.update', $category->slug) }}" method="post">
    @csrf
    @method('PUT')
    <div class="border-b">
      <div class="mx-auto px-2 sm:px-3 lg:px-4">
        <nav class="-mb-px flex flex-col sm:flex-row sm:justify-start space-y-2 sm:space-y-0 sm:space-x-4"
             aria-label="Tabs" role="tablist">
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-1" aria-selected="true" role="tab" aria-controls="tab-1-content">Общие данные
          </button>
        </nav>
      </div>
    </div>

    <div class="mx-auto px-2 sm:px-3 lg:px-4 py-6" id="tab-content">
      <div id="tab-1-content" role="tabpanel">
        <div class="sm:w-[50%]">
          <div class="form-group">
            <x-input-label for="category-name" :value="__('Наименование')" />
            <x-text-input type="text" name="name" id="category-name" value="{{ old('name') ?? $category->name }}" class="mt-1 block w-full" required />
          </div>
          <div class="form-group">
            <x-input-label for="status" :value="__('Статус')"/>
            <select id="status" name="status" class="form-control w-full">
              <option value="0" @if(old('status') == 0 || $category->status == 0) selected @endif>Скрыт</option>
              <option value="1" @if(old('status') == 1 || $category->status == 1) selected @endif>Активн</option>
              <option value="2" @if(old('status') == 2 || $category->status == 2) selected @endif>Неактивн</option>
            </select>
          </div>
          <div class="form-group">

            <x-input-label for="image" class="mb-2" :value="__('Изображение')" />

            <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
              <div id="lfm-preview-image" class="rounded-md overflow-hidden w-20" data-name="data[image]">
                <input type="hidden" name="data[image][maxWidth]" id="input-lfm-image" value="320">
                @if(isset($category->data['image']['img']))
                  <input type="hidden" name="data[image][img]" id="input-lfm-image" value="{{ old('data')['image']['img'] ?? $category->data['image']['img'] ?? '' }}">
                  <input type="hidden" name="data[image][thumb]" id="input-lfm-thumb-image" value="{{ old('data')['image']['thumb'] ?? $category->data['image']['thumb'] ?? '' }}">

                  <a href="javascript:;" data-fancybox="true" data-src="{{ $category->data['image']['img'] }}" style="display: block;">
                    <img src="{{ $category->data['image']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
                  </a>
                @endif
              </div>
              <div class="mb-2">
                <div class="mb-2 text-gray-500 text-sm"></div>
                <button
                  type="button"
                  id="image"
                  class="button button-secondary"
                  data-lfm="image"
                  data-preview="lfm-preview-image">Выбрать изображение</button>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="px-3 sm:px-4 lg:px-6 py-6 flex justify-end">
      <x-primary-button>Сохранить</x-primary-button>
    </div>
  </form>
</x-admin-layout>
