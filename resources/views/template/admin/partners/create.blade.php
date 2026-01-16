@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <form action="{{ route('admin.partners.store') }}" method="post">
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
            <x-input-label for="code" :value="__('Код партнера')" />
            <x-text-input type="text" name="code" id="code" value="{{ old('code') }}" class="mt-1 block w-full" required />
          </div>
          <div class="form-group">
            <x-input-label for="email" :value="__('Email пользователя')" />
            <x-text-input type="text" name="email" id="email" value="{{ old('email') }}" class="mt-1 block w-full" required />
          </div>
          <div class="form-group">
            <x-input-label for="description" :value="__('Описание')" />
            <x-textarea name="description" id="description" class="mt-1 block w-full"></x-textarea>
          </div>
          <div class="form-group">
            <div class="flex items-center">
              <x-checkbox id="promocode" name="promocode" value="1"
                          :checked="old('promocode') ? true : false"/>
              <x-input-label for="promocode" class="ml-2" :value="__('Создать промокод')"/>
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
