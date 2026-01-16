@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <x-slot name="script">
    <input type="hidden" name="product_ids" id="product_ids" value="{{ json_encode($products->pluck('id')->toArray()) }}">
    <input type="hidden" name="request" id="request" value="{{ json_encode(request()->toArray()) }}">
    <script>
      // planning
      const dataName = document.querySelectorAll('input[data-name],textarea[data-name],select[data-name]');
      if(dataName.length){
        dataName.forEach((field) => {
          field.addEventListener('input', (event) => {
            const field = event.target
            const fieldName = field.dataset.name

            field.name = fieldName;
          })
        })
      }
      const commonFields = document.querySelectorAll('input[data-common-field],textarea[data-common-field],select[data-common-field]');
      if(commonFields.length){
        commonFields.forEach((field) => {
          field.addEventListener('change', (event) => {
            const field = event.target
            const fieldName = field.dataset.field

            const childFields = document.querySelectorAll('input[data-field="'+fieldName+'"]:not([data-common-field]),textarea[data-field="'+fieldName+'"]:not([data-common-field]),select[data-field="'+fieldName+'"]:not([data-common-field])')
            if(childFields.length){
              let input = new Event('input');
              childFields.forEach((child) => {
                child.value = field.value
                child.dispatchEvent(input)
              })
            }
          })
        })
      }
      // end
    </script>
  </x-slot>
  <x-admin.search-form :route="route('admin.products.statistic')">
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="date_from" :value="__('Дата от')"/>
        <x-text-input type="text" name="date_from" id="date_from" value="{{ request()->get('date_from') ?? now()->startOfMonth()->format('d.m.Y H:i') }}" data-minDate="false" class="mt-1 block w-full datepicker"/>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="date_until" :value="__('Дата до')"/>
        <x-text-input type="text" name="date_until" id="date_until" value="{{ request()->get('date_to') ?? now()->format('d.m.Y H:i') }}" data-minDate="false" class="mt-1 block w-full datepicker"/>
      </div>
    </div>
    <div class="p-1 w-full">
      <div class="form-group">
        <x-input-label for="keyword" :value="__('Наименоване или артикул')" />
        <x-text-input type="text" name="keyword" id="keyword" value="{{ request()->get('keyword') }}" class="mt-1 block w-full" />
      </div>
    </div>
    <div class="p-1 w-full">
      <div class="form-group">
        <x-input-label for="filter-category" :value="__('Категория')"/>
        <select id="filter-category" name="categories[]" multiple class="multipleSelect form-control">
          @foreach($categories as $category)
            <option value="{{ $category->id }}" @if(is_array(request()->get('categories'))&&in_array($category->id, request()->get('categories'))){!! 'selected' !!}@endif>{{ $category->id }}: {{ $category->title }}</option>
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
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
          <thead>
            <tr>
              <th class="bg-gray-100 border p-2">#</th>
              <th class="bg-gray-100 border p-2">Наименоание</th>
              <th class="bg-gray-100 border p-2">Артикул</th>
              <th class="bg-gray-100 border p-2" style="width: 15%">Оформление</th>
              <th class="bg-gray-100 border p-2" style="width: 15%">Продано</th>
              <th class="bg-gray-100 border p-2" style="width: 15%">На сумму</th>
            </tr>
          </thead>
        <tbody>
        @foreach($products as $product)
          <tr>
            <td class="border p-2">{{ $product->id }}</td>
            <td class="border p-2 text-left" style="max-width:220px">{{ $product->name }}</td>
            <td class="border p-2">{{ $product->sku }}</td>
            <td class="border p-2">
              <span id="count-{{ $product->id }}">{{ $orderItems->where('product_id', $product->id)->first()->count ?? 0 }}</span></td>
            <td class="border p-2">
              <span id="count-sold-{{ $product->id }}">{{ $orderSoldItems->where('product_id', $product->id)->first()->count ?? 0 }}</span>
              <br/><span class="text-xs">подарки: <span id="gifts-{{ $product->id }}">{{ $orderGifts->where('product_id', $product->id)->first()->gifts ?? 0 }}</span></span></td>
            <td class="border p-2">
              <span id="total-{{ $product->id }}">{{ formatPrice(($orderSoldItems->where('product_id', $product->id)->first()->count ?? 0) * ($orderSoldItems->where('product_id', $product->id)->first()->price ?? 0)) }}</span>
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


