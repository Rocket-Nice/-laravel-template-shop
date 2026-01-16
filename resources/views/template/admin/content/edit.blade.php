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
      window.filemanger.file_dir = @json($files_dir);
    </script>
  </x-slot>
  <form action="{{ route('admin.content.update', $content->id) }}" method="post">
    @csrf
    @method('PUT')
    <div class="border-b">
      <div class="mx-auto px-2 sm:px-3 lg:px-4">
        <nav class="-mb-px flex flex-col sm:flex-row sm:justify-start space-y-2 sm:space-y-0 sm:space-x-4"
             aria-label="Tabs" role="tablist">
          @if(view()->exists($content->template_path.'.text'))
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-2" aria-selected="false" role="tab" aria-controls="tab-2-content">Текст
          </button>
          @endif
          @if(view()->exists($content->template_path.'.image'))
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-3" aria-selected="false" role="tab" aria-controls="tab-3-content">Картинки
          </button>
            @endif
            @if(view()->exists($content->template_path.'.slider'))
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-4" aria-selected="false" role="tab" aria-controls="tab-4-content">Слайдеры
          </button>
            @endif
            @if(view()->exists($content->template_path.'.orderSlider'))
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-6" aria-selected="false" role="tab" aria-controls="tab-6-content">Сортировка слайдеров
          </button>
            @endif
            @if(view()->exists($content->template_path.'.ourStores'))
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-5" aria-selected="false" role="tab" aria-controls="tab-5-content">Наши магазины
          </button>
            @endif
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-1" aria-selected="false" role="tab" aria-controls="tab-1-content">Настройки
          </button>
        </nav>
      </div>
    </div>

    <div class="mx-auto px-2 sm:px-3 lg:px-4 py-6" id="tab-content">
      <div id="tab-2-content" role="tabpanel">
        <div>
          @if(view()->exists($content->template_path.'.text'))
            @include($content->template_path.'.text')
          @endif
        </div>
      </div>
      <div id="tab-3-content" role="tabpanel">
        <div>
          @if(view()->exists($content->template_path.'.image'))
            @include($content->template_path.'.image')
          @endif
        </div>
      </div>
      <div id="tab-4-content" role="tabpanel">
        <div>
          @if(view()->exists($content->template_path.'.slider'))
            @include($content->template_path.'.slider')
          @endif
        </div>
      </div>
      <div id="tab-6-content" role="tabpanel">
        <div>
          @if(view()->exists($content->template_path.'.orderSlider'))
            @include($content->template_path.'.orderSlider')
          @endif
        </div>
      </div>
      <div id="tab-5-content" role="tabpanel">
        <div>
          @if(view()->exists($content->template_path.'.ourStores'))
            @include($content->template_path.'.ourStores')
          @endif
        </div>
      </div>
      <div id="tab-1-content" role="tabpanel">
        <div class="sm:w-[50%]">
          <div class="form-group">
            <x-input-label for="title" :value="__('Заголовок')"/>
            <x-text-input type="text" name="title" id="title" value="{{ old('title') ?? $content->title }}"
                          class="mt-1 block w-full" required/>
          </div>
          <div class="form-group">
            <x-input-label for="route" :value="__('Имя роута')"/>
            <x-text-input type="text" name="route" id="route" value="{{ old('route') ?? $content->route }}"
                          class="mt-1 block w-full" required/>
          </div>
          <div class="form-group">
            <x-input-label for="template_path" :value="__('Путь к шаблону')" />
            <x-text-input type="text" name="template_path" id="template_path" value="{{ old('template_path') ?? $content->template_path }}" class="mt-1 block w-full" required />
          </div>
          <div class="form-group">
            <div class="flex items-center">
              <x-checkbox id="active" name="active" value="1"
                          :checked="(old('active') ?? $content->active) ? true : false"/>
              <x-input-label for="active" class="ml-2" :value="__('Страница открыта')"/>
            </div>
          </div>
          <div class="form-group">
            <x-input-label for="content-keywords" :value="__('Ключевые слова для поиска')" />
            <x-textarea type="text" name="keywords" id="content-keywords" class="w-full mt-1">{{ old('keywords') ?? $content->keywords }}</x-textarea>
          </div>
        </div>
      </div>
    </div>
    <div class="px-3 sm:px-4 lg:px-6 py-6 flex justify-end">
      <x-primary-button>Сохранить</x-primary-button>
    </div>
  </form>
</x-admin-layout>
