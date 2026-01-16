@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <form action="{{ route('admin.sytstem_settings.store') }}" method="post">
    @csrf
    <div class="mx-auto px-3 sm:px-4 lg:px-6 py-6">
      <div class="sm:w-[75%]">
        <div class="form-group">
          <x-input-label for="key" :value="__('Код')"/>
          <x-text-input type="text" name="key" id="key" value="{{ old('key') }}" class="mt-1 block w-full" required/>
        </div>
        <div class="form-group">
          <x-input-label for="value" :value="__('Значение')" />
          <x-textarea name="value" id="value" class="mt-1 tinymce-textarea w-full"  data-toolbar="undo redo | bold align bullist forecolor | link unlink code">{{ old('value') }}</x-textarea>
        </div>
        <div class="form-group">
          <x-secondary-button type="button" onclick="toggleTinyMCE()">Выключить редактор</x-secondary-button>
          <script>
            let isTinyMCEActive = true;

            function toggleTinyMCE() {
              if (isTinyMCEActive) {
                // Выключаем TinyMCE
                tinymce.remove('#value');
              }
              isTinyMCEActive = !isTinyMCEActive;
            }
          </script>
        </div>
      </div>
    </div>
    <div class="px-3 sm:px-4 lg:px-6 py-6 flex justify-end">
      <button class="button button-success">Сохранить</button>
    </div>
  </form>
</x-admin-layout>
