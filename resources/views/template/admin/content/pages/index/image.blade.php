<div class="form-group">

  <x-input-label for="firstImage" class="mb-2" :value="__('Фон для заголовка')" />

  <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
    <div id="lfm-preview-firstImage" class="rounded-md overflow-hidden w-20" data-name="image_data[firstImage]">
      @if(isset($content->image_data['firstImage']['img']))
        <input type="hidden" name="image_data[firstImage][img]" id="input-lfm-firstImage" value="{{ old('image_data')['firstImage']['img'] ?? $content->image_data['firstImage']['img'] ?? '' }}">
        <input type="hidden" name="image_data[firstImage][thumb]" id="input-lfm-thumb-firstImage" value="{{ old('image_data')['firstImage']['thumb'] ?? $content->image_data['firstImage']['thumb'] ?? '' }}">

        <a href="javascript:;" data-fancybox="true" data-src="{{ $content->image_data['firstImage']['img'] }}" style="display: block;">
          <img src="{{ $content->image_data['firstImage']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
        </a>
      @endif
    </div>
    <div class="mb-2">
      <div class="mb-2 text-gray-500 text-sm"></div>
      <button
        type="button"
        id="firstImage"
        class="button button-secondary"
        data-lfm="image"
        data-preview="lfm-preview-firstImage">Выбрать изображение</button>
    </div>

  </div>
</div>
<div class="form-group">

  <x-input-label for="activeComponentsImage" class="mb-2" :value="__('Фон для блока с активными компонентами')" />

  <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
    <div id="lfm-preview-activeComponentsImage" class="rounded-md overflow-hidden w-20" data-name="image_data[activeComponentsImage]">
      @if(isset($content->image_data['activeComponentsImage']['img']))
        <input type="hidden" name="image_data[activeComponentsImage][img]" id="input-lfm-activeComponentsImage" value="{{ old('image_data')['activeComponentsImage']['img'] ?? $content->image_data['activeComponentsImage']['img'] ?? '' }}">
        <input type="hidden" name="image_data[activeComponentsImage][thumb]" id="input-lfm-thumb-activeComponentsImage" value="{{ old('image_data')['activeComponentsImage']['thumb'] ?? $content->image_data['activeComponentsImage']['thumb'] ?? '' }}">

        <a href="javascript:;" data-fancybox="true" data-src="{{ $content->image_data['activeComponentsImage']['img'] }}" style="display: block;">
          <img src="{{ $content->image_data['activeComponentsImage']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
        </a>
      @endif
    </div>
    <div class="mb-2">
      <div class="mb-2 text-gray-500 text-sm">557x210px</div>
      <button
        type="button"
        id="activeComponentsImage"
        class="button button-secondary"
        data-lfm="image"
        data-preview="lfm-preview-activeComponentsImage">Выбрать изображение</button>
    </div>

  </div>
</div>
<div class="form-group">

  <x-input-label for="categoriesBigImage" class="mb-2" :value="__('Категории, большое фото')" />

  <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
    <div id="lfm-preview-categoriesBigImage" class="rounded-md overflow-hidden w-20" data-name="image_data[categoriesBigImage]">
      @if(isset($content->image_data['categoriesBigImage']['img']))
        <input type="hidden" name="image_data[categoriesBigImage][img]" id="input-lfm-categoriesBigImage" value="{{ old('image_data')['categoriesBigImage']['img'] ?? $content->image_data['categoriesBigImage']['img'] ?? '' }}">
        <input type="hidden" name="image_data[categoriesBigImage][thumb]" id="input-lfm-thumb-categoriesBigImage" value="{{ old('image_data')['categoriesBigImage']['thumb'] ?? $content->image_data['categoriesBigImage']['thumb'] ?? '' }}">

        <a href="javascript:;" data-fancybox="true" data-src="{{ $content->image_data['categoriesBigImage']['img'] }}" style="display: block;">
          <img src="{{ $content->image_data['categoriesBigImage']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
        </a>
      @endif
    </div>
    <div class="mb-2">
      <div class="mb-2 text-gray-500 text-sm">468x690px</div>
      <button
        type="button"
        id="categoriesBigImage"
        class="button button-secondary"
        data-lfm="image"
        data-preview="lfm-preview-categoriesBigImage">Выбрать изображение</button>
    </div>
  </div>
</div>
<div class="form-group">

  <x-input-label for="categoriesSmallImage" class="mb-2" :value="__('Категории, маленькое фото')" />

  <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
    <div id="lfm-preview-categoriesSmallImage" class="rounded-md overflow-hidden w-20" data-name="image_data[categoriesSmallImage]">
      @if(isset($content->image_data['categoriesSmallImage']['img']))
        <input type="hidden" name="image_data[categoriesSmallImage][img]" id="input-lfm-categoriesSmallImage" value="{{ old('image_data')['categoriesSmallImage']['img'] ?? $content->image_data['categoriesSmallImage']['img'] ?? '' }}">
        <input type="hidden" name="image_data[categoriesSmallImage][thumb]" id="input-lfm-thumb-categoriesSmallImage" value="{{ old('image_data')['categoriesSmallImage']['thumb'] ?? $content->image_data['categoriesSmallImage']['thumb'] ?? '' }}">

        <a href="javascript:;" data-fancybox="true" data-src="{{ $content->image_data['categoriesSmallImage']['img'] }}" style="display: block;">
          <img src="{{ $content->image_data['categoriesSmallImage']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
        </a>
      @endif
    </div>
    <div class="mb-2">
      <div class="mb-2 text-gray-500 text-sm">256x378px</div>
      <button
        type="button"
        id="categoriesSmallImage"
        class="button button-secondary"
        data-lfm="image"
        data-preview="lfm-preview-categoriesSmallImage">Выбрать изображение</button>
    </div>
  </div>
</div>
<div class="form-group">
  <x-input-label for="uniqueWorkingRecipes" class="mb-2" :value="__('Уникальные рабочие рецептуры')" />


  <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
    <div id="lfm-preview-uniqueWorkingRecipes" class="rounded-md overflow-hidden w-20" data-name="image_data[uniqueWorkingRecipes]">
      @if(isset($content->image_data['uniqueWorkingRecipes']['img']))
        <input type="hidden" name="image_data[uniqueWorkingRecipes][img]" id="input-lfm-uniqueWorkingRecipes" value="{{ old('image_data')['uniqueWorkingRecipes']['img'] ?? $content->image_data['uniqueWorkingRecipes']['img'] ?? '' }}">
        <input type="hidden" name="image_data[uniqueWorkingRecipes][thumb]" id="input-lfm-thumb-uniqueWorkingRecipes" value="{{ old('image_data')['uniqueWorkingRecipes']['thumb'] ?? $content->image_data['uniqueWorkingRecipes']['thumb'] ?? '' }}">

        <a href="javascript:;" data-fancybox="true" data-src="{{ $content->image_data['uniqueWorkingRecipes']['img'] }}" style="display: block;">
          <img src="{{ $content->image_data['uniqueWorkingRecipes']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
        </a>
      @endif
    </div>
    <div class="mb-2">
      <div class="mb-2 text-gray-500 text-sm">909x376px</div>
      <button
        type="button"
        id="uniqueWorkingRecipes"
        class="button button-secondary"
        data-lfm="image"
        data-preview="lfm-preview-uniqueWorkingRecipes">Выбрать изображение</button>
    </div>
  </div>
</div>
<div class="form-group">
  <x-input-label for="new_products_line" class="mb-2" :value="__('Новая линейка продуктов')" />


  <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
    <div id="lfm-preview-new_products_line" class="rounded-md overflow-hidden w-20" data-name="image_data[new_products_line]">
      @if(isset($content->image_data['new_products_line']['img']))
        <input type="hidden" name="image_data[new_products_line][img]" id="input-lfm-new_products_line" value="{{ old('image_data')['new_products_line']['img'] ?? $content->image_data['new_products_line']['img'] ?? '' }}">
        <input type="hidden" name="image_data[new_products_line][thumb]" id="input-lfm-thumb-new_products_line" value="{{ old('image_data')['new_products_line']['thumb'] ?? $content->image_data['new_products_line']['thumb'] ?? '' }}">

        <a href="javascript:;" data-fancybox="true" data-src="{{ $content->image_data['new_products_line']['img'] }}" style="display: block;">
          <img src="{{ $content->image_data['new_products_line']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
        </a>
      @endif
    </div>
    <div class="mb-2">
      <div class="mb-2 text-gray-500 text-sm">760x518px</div>
      <button
        type="button"
        id="new_products_line"
        class="button button-secondary"
        data-lfm="image"
        data-preview="lfm-preview-new_products_line">Выбрать изображение</button>
    </div>
  </div>
</div>
<div class="form-group">
  <x-input-label for="dermatologistsImg" class="mb-2" :value="__('Изображение для блока «Наши специалисты»')" />


  <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
    <div id="lfm-preview-dermatologistsImg" class="rounded-md overflow-hidden w-20" data-name="image_data[dermatologistsImg]">
      @if(isset($content->image_data['dermatologistsImg']['img']))
        <input type="hidden" name="image_data[dermatologistsImg][img]" id="input-lfm-dermatologistsImg" value="{{ old('image_data')['dermatologistsImg']['img'] ?? $content->image_data['dermatologistsImg']['img'] ?? '' }}">
        <input type="hidden" name="image_data[dermatologistsImg][thumb]" id="input-lfm-thumb-dermatologistsImg" value="{{ old('image_data')['dermatologistsImg']['thumb'] ?? $content->image_data['dermatologistsImg']['thumb'] ?? '' }}">

        <a href="javascript:;" data-fancybox="true" data-src="{{ $content->image_data['dermatologistsImg']['img'] }}" style="display: block;">
          <img src="{{ $content->image_data['dermatologistsImg']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
        </a>
      @endif
    </div>
    <div class="mb-2">
      <div class="mb-2 text-gray-500 text-sm">760x518px</div>
      <button
        type="button"
        id="dermatologistsImg"
        class="button button-secondary"
        data-lfm="image"
        data-preview="lfm-preview-dermatologistsImg">Выбрать изображение</button>
    </div>
  </div>
</div>
<div class="form-group">

  <x-input-label for="catalogMenu" class="mb-2" :value="__('Категория «Смотреть все»')" />

  <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
    <div id="lfm-preview-catalog" class="rounded-md overflow-hidden w-20" data-name="image_data[catalog]">
      <input type="hidden" name="image_data[catalog][maxWidth]" value="768">
      @if(isset($content->image_data['catalog']['img']))
        <input type="hidden" name="image_data[catalog][img]" id="input-lfm-catalog" value="{{ old('image_data')['catalog']['img'] ?? $content->image_data['catalog']['img'] ?? '' }}">
        <input type="hidden" name="image_data[catalog][thumb]" id="input-lfm-thumb-catalog" value="{{ old('image_data')['catalog']['thumb'] ?? $content->image_data['catalog']['thumb'] ?? '' }}">
        <a href="javascript:;" data-fancybox="true" data-src="{{ $content->image_data['catalog']['img'] }}" style="display: block;">
          <img src="{{ $content->image_data['catalog']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
        </a>
      @endif
    </div>
    <div class="mb-2">
      <div class="mb-2 text-gray-500 text-sm"></div>
      <button
        type="button"
        id="catalog"
        class="button button-secondary"
        data-lfm="image"
        data-preview="lfm-preview-catalog">Выбрать изображение</button>
    </div>
  </div>
</div>
<div class="form-group">

  <x-input-label for="instockMenu" class="mb-2" :value="__('Категория «В наличии»')" />

  <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
    <div id="lfm-preview-instock" class="rounded-md overflow-hidden w-20" data-name="image_data[instock]">
      <input type="hidden" name="image_data[instock][maxWidth]" value="768">
      @if(isset($content->image_data['instock']['img']))
        <input type="hidden" name="image_data[instock][img]" id="input-lfm-instock" value="{{ old('image_data')['instock']['img'] ?? $content->image_data['instock']['img'] ?? '' }}">
        <input type="hidden" name="image_data[instock][thumb]" id="input-lfm-thumb-instock" value="{{ old('image_data')['instock']['thumb'] ?? $content->image_data['instock']['thumb'] ?? '' }}">
        <a href="javascript:;" data-fancybox="true" data-src="{{ $content->image_data['instock']['img'] }}" style="display: block;">
          <img src="{{ $content->image_data['instock']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
        </a>
      @endif
    </div>
    <div class="mb-2">
      <div class="mb-2 text-gray-500 text-sm"></div>
      <button
        type="button"
        id="instock"
        class="button button-secondary"
        data-lfm="image"
        data-preview="lfm-preview-instock">Выбрать изображение</button>
    </div>
  </div>
</div>
<div class="form-group">

  <x-input-label for="vouchersMenu" class="mb-2" :value="__('Категория «Подарочные сертификаты»')" />

  <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
    <div id="lfm-preview-vouchers" class="rounded-md overflow-hidden w-20" data-name="image_data[vouchers]">
      <input type="hidden" name="image_data[vouchers][maxWidth]" value="768">
      @if(isset($content->image_data['vouchers']['img']))
        <input type="hidden" name="image_data[vouchers][img]" id="input-lfm-vouchers" value="{{ old('image_data')['vouchers']['img'] ?? $content->image_data['vouchers']['img'] ?? '' }}">
        <input type="hidden" name="image_data[vouchers][thumb]" id="input-lfm-thumb-vouchers" value="{{ old('image_data')['vouchers']['thumb'] ?? $content->image_data['vouchers']['thumb'] ?? '' }}">
        <a href="javascript:;" data-fancybox="true" data-src="{{ $content->image_data['vouchers']['img'] }}" style="display: block;">
          <img src="{{ $content->image_data['vouchers']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
        </a>
      @endif
    </div>
    <div class="mb-2">
      <div class="mb-2 text-gray-500 text-sm"></div>
      <button
        type="button"
        id="vouchers"
        class="button button-secondary"
        data-lfm="image"
        data-preview="lfm-preview-vouchers">Выбрать изображение</button>
    </div>
  </div>
</div>
<div class="form-group">

  <x-input-label for="presentsMenu" class="mb-2" :value="__('Категория «Наши презенты»')" />

  <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
    <div id="lfm-preview-presents" class="rounded-md overflow-hidden w-20" data-name="image_data[presents]">
      <input type="hidden" name="image_data[presents][maxWidth]" value="768">
      @if(isset($content->image_data['presents']['img']))
        <input type="hidden" name="image_data[presents][img]" id="input-lfm-presents" value="{{ old('image_data')['presents']['img'] ?? $content->image_data['presents']['img'] ?? '' }}">
        <input type="hidden" name="image_data[presents][thumb]" id="input-lfm-thumb-presents" value="{{ old('image_data')['presents']['thumb'] ?? $content->image_data['presents']['thumb'] ?? '' }}">
        <a href="javascript:;" data-fancybox="true" data-src="{{ $content->image_data['presents']['img'] }}" style="display: block;">
          <img src="{{ $content->image_data['presents']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
        </a>
      @endif
    </div>
    <div class="mb-2">
      <div class="mb-2 text-gray-500 text-sm"></div>
      <button
        type="button"
        id="presents"
        class="button button-secondary"
        data-lfm="image"
        data-preview="lfm-preview-presents">Выбрать изображение</button>
    </div>
  </div>
</div>
