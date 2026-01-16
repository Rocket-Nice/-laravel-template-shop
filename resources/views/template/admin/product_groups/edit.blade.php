@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <div class="flex justify-end">
    <div class="flex flex-col md:flex-row -m-1">
      <a href="{{ route('admin.product-group.index') }}" class="m-1 button button-secondary">Группы товаров</a>
    </div>
  </div>
  <form action="{{ route('admin.product-group.update', $productGroup->id) }}" method="post">
    @csrf
    @method('PUT')
    <div class="mx-auto px-3 sm:px-4 lg:px-6 py-6">
      <div class="sm:w-[75%]">
        <div class="form-group">
          <x-input-label for="name" :value="__('Имя группы')"/>
          <x-text-input type="text" name="name" id="name" value="{{ old('name') ?? $productGroup->name }}" class="mt-1 block w-full" required/>
          <div class="hint">Латиница без пробелов</div>
        </div>
        <div class="form-group">
          <x-input-label for="description" :value="__('Описание')" />
          <x-textarea name="description" id="description" class="mt-1 block w-full">{{ old('description') ?? $productGroup->description }}</x-textarea>
        </div>
        <div class="form-group">
          <x-input-label for="content" :value="__('Связанные страницы')"/>
          <select name="content[]" id="content" multiple class="multipleSelect form-control">
            @foreach($contents as $content)
              <option value="{{ $content->id }}" data-keywords="{{ $content->route }}" @if($productGroup->pages()->where('content_id', $content->id)->exists()){!! 'selected' !!}@endif>{{ $content->id }}: {{ $content->title }} ({{ $content->route }})</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <x-input-label for="category_id" :value="__('Связанная категория')"/>
          <select id="category_id" name="category_id" class="multipleSelect form-control w-full">
            <option value="">Выбрать</option>
            @foreach($categories as $category)
              <option value="{{ $category->id }}" @if($productGroup->category_id==$category->id){{ 'selected' }}@endif>@if($category->parent){{ $category->parent->title }} > @endif{{ $category->title }}</option>
            @endforeach
          </select>
        </div>
        <script>
          window.productsGroup = @json($products);
          const getProducts = @json(route('admin.product-group.products'));
          const getProductsEvent = new CustomEvent('get-products-event', {})
          const updateProductsEvent = new CustomEvent('update-products-event', {})
        </script>
      </div>
      <div>
        <div class="form-group">
          <a href="javascript:;"  data-src="#products" data-fancybox onclick="window.dispatchEvent(getProductsEvent)" class="button button-success button-sm">Добавить товары</a>
          <div x-data="{
              products: [],
              init() {
                this.updateProducts();
              },
              updateProducts() {
                this.$nextTick(() => {
                    this.products = [...window.productsGroup];
                });
              },
              removeProduct(id){
                window.productsGroup = window.productsGroup.filter(product => product.id !== id);
                this.updateProducts();
              }
          }" @update-products-event.window="updateProducts()">
            <div class="form-group mt-4">
              <table class="w-full">
                <thead>
                <tr>
                  <th class="bg-gray-100 border p-px">ID</th>
                  <th class="bg-gray-100 border p-px">Наименование</th>
                  <th class="bg-gray-100 border p-px">Артикул</th>
                  <th class="bg-gray-100 border p-px">Категория</th>
                  <th class="bg-gray-100 border p-px">Сортировка</th>
                  <th class="bg-gray-100 border p-px"></th>
                </tr>
                </thead>
                <tbody>
                <template x-for="product in products" :key="product.id">
                  <tr>
                    <td class="border p-px"><label x-text="product.id"></label></td>
                    <td class="border p-px"><label x-text="product.name"></label></td>
                    <td class="border p-px text-center"><label x-text="product.sku"></label></td>
                    <td class="border p-px text-center"><label x-text="product.category ? product.category.title : ''"></label></td>
                    <td class="border p-px text-center">
                      <label :for="product.id+'order'">
                        <input type="text" :name="'products['+product.id+'][order]'" class="numeric-field bg-transparent p-0 border-0 w-full text-center text-sm" :id="product.id+'order'" :value="product.pivot.order">
                      </label>
                    </td>
                    <td class="border p-px">
                      <a href="#" class="text-gray-400 hover:text-gray-900" @click.prevent="removeProduct(product.id)">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" stroke-width="1.5">
                          <path d="M18 6l-12 12"></path>
                          <path d="M6 6l12 12"></path>
                        </svg>
                      </a>
                    </td>
                  </tr>
                </template>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="px-3 sm:px-4 lg:px-6 py-6 flex justify-end">
      <button class="button button-success">Сохранить</button>
    </div>
  </form>
  <div class="hidden">
    <div style="display: none;" id="products" class="w-full !max-w-5xl"
         x-data="{
            products: [],
            getCheckedProducts(){
              const selectedCheckboxes = document.querySelectorAll('#product-table .action:checked');
              const selectedIds = Array.from(selectedCheckboxes).map(cb => parseInt(cb.value));
              // Фильтруем массив продуктов
              const selectedProducts = this.products.filter(product => selectedIds.includes(product.id)).map(product => ({
                  ...product,
                  pivot: {order: ''}
              }));
              selectedProducts.forEach(product => {
                  if (!window.productsGroup.some(p => p.id === product.id)) {
                      window.productsGroup.push(product);
                  }
              });
              Fancybox.close();
              window.dispatchEvent(updateProductsEvent);
            },
            checkedAllProducts() {
              const status = this.$el.checked;
              const products = document.querySelectorAll('#product-table .action');

              products.forEach(product => {
                product.checked = status;
              });
            },
            async loadProducts() {
                try {
                    let url = getProducts;
                    let response_params = {
                      method: 'GET',
                      headers: {
                        'Content-Type': 'application/json'
                      }
                    };
                    let name = document.querySelector('#keyword').value;
                    let category = document.querySelector('#category').value;
                    let query = [];
                    if(name) query.push(`keyword=${name}`)
                    if(category) query.push(`category=${category}`)
                    if (query.length > 0) {
                      var separator = url.includes('?') ? '&' : '?';
                      url += (query.length ? separator + query.join('&') : '')
                    }
                    const response = await fetch(url, response_params);
                    if (!response.ok) throw new Error('Ошибка загрузки данных');
                    this.products = await response.json();
                    console.log('this.products', this.products);
                } catch (error) {
                    console.error('Ошибка:', error);
                }
            }
         }" @get-products-event.window="loadProducts()"
    >
      <div class="p-2 sm:p-4 md:p-6 lg:p-9">
        <div id="product-table" class="relative">
          <div class="font-bold mb-4">Добавить продукты</div>
          <div>
            <div class="p-1 w-full">
              <div class="form-group">
                <x-input-label for="keyword" :value="__('Наименоване или артикул')" />
                <x-text-input type="text" name="keyword" id="keyword" value="{{ request()->get('keyword') }}" class="mt-1 block w-full" @change="loadProducts()"/>
              </div>
            </div>
            <div class="p-1 w-full">
              <div class="form-group">
                <x-input-label for="category" :value="__('Категория')" />
                <select id="category" name="category" class="form-control w-full" @change="loadProducts()">
                  <option value="">Выбрать</option>
                  @foreach($categories as $category)
                    <option value="{{ $category->id }}">@if($category->parent){{ $category->parent->title }} > @endif{{ $category->title }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <table class="w-full">
              <thead>
              <tr>
                <th class="bg-gray-100 border p-px"><input type="checkbox" @click="checkedAllProducts()"></th>
                <th class="bg-gray-100 border p-px">ID</th>
                <th class="bg-gray-100 border p-px">Наименование</th>
                <th class="bg-gray-100 border p-px">Артикул</th>
                <th class="bg-gray-100 border p-px">Категория</th>
              </tr>
              </thead>
              <tbody>
              <template x-for="product in products" :key="product.id">
                <tr>
                  <td class="border p-px">
                    <input type="checkbox" :name="'products[]'" :id="product.id" :value="product.id" class="action">
                  </td>
                  <td class="border p-px"><label :for="product.id" x-text="product.id"></label></td>
                  <td class="border p-px"><label :for="product.id" x-text="product.name"></label></td>
                  <td class="border p-px text-center"><label :for="product.id" x-text="product.sku"></label></td>
                  <td class="border p-px text-center"><label :for="product.id" x-text="product.category ? product.category.title : ''"></label></td>
                </tr>
              </template>
              </tbody>
            </table>
          </div>
          <div class="py-6 flex justify-end sticky left-0 w-full bottom-0">
            <x-primary-button type="button" class="shadow-2xl" @click="getCheckedProducts()">Добавить</x-primary-button>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-admin-layout>

