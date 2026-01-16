@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <form action="{{ route('admin.products.store') }}" method="post">
    @csrf
    @if($product)
      <input type="hidden" name="copy" value="{{ $product->id }}">
    @endif
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
            <x-input-label for="product-name" :value="__('Наименование')" />
            <x-text-input type="text" name="name" id="product-name" value="{{ old('name') ?? $product->name ?? '' }}" class="mt-1" required />
          </div>
          <div class="form-group">
            <x-input-label for="product-sku" :value="__('Артикул')" />
            <x-text-input type="text" name="sku" id="product-sku" value="{{ old('sku') ?? $product->sku ?? '' }}" class="mt-1" required />
          </div>
          <div class="form-group">
            <x-input-label for="product-old-price" :value="__('Старая цена')" />
            <x-text-input type="text" name="old_price" id="product-old-price" value="{{ old('old_price') ?? $product->old_price ?? '' }}" class="mt-1 numeric-field" />
          </div>
          <div class="form-group">
            <x-input-label for="product-price" :value="__('Цена')" />
            <x-text-input type="text" name="price" id="product-price" value="{{ old('price') ?? $product->price ?? '' }}" class="mt-1 numeric-field" required />
          </div>
          <div class="form-group">
            <x-input-label for="product-volume" :value="__('Объем')" />
            <x-text-input type="text" name="volume" id="product-volume" value="{{ old('volume') ?? $product->volume ?? '' }}" class="mt-1" />
          </div>
          <div class="form-group">
            <x-input-label for="product-weight" :value="__('Вес')" />
            <x-text-input type="text" name="weight" id="product-weight" value="{{ old('weight') ?? $product->weight ?? '' }}" class="mt-1 numeric-field" />
          </div>
          <div class="form-group">
            <x-input-label for="product-tnved" :value="__('ТН ВЭД ЕАЭС')" />
            <x-text-input type="text" name="tnved" id="product-tnved" value="{{ old('tnved') ?? $product->tnved ?? '' }}" class="mt-1 numeric-field" />
          </div>

          <div class="form-group">
            <x-input-label for="category_id" :value="__('Родительская категория')" />
            <select id="category_id" name="category_id" class="form-control w-full">
              <option value="">Выбрать</option>
              @foreach($categories as $category)
                <option value="{{ $category->id }}" @if(old('category_id')==$category->id||(isset($product->category_id)&&$product->category_id==$category->id)){{ 'selected' }}@endif>@if($category->parent){{ $category->parent->title }} > @endif{{ $category->title }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <x-input-label for="roles" :value="__('Дополнительные категории')" />
            <select id="role" name="categories[]" multiple class="multipleSelect form-control">
              <option value="">Выбрать</option>
              @foreach($categories as $category)
                <option value="{{ $category->id }}" @if(old('category_id')==$category->id||(isset($product->category_id)&&$product->category_id==$category->id)){{ 'selected' }}@endif>@if($category->parent){{ $category->parent->title }} > @endif{{ $category->title }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <x-input-label for="type_id" :value="__('Тип товара')" />
            <select id="type_id" name="type_id" class="form-control w-full">
              <option value="">Выбрать</option>
              @foreach($product_types as $product_type)
                <option value="{{ $product_type->id }}" @if(old('type_id')==$product_type->id||(isset($product->type_id)&&$product->type_id==$product_type->id)){{ 'selected' }}@endif>{{ $product_type->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <div class="flex items-center">
              <x-checkbox id="preorder" name="preorder" value="1"
                          :checked="old('preorder') ? true : false"/>
              <x-input-label for="preorder" class="ml-2" :value="__('Предзаказ')"/>
            </div>
          </div>
          <div class="form-group">
            <div class="flex items-center">
              <x-checkbox id="hidden" name="hidden" value="1"
                          :checked="old('hidden') ? true : false"/>
              <x-input-label for="hidden" class="ml-2" :value="__('Товар скрыт')"/>
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
