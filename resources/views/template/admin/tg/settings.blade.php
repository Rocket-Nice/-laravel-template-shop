@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <form action="{{ route('admin.tg_notifications.save') }}" method="post">
    @csrf
    <div class="border-b">
      <div class="mx-auto px-2 sm:px-3 lg:px-4">
        <nav class="-mb-px flex flex-col sm:flex-row sm:justify-start space-y-2 sm:space-y-0 sm:space-x-4"
             aria-label="Tabs" role="tablist">
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-1" aria-selected="true" role="tab" aria-controls="tab-1-content">Общие
          </button>
        </nav>
      </div>
    </div>

    <div class="mx-auto px-2 sm:px-3 lg:px-4 py-6" id="tab-content">
      <div id="tab-1-content" role="tabpanel">
        <div class="sm:w-[75%]">
          <div class="form-group">
            <x-input-label for="tg_notifications_bot" :value="__('API ключ')" />
            <x-text-input type="text" name="tg_notifications_bot" id="tg_notifications_bot" class="mt-1 w-full" value="{{ old('tg_notifications_bot') }}"/>
          </div>
          <div class="form-group">
            <x-input-label for="tg_notifications_start" :value="__('Приветственное сообщение')" />
            <x-textarea type="text" name="tg_notifications_start" id="tg_notifications_start" class="mt-1 w-full">{{ old('tg_notifications_start') ?? $messages->where('key', 'tg_notifications_start')->first()?->value }}</x-textarea>
          </div>
          <div class="form-group">
            <x-input-label for="tg_notifications_reply" :value="__('Автоответ')" />
            <x-textarea type="text" name="tg_notifications_reply" id="tg_notifications_reply" class="mt-1 w-full">{{ old('tg_notifications_reply') ?? $messages->where('key', 'tg_notifications_reply')->first()?->value }}</x-textarea>
          </div>
        </div>
      </div>
    </div>
    <div class="px-3 sm:px-4 lg:px-6 py-6 flex justify-end">
      <x-primary-button>Сохранить</x-primary-button>
    </div>
  </form>
</x-admin-layout>
