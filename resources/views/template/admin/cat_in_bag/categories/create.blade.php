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
  <form action="{{ route('admin.cat-in-bag.categories.store') }}" method="post">
    @csrf
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
            <x-text-input type="text" name="name" id="category-name" value="{{ old('name') }}" class="mt-1 block w-full" required />
          </div>
          <div class="form-group">
            <x-input-label for="image" class="mb-2" :value="__('Картинка')" />
            <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
              <div id="lfm-preview-image" class="rounded-md overflow-hidden w-20" data-name="image">
                @if(old('image'))
                  <input type="hidden" name="image[img]" id="input-lfm-image" value="{{ old('image')['img'] ?? '' }}">
                  <input type="hidden" name="image[thumb]" id="input-lfm-thumb-image" value="{{ old('image')['thumb'] ?? old('image')['img'] ?? '' }}">
                  <a href="javascript:;" data-fancybox="true" data-src="{{ old('image')['img'] ?? '' }}" style="display: block;">
                    <img src="{{ old('image')['thumb'] ?? old('image')['img'] ?? '' }}" class="overflow-hidden max-w-full object-cover object-center">
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
          <div class="form-group">
            <div class="flex items-center">
              <x-checkbox id="is_enabled" name="is_enabled" value="1"
                          :checked="old('is_enabled', true) ? true : false"/>
              <x-input-label for="is_enabled" class="ml-2" :value="__('Включена')"/>
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
