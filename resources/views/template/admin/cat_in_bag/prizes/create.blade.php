@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  @php
    $productsForSelect = $products->map(function ($product) {
      return [
        'id' => $product->id,
        'name' => $product->name,
        'sku' => $product->sku,
        'type_id' => $product->type_id,
      ];
    })->values();
    $selectedProductId = old('product_id');
    $selectedTypeId = old('product_type_id');
    if ($selectedTypeId === null && $selectedProductId) {
      $selectedTypeId = optional($products->firstWhere('id', $selectedProductId))->type_id;
    }
  @endphp
  <x-slot name="script">
    <script>
      window.filemanger.working_dir = @json($working_dir);
    </script>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.getElementById('product_type_id');
        const productSelect = document.getElementById('product_id');
        if (!typeSelect || !productSelect) {
          return;
        }

        const products = @json($productsForSelect);

        const ensureChoices = () => {
          if (productSelect._choices) {
            return productSelect._choices;
          }
          if (!window.Choices) {
            return null;
          }
          const options = {
            shouldSort: false,
            removeItemButton: true,
            noChoicesText: 'Пусто',
            itemSelectText: 'Выбрать'
          };
          const instance = new Choices(productSelect, options);
          productSelect._choices = instance;
          return instance;
        };

        const updateProducts = () => {
          const instance = ensureChoices();
          if (!instance) {
            return;
          }
          const selectedId = productSelect.value;
          const typeId = typeSelect.value;
          const filtered = products.filter((product) => !typeId || String(product.type_id) === String(typeId));
          const hasSelected = filtered.some((product) => String(product.id) === String(selectedId));
          const choices = filtered.map((product) => ({
            value: String(product.id),
            label: product.name + ' (' + product.sku + ')',
            selected: hasSelected && String(product.id) === String(selectedId),
            customProperties: { keywords: product.sku }
          }));

          instance.clearChoices();
          instance.setChoices(
            [
              { value: '', label: 'Выбрать', selected: !hasSelected, disabled: true, placeholder: true },
              ...choices
            ],
            'value',
            'label',
            true
          );

          if (!hasSelected) {
            productSelect.value = '';
          }
        };

        updateProducts();
        typeSelect.addEventListener('change', updateProducts);
      });
    </script>
  </x-slot>
  <form action="{{ route('admin.cat-in-bag.prizes.store') }}" method="post">
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
            <x-input-label for="prizeImage" class="mb-2" :value="__('Изображение в каталоге')" />
            <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
              <div id="lfm-preview-prizeImage" class="rounded-md overflow-hidden w-20" data-name="image">
                @if(old('image'))
                  <input type="hidden" name="image[img]" id="input-lfm-image" value="{{ old('image')['img'] ?? '' }}">
                  <input type="hidden" name="image[thumb]" id="input-lfm-thumb-image" value="{{ old('image')['thumb'] ?? old('image')['img'] ?? '' }}">
                  <a href="javascript:;" data-fancybox="true" data-src="{{ old('image')['img'] ?? '' }}" style="display: block;">
                    <img src="{{ old('image')['thumb'] ?? old('image')['img'] ?? '' }}" class="overflow-hidden max-w-full object-cover object-center">
                  </a>
                @endif
              </div>
              <div class="mb-2">
                <div class="mb-2 text-gray-500 text-sm"></div>
                <button
                  type="button"
                  id="prizeImage"
                  class="button button-secondary"
                  data-lfm="image"
                  data-preview="lfm-preview-prizeImage">Выбрать изображение</button>
              </div>
            </div>
          </div>
          <div class="form-group">
            <x-input-label for="name" :value="__('Наименование')" />
            <x-text-input type="text" name="name" id="name" value="{{ old('name') }}" class="mt-1 block w-full" required />
          </div>
          <div class="form-group">
            <x-input-label for="total_qty" :value="__('Общее количество')" />
            <x-text-input type="number" name="total_qty" id="total_qty" value="{{ old('total_qty') }}" class="mt-1 block w-full" min="0" required />
          </div>
          <div class="form-group">
            <x-input-label for="category_id" :value="__('Категория')" />
            <select id="category_id" name="category_id" class="form-control w-full" required>
              <option value="">Выбрать</option>
              @foreach($categories as $category)
                <option value="{{ $category->id }}" @if(old('category_id') == $category->id) selected @endif>{{ $category->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <x-input-label for="product_type_id" :value="__('Тип товара')" />
            <select id="product_type_id" name="product_type_id" class="form-control w-full">
              <option value="">Выбрать</option>
              @foreach($productTypes as $productType)
                <option value="{{ $productType->id }}" @if($selectedTypeId == $productType->id) selected @endif>{{ $productType->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <x-input-label for="product_id" :value="__('Товар из магазина')" />
            <select id="product_id" name="product_id" class="multipleSelect form-control w-full" data-keywords="1" required>
              <option value="">Выбрать</option>
              @foreach($products as $product)
                <option value="{{ $product->id }}" data-keywords="{{ $product->sku }}" data-type="{{ $product->type_id }}" @if(old('product_id') == $product->id) selected @endif>{{ $product->name }} ({{ $product->sku }})</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <div class="flex items-center">
              <x-checkbox id="is_enabled" name="is_enabled" value="1"
                          :checked="old('is_enabled', true) ? true : false"/>
              <x-input-label for="is_enabled" class="ml-2" :value="__('Включен')"/>
            </div>
          </div>
          <div class="form-group">
            <div class="flex items-center">
              <x-checkbox id="is_golden" name="is_golden" value="1"
                          :checked="old('is_golden') ? true : false"/>
              <x-input-label for="is_golden" class="ml-2" :value="__('Дарится в золотом мешке')"/>
            </div>
          </div>
          <div class="form-group">
            <div class="flex items-center">
              <x-checkbox id="is_certificate" name="is_certificate" value="1"
                          :checked="old('is_certificate') ? true : false"/>
              <x-input-label for="is_certificate" class="ml-2" :value="__('Сертификат')"/>
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
