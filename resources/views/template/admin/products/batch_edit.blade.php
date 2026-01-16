@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <x-search-form :route="url()->current()">
    <style>
      @keyframes spin {
        0% {
          transform: rotate(0deg);
        }
        100% {
          transform: rotate(360deg);
        }
      }
    </style>
    <div class="p-1 w-full">
      <div class="form-group">
        <x-input-label for="keyword" :value="__('Наименоване или артикул')" />
        <x-text-input type="text" name="keyword" id="keyword" value="{{ request()->get('keyword') }}" class="mt-1 block w-full" />
      </div>
    </div>
    <div class="p-1 w-full">
      <div class="form-group">
        <x-input-label for="categories" :value="__('Категория')" />
        <select id="categories" name="categories[]" multiple class="multipleSelect form-control w-full">
          <option value="">Выбрать</option>
          @foreach($categories as $category)
            <option value="{{ $category->id }}" @if(is_array(request()->get('categories'))&&in_array($category->id, request()->get('categories'))){!! 'selected' !!}@endif>@if($category->parent){{ $category->parent->title }} > @endif{{ $category->title }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="product_type" :value="__('Тип товара')" />
        <select id="product_type" name="product_type" class="form-control w-full">
          <option value="">Выбрать</option>
          <option value="{all}"  @if(request()->product_type=='{all}'){{ 'selected' }}@endif>Все</option>
          @foreach($product_types as $product_type)
            <option value="{{ $product_type->id }}"  @if(request()->product_type==$product_type->id){{ 'selected' }}@endif>{{ $product_type->name }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="filter-hidden" :value="__('Скрыт')"/>
        <select id="filter-hidden" name="hidden" class="form-control w-full">
          <option @if(request()->hidden===null)
            {!! 'selected' !!}
            @endif>Все
          </option>
          <option value="1" @if(request()->get('hidden')==='1')
            {!! 'selected' !!}
            @endif>Да
          </option>
          <option value="0" @if(request()->get('hidden')==='0')
            {!! 'selected' !!}
            @endif>Нет
          </option>
        </select>
      </div>
    </div>
  </x-search-form>
  <div class="mx-auto py-4">
    <form action="{{ route('admin.products.batchUpdate') }}" method="POST" id="products-update">
      @csrf
      @method('PUT')
      <div class="relative overflow-x-auto min-h-[500px]">
        <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-xs">
          <thead>
          <tr>
            <th class="bg-gray-100 border p-2">#</th>
            <th class="bg-gray-100 border p-2">Изображение</th>
            <th class="bg-gray-100 border p-2">Наименоание</th>
            <th class="bg-gray-100 border p-2">Артикул</th>
            <th class="bg-gray-100 border p-2">Категория</th>
            <th class="bg-gray-100 border p-2">Старая цена (р)</th>
            <th class="bg-gray-100 border p-2">Цена (р)</th>
            <th class="bg-gray-100 border p-2">Вес (г)</th>
            <th class="bg-gray-100 border p-2">Объем</th>
            <th class="bg-gray-100 border p-2">Сортировка</th>
            <th class="bg-gray-100 border p-2">Скрыт</th>
          </tr>
          </thead>
          <tbody>
          @foreach($products as $product)
            <tr>
              <td class="border p-2">{{ $product->id }}</td>
              <td class="border p-2">
                <x-image-field-sm :id="'products['.$product->id.'][style_page][cardImage]'" :name="'products['.$product->id.'][style_page][cardImage]'" :file="$product->style_page['cardImage']['img'] ?? null" :thumb="$product->style_page['cardImage']['thumb'] ?? null" :working_dir="$product->workingDir"/>
              </td>
              <td class="border p-2 text-left  @if($product->hidden) opacity-40 @endif" style="max-width:220px">
                <textarea data-field-name="products[{{ $product->id }}][name]" class="edit_quantity bg-transparent p-0 border-0 text-xs w-full auto-height" style="resize: none; max-width: 200px;" autocomplete="off">{{ $product->name }}</textarea>
              </td>
              <td class="border p-2">
                <input type="text" class="edit_quantity bg-transparent p-0 border-0 w-14 text-xs" data-field-name="products[{{ $product->id }}][product_sku_id]" placeholder="артикул" value="{{ $product->product_sku?->name }}" autocomplete="off" style="max-width: 120px;">
              </td>
              <td class="border p-2">{!! $product->category ? $product->category->title : '<span class="text-slate-400">без категории</span>' !!}</td>
              <td class="border p-2">
                <input type="text" class="edit_quantity numeric-field bg-transparent p-0 border-0 w-14 text-xs" data-field-name="products[{{ $product->id }}][old_price]" placeholder="число" value="{{ $product->old_price }}" autocomplete="off" style="max-width: 120px;">
              </td>
              <td class="border p-2">
                <input type="text" class="edit_quantity numeric-field bg-transparent p-0 border-0 w-14 text-xs" data-field-name="products[{{ $product->id }}][price]" placeholder="число" value="{{ $product->price }}" autocomplete="off" style="max-width: 120px;">
              </td>
              <td class="border p-2">
                <input type="text" class="edit_quantity numeric-field bg-transparent p-0 border-0 w-14 text-xs" data-field-name="products[{{ $product->id }}][weight]" placeholder="число" value="{{ $product->weight }}" autocomplete="off" style="max-width: 120px;">
              </td>
              <td class="border p-2">
                <input type="text" class="edit_quantity bg-transparent p-0 border-0 w-14 text-xs" data-field-name="products[{{ $product->id }}][volume]" placeholder="мл" value="{{ $product->volume }}" autocomplete="off" style="max-width: 120px;">
              </td>
              <td class="border p-2">
                <input type="text" class="edit_quantity bg-transparent p-0 border-0 w-14 text-xs" data-field-name="products[{{ $product->id }}][order]" placeholder="0" value="{{ $product->order }}" autocomplete="off" style="max-width: 120px;">
              </td>
              <td class="border p-2">
                <input type="checkbox" class="edit_status mr-1" data-field-name="products[{{ $product->id }}][hidden]" data-id="status-{{ $product->id }}" value="1" @if($product->hidden){!! 'checked' !!}@endif>
                <input type="checkbox" style="opacity: 0;position: absolute;visibility: hidden;pointer-events: none;width: 1px;height: 1px;" class="off mr-1" data-field-name="products[{{ $product->id }}][hidden]" value="0" @if(!$product->hidden){!! 'checked' !!}@endif>
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </form>
    <div class="p-2">
      {{ $products->appends(request()->input())->links('pagination::tailwind') }}
    </div>
    <div class="text-right p-3 fixed bottom-0 w-full left-0" id="btn-update" style="display: none;">
      <x-primary-button type="submit" form="products-update">Обновить значения</x-primary-button>
    </div>
  </div>
</x-admin-layout>
