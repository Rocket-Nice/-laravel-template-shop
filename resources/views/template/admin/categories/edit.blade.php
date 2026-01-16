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
  <form action="{{ route('admin.categories.update', $category->slug) }}" method="post">
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
            <x-input-label for="category-title" :value="__('Наименование')" />
            <x-text-input type="text" name="title" id="category-title" value="{{ old('title') ?? $category->title }}" class="mt-1 block w-full" required />
          </div>
          <div class="form-group">
            <x-input-label for="category-description" :value="__('Описание')" />
            <x-textarea name="description" id="category-description" class="mt-1 block w-full">{{ old('description') ?? $category->description }}</x-textarea>
          </div>

          <div class="form-group">
            <x-input-label for="category-order" :value="__('Порядок сортировки')" />
            <x-text-input type="text" name="order" id="category-order" value="{{ old('order') ?? $category->order }}" class="mt-1 block w-full" />
          </div>
          <div class="form-group">
            <x-input-label for="parent" :value="__('Родительская категория')" />
            <select id="parent" name="parent" class="form-control w-full">
              <option value="">Выбрать</option>
              @foreach($categories as $parent)
                @if($parent->id==$category->id)
                  @continue
                @endif
                <option value="{{ $parent->id }}" @if($category->category_id==$parent->id){{ 'selected' }}@endif>@if($parent->parent){{ $parent->parent->title }} > @endif{{ $parent->title }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <div class="flex items-center">
              <x-checkbox id="hidden" name="hidden" value="1"
                          :checked="old('hidden') || $category->hidden ? true : false"/>
              <x-input-label for="hidden" class="ml-2" :value="__('Категория скрыта')"/>
            </div>
          </div>
          <div class="form-group">

            <x-input-label for="categoryImage" class="mb-2" :value="__('Изображение категории')" />

            <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
              <div id="lfm-preview-categoryImage" class="rounded-md overflow-hidden w-20" data-name="options[categoryImage]">
                @if(isset($category->options['categoryImage']['img']))
                  <input type="hidden" name="options[categoryImage][img]" id="input-lfm-categoryImage" value="{{ old('options')['categoryImage']['img'] ?? $category->options['categoryImage']['img'] ?? '' }}">
                  <input type="hidden" name="options[categoryImage][thumb]" id="input-lfm-thumb-categoryImage" value="{{ old('options')['categoryImage']['thumb'] ?? $category->options['categoryImage']['thumb'] ?? '' }}">

                  <a href="javascript:;" data-fancybox="true" data-src="{{ $category->options['categoryImage']['img'] }}" style="display: block;">
                    <img src="{{ $category->options['categoryImage']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
                  </a>
                @endif
              </div>
              <div class="mb-2">
                <div class="mb-2 text-gray-500 text-sm"></div>
                <button
                  type="button"
                  id="categoryImage"
                  class="button button-secondary"
                  data-lfm="image"
                  data-preview="lfm-preview-categoryImage">Выбрать изображение</button>
              </div>

            </div>
          </div>
          <div class="form-group">

            <x-input-label for="menuImage" class="mb-2" :value="__('Изображение в меню')" />

            <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
              <div id="lfm-preview-menuImage" class="rounded-md overflow-hidden w-20" data-name="options[menuImage]">
                @if(isset($category->options['menuImage']['img']))
                  <input type="hidden" name="options[menuImage][img]" id="input-lfm-menuImage" value="{{ old('options')['menuImage']['img'] ?? $category->options['menuImage']['img'] ?? '' }}">
                  <input type="hidden" name="options[menuImage][thumb]" id="input-lfm-thumb-menuImage" value="{{ old('options')['menuImage']['thumb'] ?? $category->options['menuImage']['thumb'] ?? '' }}">

                  <a href="javascript:;" data-fancybox="true" data-src="{{ $category->options['menuImage']['img'] }}" style="display: block;">
                    <img src="{{ $category->options['menuImage']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
                  </a>
                @endif
              </div>
              <div class="mb-2">
                <div class="mb-2 text-gray-500 text-sm"></div>
                <button
                  type="button"
                  id="menuImage"
                  class="button button-secondary"
                  data-lfm="image"
                  data-preview="lfm-preview-menuImage">Выбрать изображение</button>
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
