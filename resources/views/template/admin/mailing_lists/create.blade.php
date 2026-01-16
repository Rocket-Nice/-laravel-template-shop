@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <form action="{{ route('admin.mailing_lists.store') }}" method="post">
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
            <x-input-label for="name" :value="__('Наименование')" />
            <x-text-input type="text" name="name" id="name" value="{{ old('name') }}" class="mt-1 block w-full" required />
          </div>
          <div class="form-group">
            <x-input-label for="method" :value="__('Способ рассылки')"/>
            <select id="method" name="method" class="form-control w-full">
              <option value="">Выбрать</option>
              <option value="SMS" @if(old('method')&&old('method')=='SMS'){!! 'selected' !!}@endif>SMS</option>
              <option value="Telegram" @if(old('method')&&old('method')=='Telegram'){!! 'selected' !!}@endif>Telegram</option>
            </select>
          </div>
          <div class="form-group">
            <x-input-label for="message" :value="__('Сообщение')" />
            <x-textarea name="message" id="message" class="mt-1 block w-full tinymce-textarea">{{ old('message') }}</x-textarea>
          </div>
          <div class="form-group">
            <x-input-label for="sending_date" :value="__('Дата проведения расылки')" />
            <x-text-input type="text" name="sending_date" id="sending_date" value="{{ old('sending_date') }}" data-minDate="false" class="mt-1 block w-full datepicker" />
          </div>
        </div>
      </div>
    </div>
    <div class="px-3 sm:px-4 lg:px-6 py-6 flex justify-end">
      <x-primary-button>Сохранить</x-primary-button>
    </div>
  </form>
</x-admin-layout>


