@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <x-admin.search-form :route="route('admin.products.marketplaces')">
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="keyword" :value="__('Наименоване')" />
        <x-text-input type="text" name="keyword" id="keyword" value="{{ request()->get('keyword') }}" class="mt-1 block w-full" />
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="sku" :value="__('Артикул')" />
        <x-text-input type="text" name="sku" id="sku" value="{{ request()->get('sku') }}" class="mt-1 block w-full" />
      </div>
    </div>
    <div class="p-1 w-full">
      <div class="form-group">
        <x-input-label for="categories" :value="__('Категория')"/>
        <select id="categories" name="categories[]" multiple class="multipleSelect form-control w-full">
          <option value="">Выбрать</option>
          @foreach($categories as $category)
            <option
              value="{{ $category->id }}" @if(is_array(request()->get('categories'))&&in_array($category->id, request()->get('categories')))
              {!! 'selected' !!}
              @endif>@if($category->parent)
                {{ $category->parent->title }} >
              @endif{{ $category->title }}</option>
          @endforeach
        </select>
      </div>
    </div>
  </x-admin.search-form>
  <div class="mx-auto py-4">
    <form action="{{ route('admin.products.marketplaces.put') }}" id="update-products" method="POST"
          x-data="{show:false}" @change="show=true">
      @csrf
      @method('PUT')
      <div class="relative overflow-x-auto min-h-[500px]">
        <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
          <thead>
          <tr>
            <th class="bg-gray-100 border p-2">ID</th>
            <th class="bg-gray-100 border p-2">Наименоание</th>
            <th class="bg-gray-100 border p-2">Артикул</th>
            <th class="bg-gray-100 border p-2">Категория</th>
            <th class="bg-gray-100 border p-2">Производится</th>
            <th class="bg-gray-100 border p-2">Наличие на WB</th>
            <th class="bg-gray-100 border p-2">Наличие на ОЗОН</th>
            <th class="bg-gray-100 border p-2">Дата создания</th>
            <th class="bg-gray-100 border p-2">Комментарий</th>
          </tr>
          </thead>
          <tbody>
          @foreach($products as $k => $product)
            <tr class="@if(!($k & 1)) bg-gray-200 @endif">
              <td class="border p-2">{{ $product->id }}</td>
              <td class="border p-2 text-left  @if($product->hidden) opacity-40 @endif"
                  style="max-width:220px">{{ $product->name }}</td>
              <td class="border p-2">{{ $product->product_sku?->name }}</td>
              <td
                class="border p-2">{!! $product->category ? $product->category->title : '<span class="text-slate-400">без категории</span>' !!}</td>
              <td class="border p-2">
                <select data-name="products[{{ $product->id }}][is_producing]"
                        id="is_producing-{{ $product->id }}" class="td-field w-full text-center"
                        @change="$el.setAttribute('name', $el.dataset.name)">
                  <option value="" disabled @if($product->is_producing === null)
                    {{ 'selected' }}
                    @endif>Не указано
                  </option>
                  <option value="1" @if($product->is_producing === 1)
                    {{ 'selected' }}
                    @endif>Да
                  </option>
                  <option value="0" @if($product->is_producing === 0)
                    {{ 'selected' }}
                    @endif>Нет
                  </option>
                </select>
              </td>
              <td class="border p-2">
                <select data-name="products[{{ $product->id }}][in_stock_wb]"
                        id="in_stock_wb-{{ $product->id }}" class="td-field w-full text-center"
                        @change="$el.setAttribute('name', $el.dataset.name)">
                  <option value="" disabled @if($product->in_stock_wb === null)
                    {{ 'selected' }}
                    @endif>Не указано
                  </option>
                  @foreach(\App\Models\Product::MARKETPLACE_STATUS as $marketplace_status_id => $marketplace_status)
                    <option value="{{ $marketplace_status_id }}" @if($product->in_stock_wb === $marketplace_status_id)
                      {{ 'selected' }}
                      @endif>{{ $marketplace_status }}</option>
                  @endforeach
                </select>
              </td>
              <td class="border p-2">
                <select data-name="products[{{ $product->id }}][in_stock_ozon]"
                        id="in_stock_ozon-{{ $product->id }}" class="td-field w-full text-center"
                        @change="$el.setAttribute('name', $el.dataset.name)">
                  <option value="" disabled @if($product->in_stock_ozon === null)
                    {{ 'selected' }}
                    @endif>Не указано
                  </option>
                  @foreach(\App\Models\Product::MARKETPLACE_STATUS as $marketplace_status_id => $marketplace_status)
                    <option value="{{ $marketplace_status_id }}" @if($product->in_stock_ozon === $marketplace_status_id)
                      {{ 'selected' }}
                      @endif>{{ $marketplace_status }}</option>
                  @endforeach
                </select>
              </td>
              <td class="border p-2">{{ \Carbon\Carbon::create($product->created_at)->format('d.m.Y H:i') }}</td>
              <td class="border p-2">
                <textarea data-name="products[{{ $product->id }}][comment]" class="td-field w-full text-center"
                          @if($product->comment) title="{{ $product->comment }}"
                          @endif  @input="$el.setAttribute('name', $el.dataset.name)">{{ $product->comment }}</textarea>
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>

      <div class="button-panel flex justify-end items-center fixed bottom-0 right-0 w-full z-30 p-2 m-4 space-x-4"
           x-show="show">
        {{--        <a href="#" class="text-gray-400 text-sm no-underline" id="updateReportCancel">Отмена</a>--}}
        <button type="submit" class="button button-success shadow-xl">Сохранить</button>
      </div>
    </form>
    <div class="p-2">
      {{ $products->appends(request()->input())->links('pagination::tailwind') }}
    </div>
  </div>
</x-admin-layout>
