<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <form action="{{ route('admin.content.store') }}" method="post">
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
            <x-input-label for="title" :value="__('Заголовок')" />
            <x-text-input type="text" name="title" id="title" value="{{ old('title') }}" class="mt-1 block w-full" required />
          </div>
          <div class="form-group">
            <x-input-label for="route" :value="__('Имя роута')" />
            <x-text-input type="text" name="route" id="route" value="{{ old('route') }}" class="mt-1 block w-full" required />
          </div>
          <div class="form-group">
            <x-input-label for="template_path" :value="__('Путь к шаблону')" />
            <x-text-input type="text" name="template_path" id="template_path" value="{{ old('template_path') }}" class="mt-1 block w-full" required />
          </div>
          <div class="form-group">
            <div class="flex items-center">
              <x-checkbox id="active" name="active" value="1"
                          :checked="old('active') ? true : false"/>
              <x-input-label for="active" class="ml-2" :value="__('Страница открыта')"/>
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
