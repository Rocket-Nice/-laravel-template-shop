<div class="form-group">

  <x-input-label for="mainImage" class="mb-2" :value="__('Изображение у заголовка')" />

  <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
    <div id="lfm-preview-mainImage" class="rounded-md overflow-hidden w-20" data-name="image_data[mainImage]">
      <input type="hidden" name="image_data[mainImage][maxWidth]" id="input-lfm-mainImage" value="900">
      @if(isset($content->image_data['mainImage']['img']))
        <input type="hidden" name="image_data[mainImage][img]" id="input-lfm-mainImage" value="{{ old('image_data')['mainImage']['img'] ?? $content->image_data['mainImage']['img'] ?? '' }}">
        <input type="hidden" name="image_data[mainImage][thumb]" id="input-lfm-thumb-mainImage" value="{{ old('image_data')['mainImage']['thumb'] ?? $content->image_data['mainImage']['thumb'] ?? '' }}">

        <a href="javascript:;" data-fancybox="true" data-src="{{ $content->image_data['mainImage']['img'] }}" style="display: block;">
          <img src="{{ $content->image_data['mainImage']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
        </a>
      @endif
    </div>
    <div class="mb-2">
      <div class="mb-2 text-gray-500 text-sm"></div>
      <button
        type="button"
        id="mainImage"
        class="button button-secondary"
        data-lfm="image"
        data-preview="lfm-preview-mainImage">Выбрать изображение</button>
    </div>

  </div>
</div>
<div class="form-group">

  <x-input-label for="forWhat" class="mb-2" :value="__('Изображение «Для чего»')" />

  <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
    <div id="lfm-preview-forWhat" class="rounded-md overflow-hidden w-20" data-name="image_data[forWhat]">
      <input type="hidden" name="image_data[forWhat][maxWidth]" id="input-lfm-forWhat" value="680">
      @if(isset($content->image_data['forWhat']['img']))
        <input type="hidden" name="image_data[forWhat][img]" id="input-lfm-forWhat" value="{{ old('image_data')['forWhat']['img'] ?? $content->image_data['forWhat']['img'] ?? '' }}">
        <input type="hidden" name="image_data[forWhat][thumb]" id="input-lfm-thumb-forWhat" value="{{ old('image_data')['forWhat']['thumb'] ?? $content->image_data['forWhat']['thumb'] ?? '' }}">

        <a href="javascript:;" data-fancybox="true" data-src="{{ $content->image_data['forWhat']['img'] }}" style="display: block;">
          <img src="{{ $content->image_data['forWhat']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
        </a>
      @endif
    </div>
    <div class="mb-2">
      <div class="mb-2 text-gray-500 text-sm"></div>
      <button
        type="button"
        id="forWhat"
        class="button button-secondary"
        data-lfm="image"
        data-input="input-lfm-forWhat"
        data-thumb="input-lfm-thumb-forWhat"
        data-preview="lfm-preview-forWhat">Выбрать изображение</button>
    </div>
  </div>
</div>
<div class="form-group">
  <x-input-label for="whatProductHaveTo" class="mb-2" :value="__('Картинка «В каких продуктах требуется обогащение ксеноном?»')" />

  <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
    <div id="lfm-preview-whatProductHaveTo" class="rounded-md overflow-hidden w-20" data-name="image_data[whatProductHaveTo]">
      <input type="hidden" name="image_data[whatProductHaveTo][maxWidth]" id="input-lfm-whatProductHaveTo" value="960">
      @if(isset($content->image_data['whatProductHaveTo']['img']))
        <input type="hidden" name="image_data[whatProductHaveTo][img]" id="input-lfm-whatProductHaveTo" value="{{ old('image_data')['whatProductHaveTo']['img'] ?? $content->image_data['whatProductHaveTo']['img'] ?? '' }}">
        <input type="hidden" name="image_data[whatProductHaveTo][thumb]" id="input-lfm-thumb-whatProductHaveTo" value="{{ old('image_data')['whatProductHaveTo']['thumb'] ?? $content->image_data['whatProductHaveTo']['thumb'] ?? '' }}">

        <a href="javascript:;" data-fancybox="true" data-src="{{ $content->image_data['whatProductHaveTo']['img'] }}" style="display: block;">
          <img src="{{ $content->image_data['whatProductHaveTo']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
        </a>
      @endif
    </div>
    <div class="mb-2">
      <div class="mb-2 text-gray-500 text-sm"></div>
      <button
        type="button"
        id="whatProductHaveTo"
        class="button button-secondary"
        data-lfm="image"
        data-input="input-lfm-whatProductHaveTo"
        data-thumb="input-lfm-thumb-whatProductHaveTo"
        data-preview="lfm-preview-whatProductHaveTo">Выбрать изображение</button>
    </div>
  </div>
</div>
<div class="form-group">
  <x-input-label for="moreInfo" class="mb-2" :value="__('Картинка «Более подробно об исследованиях»')" />

  <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
    <div id="lfm-preview-moreInfo" class="rounded-md overflow-hidden w-20" data-name="image_data[moreInfo]">
      <input type="hidden" name="image_data[moreInfo][maxWidth]" id="input-lfm-moreInfo" value="960">
      @if(isset($content->image_data['moreInfo']['img']))
        <input type="hidden" name="image_data[moreInfo][img]" id="input-lfm-moreInfo" value="{{ old('image_data')['moreInfo']['img'] ?? $content->image_data['moreInfo']['img'] ?? '' }}">
        <input type="hidden" name="image_data[moreInfo][thumb]" id="input-lfm-thumb-moreInfo" value="{{ old('image_data')['moreInfo']['thumb'] ?? $content->image_data['moreInfo']['thumb'] ?? '' }}">

        <a href="javascript:;" data-fancybox="true" data-src="{{ $content->image_data['moreInfo']['img'] }}" style="display: block;">
          <img src="{{ $content->image_data['moreInfo']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
        </a>
      @endif
    </div>
    <div class="mb-2">
      <div class="mb-2 text-gray-500 text-sm"></div>
      <button
        type="button"
        id="moreInfo"
        class="button button-secondary"
        data-lfm="image"
        data-input="input-lfm-moreInfo"
        data-thumb="input-lfm-thumb-moreInfo"
        data-preview="lfm-preview-moreInfo">Выбрать изображение</button>
    </div>
  </div>
</div>
