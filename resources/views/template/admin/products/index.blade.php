<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
      <a href="{{ route('admin.products.create') }}" class="button button-success">Создать товар</a>
    @endif
  </x-slot>
  <x-search-form :route="route('admin.products.index')">
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
{{--    <div class="p-1 w-full">--}}
{{--      <div class="form-group">--}}
{{--        <x-input-label for="main_category" :value="__('Основная категория')" />--}}
{{--        <select id="main_category" name="main_category[]" multiple class="multipleSelect form-control w-full">--}}
{{--          <option value="">Выбрать</option>--}}
{{--          @foreach($categories as $category)--}}
{{--            <option value="{{ $category->id }}" @if(is_array(request()->get('main_category'))&&in_array($category->id, request()->get('main_category'))){!! 'selected' !!}@endif>@if($category->parent){{ $category->parent->title }} > @endif{{ $category->title }}</option>--}}
{{--          @endforeach--}}
{{--        </select>--}}
{{--      </div>--}}
{{--    </div>--}}
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
    <div class="p-1 w-full">
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
  </x-search-form>
  <div class="mx-auto py-4">
    @if(auth()->user()->hasPermissionTo('Выгрузка продуктов'))
      <div class="flex justify-between py-2">
        <div>Всего {{ formatPrice($products->total(), false, '') }}</div>
        <form action="{{ route('admin.products.export')  }}{{ stristr($_SERVER['REQUEST_URI'], '?') }}" id="export-products" method="POST">
          @csrf
        </form>
        <x-dropdown_menu>
          <x-slot name="content">
            <div class="py-1" role="none">

              @if(auth()->user()->hasPermissionTo('Выгрузка продуктов'))
                <a class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1" href="#" onclick="if(confirm('Выгрузить в Excel выбранные товары?'))document.getElementById('export-products').submit();">
                  Выгрузить в Excel
                </a>
                <a class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1" href="{{ route('admin.export_data.index') }}">
                  Открыть выгруженные файлы
                </a>
              @endif

            </div>
          </x-slot>
        </x-dropdown_menu>
      </div>
    @endif
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
        <thead>
          <tr>
            <th class="bg-gray-100 border p-2">#</th>
            <th class="bg-gray-100 border p-2">Наименоание</th>
            <th class="bg-gray-100 border p-2">Артикул</th>
            <th class="bg-gray-100 border p-2">Категория</th>
            <th class="bg-gray-100 border p-2">Цена</th>
            <th class="bg-gray-100 border p-2">Вес</th>
            <th class="bg-gray-100 border p-2">Объем</th>
            <th class="bg-gray-100 border p-2">Сортировка</th>
            <th class="bg-gray-100 border p-2" style="width:60px"></th>
          </tr>
        </thead>
        <tbody>
        @foreach($products as $product)
          <tr>
            <td class="border p-2">{{ $product->id }}</td>
            <td class="border p-2 text-left @if($product->hidden) opacity-40 @endif" style="max-width:220px">
              {{ $product->name }}{!! $product->preorder ? '<br/><span class="badge-orange text-xs">Предзаказ</span>' : '' !!}
              {!! ($product->options['puzzles'] ?? false) ? '<br/><span class="badge-orange text-xs">'.denum($product->options['puzzles_count'] ?? 0, ['%d пазл','%d пазла','%d пазлов']).'</span>' : '' !!}
            </td>
            <td class="border p-2">{{ $product->product_sku?->name }}</td>
            <td class="border p-2">{!! $product->category ? $product->category->title : '<span class="text-slate-400">без категории</span>' !!}</td>
            <td class="border p-2">@if($product->old_price&&$product->old_price>$product->price)<span class="whitespace-nowrap line-through text-slate-400">{{ formatPrice($product->old_price) }}</span><br/>@endif<span class="whitespace-nowrap">{{ formatPrice($product->price) }}</span></td>
            <td class="border p-2">{{ $product->weight ? $product->weight.' г.' : '' }}</td>
            <td class="border p-2">{{ $product->volume }}</td>
            <td class="border p-2">{{ $product->order }}</td>
            <td class="border p-2 text-right">

              <x-dropdown_menu>
                <x-slot name="content">
                  <div class="py-1" role="none">
                    <a href="{{ route('admin.products.edit', $product->slug)  }}" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1">Редактировать товар</a>
                    <a href="{{ route('admin.products.editDesign', $product->slug)  }}" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1">Редактировать дизайн</a>
                    <a href="{{ route('admin.products.create', ['copy' => $product->slug])  }}" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1">Копировать товар</a>
                  </div>
                </x-slot>
              </x-dropdown_menu>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    <div class="p-2">
      {{ $products->appends(request()->input())->links('pagination::tailwind') }}
    </div>
  </div>
</x-admin-layout>
