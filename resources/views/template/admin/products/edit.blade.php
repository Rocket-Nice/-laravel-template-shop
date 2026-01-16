@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <form action="{{ route('admin.products.update', $product->slug) }}" method="post">
    @csrf
    @method('PUT')
    <div class="border-b">
      <div class="mx-auto px-2 sm:px-3 lg:px-4">
        <nav class="-mb-px flex flex-col sm:flex-row sm:justify-start space-y-2 sm:space-y-0 sm:space-x-4"
             aria-label="Tabs" role="tablist">
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-1" aria-selected="true" role="tab" aria-controls="tab-1-content">Общие данные
          </button>
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-stickers" aria-selected="true" role="tab" aria-controls="tab-stickers-content">Доп информация
          </button>
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-3" role="tab" aria-controls="tab-3-content">Категории и тип
          </button>
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-2" role="tab" aria-controls="tab-2-content">Опции товара
          </button>
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-4" role="tab" aria-controls="tab-4-content">Промоакции
          </button>
          <a href="{{ route('admin.products.editDesign', $product->slug) }}" class="whitespace-nowrap no-underline py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500">Редактировать дизайн <i class="fas fa-external-link-alt"></i></a>
        </nav>
      </div>
    </div>

    <div class="mx-auto px-2 sm:px-3 lg:px-4 py-6" id="tab-content">
      <div id="tab-1-content" role="tabpanel">
        <div class="sm:w-[50%]">
          <div class="form-group">
            <x-input-label for="product-name" :value="__('Наименование')" />
            <x-text-input type="text" name="name" id="product-name" value="{{ old('name') ?? $product->name }}" class="mt-1" required />
          </div>
          <div class="form-group">
            <x-input-label for="product-sku" :value="__('Артикул')" />
            <x-text-input type="text" name="sku" id="product-sku" value="{{ old('sku') ?? $product->product_sku?->name }}" class="mt-1" required />
          </div>
          <div class="form-group">
            <x-input-label for="product-old-price" :value="__('Старая цена')" />
            <x-text-input type="text" name="old_price" id="product-old-price" value="{{ old('old_price') ?? $product->old_price }}" class="mt-1 numeric-field" />
          </div>
          <div class="form-group">
            <x-input-label for="product-price" :value="__('Цена')" />
            <x-text-input type="text" name="price" id="product-price" value="{{ old('price') ?? $product->price }}" class="mt-1 numeric-field" required />
          </div>
          <div class="form-group">
            <x-input-label for="product-volume" :value="__('Объем')" />
            <x-text-input type="text" name="volume" id="product-volume" value="{{ old('volume') ?? $product->volume }}" class="mt-1" />
          </div>
          <div class="form-group">
            <x-input-label for="product-weight" :value="__('Вес')" />
            <x-text-input type="text" name="weight" id="product-weight" value="{{ old('weight') ?? $product->weight }}" class="mt-1 numeric-field" />
          </div>
          <div class="form-group">
            <x-input-label for="product-tnved" :value="__('ТН ВЭД ЕАЭС')" />
            <x-text-input type="text" name="tnved" id="product-tnved" value="{{ old('tnved') ?? $product->tnved }}" class="mt-1" />
          </div>
          <div class="form-group">
            <x-input-label for="product-order" :value="__('Приоритет сортировки')" />
            <x-text-input type="text" name="order" id="product-order" value="{{ old('order') ?? $product->order }}" class="numeric-field mt-1" />
            <div class="hint">Чем больше число, тем раньше в списке</div>
          </div>
          <div class="form-group">
            <div class="flex items-center">
              <x-checkbox id="soon" name="options[soon]" value="1"
                          :checked="(old('options')['soon'] ?? false) || ($product->options['soon'] ?? false) ? true : false"/>
              <x-input-label for="soon" class="ml-2" :value="__('Скоро в наличии')"/>
            </div>
          </div>
          <div class="form-group">
            <div class="flex items-center">
              <x-checkbox id="hidden" name="hidden" value="1"
                          :checked="old('hidden') || $product->hidden ? true : false"/>
              <x-input-label for="hidden" class="ml-2" :value="__('Товар скрыт')"/>
            </div>
          </div>

          <div class="form-group">
            <x-input-label for="product-keywords" :value="__('Ключевые слова для поиска')" />
            <x-textarea type="text" name="keywords" id="product-keywords" class="w-full mt-1">{{ old('keywords') ?? $product->keywords }}</x-textarea>
          </div>
        </div>
      </div>
      <div id="tab-stickers-content" role="tabpanel">
        <div class="form-group">
          <div class="flex items-center">
            <x-checkbox id="new_structure" name="options[new_structure]" value="1"
                        :checked="(old('options')['new_structure'] ?? false) || ($product->options['new_structure'] ?? false) ? true : false"/>
            <x-input-label for="new_structure" class="ml-2" :value="__('Новый состав')"/>
          </div>
        </div>
        <div class="form-group">
          <div class="flex items-center">
            <x-checkbox id="only_pickup" name="options[only_pickup]" value="1"
                        :checked="(old('options')['only_pickup'] ?? false) || ($product->options['only_pickup'] ?? false) ? true : false"/>
            <x-input-label for="only_pickup" class="ml-2" :value="__('Доступен только для самовывоза')"/>
          </div>
        </div>
        <div class="form-group">
          <div class="flex items-center">
            <x-checkbox id="gold_coupon" name="options[gold_coupon]" value="1"
                        :checked="(old('options')['gold_coupon'] ?? false) || ($product->options['gold_coupon'] ?? false) ? true : false"/>
            <x-input-label for="gold_coupon" class="ml-2" :value="__('Золотой билет')"/>
          </div>
        </div>
        <div class="form-group">
          <div class="flex items-center">
            <x-checkbox id="preorder" name="preorder" value="1"
                        :checked="old('preorder') || $product->preorder ? true : false"/>
            <x-input-label for="preorder" class="ml-2" :value="__('Предзаказ')"/>
          </div>
        </div>

        <div class="form-group">
          <div class="flex items-center">
            <x-checkbox id="is_new" name="options[is_new]" value="1"
                        :checked="(old('options')['is_new'] ?? false) || ($product->options['is_new'] ?? false) ? true : false"/>
            <x-input-label for="is_new" class="ml-2" :value="__('Новинка')"/>
          </div>
        </div>
        <div class="form-group">
          <div class="flex items-center">
            <x-checkbox id="sale" name="options[sale]" value="1"
                        :checked="(old('options')['sale'] ?? false) || ($product->options['sale'] ?? false) ? true : false"/>
            <x-input-label for="sale" class="ml-2" :value="__('SALE')"/>
          </div>
        </div>
        <div class="form-group">
          <div class="flex items-center">
            <x-checkbox id="tag20" name="options[tag20]" value="1"
                        :checked="(old('options')['tag20'] ?? false) || ($product->options['tag20'] ?? false) ? true : false"/>
            <x-input-label for="tag20" class="ml-2" :value="__('-20%')"/>
          </div>
        </div>
        <div class="form-group">
          <div class="flex items-center">
            <x-checkbox id="tag30" name="options[tag30]" value="1"
                        :checked="(old('options')['tag30'] ?? false) || ($product->options['tag30'] ?? false) ? true : false"/>
            <x-input-label for="tag30" class="ml-2" :value="__('-30%')"/>
          </div>
        </div>
        <div class="form-group">
          <div class="flex items-center">
            <x-checkbox id="tag50" name="options[tag50]" value="1"
                        :checked="(old('options')['tag50'] ?? false) || ($product->options['tag50'] ?? false) ? true : false"/>
            <x-input-label for="tag50" class="ml-2" :value="__('-50%')"/>
          </div>
        </div>

      </div>
      <div id="tab-3-content" role="tabpanel">
        <div class="form-group">
          <x-input-label for="category_id" :value="__('Родительская категория')" />
          <select id="category_id" name="category_id" class="form-control w-full">
            <option value="">Выбрать</option>
            @foreach($categories as $category)
              <option value="{{ $category->id }}" @if($product->category_id==$category->id){{ 'selected' }}@endif>@if($category->parent){{ $category->parent->title }} > @endif{{ $category->title }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <x-input-label for="roles" :value="__('Дополнительные категории')" />
          <select id="role" name="categories[]" multiple class="multipleSelect form-control">
            <option value="">Выбрать</option>
            @foreach($categories as $category)
              <option value="{{ $category->id }}" @if($product->hasCategory($category->id)){{ 'selected' }}@endif>@if($category->parent){{ $category->parent->title }} > @endif{{ $category->title }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <x-input-label for="type_id" :value="__('Тип товара')" />
          <select id="type_id" name="type_id" class="form-control w-full">
            <option value="">Выбрать</option>
            @foreach($product_types as $product_type)
              <option value="{{ $product_type->id }}" @if($product->type_id==$product_type->id){{ 'selected' }}@endif>{{ $product_type->name }}</option>
            @endforeach
          </select>
        </div>
        @if($product->type_id == 9)
          <div class="form-group">
            <x-input-label for="main_product_id" :value="__('Основной товар')" />
            <select id="main_product_id" name="main_product_id" class="form-control w-full">
              <option value="">Выбрать</option>
              @foreach($mainProducts as $mainProduct)
                <option value="{{ $mainProduct->id }}" @if($product->main_product_id==$mainProduct->id){{ 'selected' }}@endif>{{ $mainProduct->name }}</option>
              @endforeach
            </select>
          </div>
        @endif
      </div>
      <div id="tab-4-content" role="tabpanel">
        <div class="form-group">
          <div class="flex items-center">
            <x-checkbox id="puzzles" name="options[puzzles]" value="1"
                        :checked="(old('options')['puzzles'] ?? false) || ($product->options['puzzles'] ?? false) ? true : false"/>
            <x-input-label for="puzzles" class="ml-2" :value="__('Добавлять пазлы к товару')"/>
          </div>
        </div>
        <div class="form-group">
          <x-input-label for="product-puzzles_count" :value="__('Количество пазлов')" />
          <x-text-input type="text" name="options[puzzles_count]" id="product-puzzles_count" value="{{ old('options')['puzzles_count'] ?? $product->options['puzzles_count'] ?? '' }}" class="numeric-field mt-1" />
        </div>
      </div>
      <div id="tab-2-content" role="tabpanel">
        <div class="carousel-contructor mb-5">
          <div class="flex justify-between items-end">
            <x-input-label for="productSize" class="mb-2" :value="__('Размеры')"/>
            <button type="button" id="productSize" class="addSlide button button-success button-sm mb-2"
                    data-id="productSize">Добавить размер
            </button>
          </div>
          <div id="productSize_donor" style="display: none;">
            <x-admin.content.home-slider-1>
              <div class="form-group">
                <x-input-label :value="__('Связанный товар')"/>
                <select data-name="product" data-field="product_options" class="form-control w-full" disabled>
                  @foreach($productsOption as $option)
                    <option value="{{ $option->id }}">{{ $option->sku.': ' }}{{ $option->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group">
                <x-input-label :value="__('Наименование')"/>
                <x-text-input type="text" data-name="name" data-field="product_options" class="mt-1 block w-full"
                              disabled/>
              </div>
            </x-admin.content.home-slider-1>
          </div>
          <div class="carousel space-y-3 mb-1 p-2 border-gray-300 rounded-md border">
            @if(isset($product->product_options['productSize']))
              @foreach($product->product_options['productSize'] as $index => $elem)
                <x-admin.content.home-slider-1>
                  <div class="form-group">
                    <x-input-label for="product_options-productSize-{{ $index }}-icon" :value="__('Связанный товар')"/>
                    <select name="product_options[productSize][{{ $index }}][product]"
                            id="product_options-productSize-{{ $index }}-product" data-name="product"
                            data-field="product_options" class="form-control form-control w-full">
                      @foreach($productsOption as $option)
                        <option value="{{ $option->id }}" @if($elem['product'] == $option->id) selected @endif>{{ $option->sku.': ' }}{{ $option->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="form-group">
                    <x-input-label for="product_options-productSize-{{ $index }}-name" :value="__('Текст')"/>
                    <x-text-input name="product_options[productSize][{{ $index }}][name]"
                                  id="product_options-productSize-{{ $index }}-name" type="text" data-name="name"
                                  data-field="product_options" class="mt-1 block w-full" value="{{ $elem['name'] }}"/>
                  </div>
                </x-admin.content.home-slider-1>
              @endforeach
            @endif
          </div>
        </div>
      </div>
    </div>
    <div class="px-3 sm:px-4 lg:px-6 py-6 flex justify-end">
      <x-primary-button>Сохранить</x-primary-button>
    </div>
  </form>
</x-admin-layout>
