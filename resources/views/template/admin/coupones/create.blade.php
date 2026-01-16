@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <form action="{{ route('admin.coupones.store') }}" method="post">
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
          <div x-data="{ products: '{{ old('type') ?? 10 }}' }" class="mb-2">
            <div class="form-group">
              <x-input-label :value="__('Тип промокода')" />
              <div class="flex items-center">
                <input type="radio" id="type-1" name="type" value="10" class="form-control" @if(old('type') == 10) {{ 'checked' }}@endif>
                <label for="type-1" class="ml-2">Скидка в рублях на корзину</label>
              </div>
              <div class="flex items-center">
                <input type="radio" id="type-2" name="type" value="1" class="form-control" @if(old('type') == 1) {{ 'checked' }}@endif>
                <label for="type-2" class="ml-2">Скдика в процентах на 1 товар</label>
              </div>
              <div class="flex items-center">
                <input type="radio" id="type-3" name="type" value="2" class="form-control" @if(old('type') == 2) {{ 'checked' }}@endif>
                <label for="type-3" class="ml-2">Скдика в процентах на корзину</label>
              </div>
              <div class="flex items-center">
                <input type="radio" id="type-4" name="type" value="4" class="form-control" @if(old('type') == 4) {{ 'checked' }}@endif x-model="products">
                <label for="type-4" class="ml-2">Скдика в процентах на выбранные товары</label>
              </div>
            </div>
            <div class="form-group" x-show="products == 4">
              <x-input-label for="products" :value="__('Товары')"/>
              <select name="products[]" id="products" multiple class="multipleSelect form-control">
                @foreach($products as $product)
                  <option value="{{ $product->id }}" data-keywords="{{ $product->category_title }}">{{ $product->id }}: {{ $product->name }} ({{ $product->sku }}, Категория "{{ $product->category_title }}")</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group">
            <x-input-label for="amount" :value="__('Сумма скидки')" />
            <x-text-input type="text" name="amount" id="amount" value="{{ old('amount') }}" class="mt-1 block w-full numeric-field" required />
          </div>
          <div class="form-group">
            <x-input-label for="available_until" :value="__('Срок действия')"/>
            <x-text-input type="text" name="available_until" id="available_until" value="{{ old('available_until') ?? now()->addMonths(6)->format('d.m.Y') }}" data-minDate="false" data-timepicker="0" class="mt-1 block w-full datepicker"/>
          </div>
          <div class="form-group">
            <x-input-label for="codes" :value="__('Введите промокоды по одному в строке')" />
            <x-textarea name="codes" id="codes" class="mt-1 block w-full">{{ old('codes') }}</x-textarea>
            <div class="hint">Не больше 200 промокодов за раз</div>
          </div>
        </div>
      </div>
    </div>
    <div class="px-3 sm:px-4 lg:px-6 py-6 flex justify-end">
      <x-primary-button>Создать купоны</x-primary-button>
    </div>
  </form>
</x-admin-layout>
