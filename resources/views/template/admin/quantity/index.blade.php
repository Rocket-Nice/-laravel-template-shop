@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <x-slot name="style">
    <style>
      table {
        position: relative;
      }
      thead tr:nth-child(1) th{
        background: white;
        position: sticky;
        top: 0;
        z-index: 10;
      }
    </style>
  </x-slot>
  <x-admin.search-form :route="route('admin.products.quantity')">
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
  </x-admin.search-form>

  <div class="mx-auto py-4">
    <form action="{{ route('admin.products.quantity.update') }}" method="POST">
      @csrf
      @method('PUT')
      <div class="relative overflow-x-auto min-h-[500px]">
        <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
          <thead>
          <tr>
            <th class="bg-gray-100 border p-2" style="width: 5%">#</th>
            <th class="bg-gray-100 border p-2 text-left" style="width: 30%">Наименоание</th>
            <th class="bg-gray-100 border p-2">
              <div class="flex items-center">
                @if(auth()->user()->hasPermissionTo('Управление наличием общее'))
                  <input type="checkbox" class="all_statuses mr-1" data-id-name="status">
                @endif
                <span>Общее</span>
              </div>
            </th>
{{--            @foreach($pickups as $pickup)--}}
{{--              <th class="bg-gray-100 border p-2">--}}
{{--                <div class="flex items-center">--}}
{{--                  @if(\Spatie\Permission\Models\Permission::where('name', $pickup->params['role'])->exists() && auth()->user()->hasPermissionTo($pickup->params['role']))--}}
{{--                    <input type="checkbox" class="all_statuses mr-1" data-id-name="{{ $pickup->params['status'] }}">--}}
{{--                  @endif--}}
{{--                  <span>{{ $pickup->params['short_name'] ?? $pickup->name }}</span>--}}
{{--                </div>--}}
{{--              </th>--}}
{{--            @endforeach--}}
            <th class="bg-gray-100 border p-2">
              <div class="flex items-center">
                <input type="checkbox" class="all_statuses mr-1" data-id-name="promotion">
                <span>Наличие в карточках</span>
              </div>
            </th>
          </tr>
          </thead>
          <tbody>
          @foreach($products as $product)
            <tr>
              <td class="border p-2">{{ $product->id }}</td>
              <td class="border p-2 text-left">
                {{ $product->name }}  {{ $product->volume }}<br/><span class="text-xs text-gray-400">{{ $product->product_sku?->name }}</span>{!! $product->preorder ? '<br/><span class="badge-orange text-xs">Предзаказ</span>' : '' !!}
              </td>
              <td class="border p-2" data-target="quantity[{{ $product->id }}]">
                @if(auth()->user()->hasPermissionTo('Управление наличием общее') && !$product->product_options)
                  <div class="form-group flex items-center">
                    <input type="checkbox" class="edit_status mr-1" data-field-name="status[{{ $product->id }}]" data-id="status" value="1" @if($product->status){!! 'checked' !!}@endif>
                    <input type="checkbox" style="opacity: 0;position: absolute;visibility: hidden;pointer-events: none;width: 1px;height: 1px;" class="off mr-1" data-field-name="status[{{ $product->id }}]" value="0" @if(!$product->status){!! 'checked' !!}@endif>
                    <input type="text" class="edit_quantity numeric-field bg-transparent p-0 border-0 w-14 text-sm" data-field-name="quantity[{{ $product->id }}]" placeholder="число" value="{{ $product->quantity }}" autocomplete="off" style="max-width: 120px;">
                  </div>
                @else
                  <span @if(!$product->status){!! 'style="opacity: 0.4"' !!}@endif>
                    {{ $product->quantity }}
                  </span>
                @endif
              </td>

{{--              @foreach($pickups as $pickup)--}}
{{--                <td class="border p-2" data-target="data_quantity[{{ $pickup->params['quantity'] }}][{{ $product->id }}]">--}}
{{--                  @if(\Spatie\Permission\Models\Permission::where('name', $pickup->params['role'])->exists() && auth()->user()->hasPermissionTo($pickup->params['role']) && !$product->product_options)--}}
{{--                    <div class="form-group flex items-center">--}}
{{--                      <input type="checkbox" class="edit_status mr-1" data-field-name="data_status[{{ $pickup->params['status'] }}][{{ $product->id }}]" data-id="{{ $pickup->params['status'] }}" value="1" @if(isset($product->data_status[$pickup->params['status']])&&$product->data_status[$pickup->params['status']]){!! 'checked' !!}@endif>--}}
{{--                      <input type="checkbox" style="opacity: 0;position: absolute;visibility: hidden;pointer-events: none;width: 1px;height: 1px;" class="off mr-1" data-field-name="data_status[{{ $pickup->params['status'] }}][{{ $product->id }}]" value="0" @if(!isset($product->data_status[$pickup->params['status']])||!$product->data_status[$pickup->params['status']]){!! 'checked' !!}@endif>--}}
{{--                      <input type="text" class="edit_quantity numeric-field bg-transparent p-0 border-0 w-14 text-sm" data-field-name="data_quantity[{{ $pickup->params['quantity'] }}][{{ $product->id }}]" placeholder="число" autocomplete="off" value="{{ $product->data_quantity[$pickup->params['quantity']] ?? 0 }}" style="max-width: 120px;">--}}
{{--                    </div>--}}
{{--                  @else--}}
{{--                    <span @if(!isset($product->data_status[$pickup->params['status']])||!$product->data_status[$pickup->params['status']]){!! 'style="opacity: 0.4"' !!}@endif>--}}
{{--                        {{ $product->data_quantity[$pickup->params['quantity']] ?? 0 }}--}}
{{--                      </span>--}}
{{--                  @endif--}}
{{--                </td>--}}
{{--              @endforeach--}}
              <td class="border p-2" data-target="data_quantity[promotion][{{ $product->id }}]">
                <div class="form-group flex items-center">
                  <input type="checkbox" class="edit_status mr-1" data-field-name="data_status[promotion][{{ $product->id }}]" data-id="promotion" value="1" @if(isset($product->data_status['promotion'])&&$product->data_status['promotion']){!! 'checked' !!}@endif>
                  <input type="checkbox" style="opacity: 0;position: absolute;visibility: hidden;pointer-events: none;width: 1px;height: 1px;" class="off mr-1" data-field-name="data_status[promotion][{{ $product->id }}]" value="0" @if(!isset($product->data_status['promotion'])||!$product->data_status['promotion']){!! 'checked' !!}@endif>
                  <input type="text" class="edit_quantity numeric-field bg-transparent p-0 border-0 w-14 text-sm" data-field-name="data_quantity[promotion][{{ $product->id }}]" placeholder="число" autocomplete="off" value="{{ $product->data_quantity['promotion'] ?? 0 }}" style="max-width: 120px;">
                </div>
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
      <div class="text-right p-3" id="btn-update" style="display: none;">
        <x-primary-button type="submit">Обновить значения</x-primary-button>
      </div>
    </form>

    <div class="p-2">
      {{ $products->appends(request()->input())->links('pagination::tailwind') }}
    </div>
  </div>
</x-admin-layout>
