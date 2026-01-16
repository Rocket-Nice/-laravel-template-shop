<div class="form-group">

  <x-input-label for="mainImage" class="mb-2" :value="__('Изображение у заголовка')" />


  <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
    <div id="lfm-preview-mainImage" class="rounded-md overflow-hidden w-20" data-name="image_data[mainImage]">
      @if(isset($content->image_data['mainImage']['img']))
        <input type="hidden" name="image_data[mainImage][img]" id="input-lfm-mainImage" value="{{ old('image_data')['mainImage']['img'] ?? $content->image_data['mainImage']['img'] ?? '' }}">
        <input type="hidden" name="image_data[mainImage][thumb]" id="input-lfm-thumb-mainImage" value="{{ old('image_data')['mainImage']['thumb'] ?? $content->image_data['mainImage']['thumb'] ?? '' }}">

        <a href="javascript:;" data-fancybox="true" data-src="{{ $content->image_data['mainImage']['img'] }}" style="display: block;">
          <img src="{{ $content->image_data['mainImage']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
        </a>
      @endif
    </div>
    <div class="mb-2">
      <div class="mb-2 text-gray-500 text-sm">622x702px</div>
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

  <x-input-label for="deliveryImage" class="mb-2" :value="__('Изображение о доставке')" />


  <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
    <div id="lfm-preview-deliveryImage" class="rounded-md overflow-hidden w-20" data-name="image_data[deliveryImage]">
      @if(isset($content->image_data['deliveryImage']['img']))
        <input type="hidden" name="image_data[deliveryImage][img]" id="input-lfm-deliveryImage" value="{{ old('image_data')['deliveryImage']['img'] ?? $content->image_data['deliveryImage']['img'] ?? '' }}">
        <input type="hidden" name="image_data[deliveryImage][thumb]" id="input-lfm-thumb-deliveryImage" value="{{ old('image_data')['deliveryImage']['thumb'] ?? $content->image_data['deliveryImage']['thumb'] ?? '' }}">

        <a href="javascript:;" data-fancybox="true" data-src="{{ $content->image_data['deliveryImage']['img'] }}" style="display: block;">
          <img src="{{ $content->image_data['deliveryImage']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
        </a>
      @endif
    </div>
    <div class="mb-2">
      <div class="mb-2 text-gray-500 text-sm">468x596px</div>
      <button
        type="button"
        id="deliveryImage"
        class="button button-secondary"
        data-lfm="image"
        data-preview="lfm-preview-deliveryImage">Выбрать изображение</button>
    </div>

  </div>
</div>
<div class="form-group">
  <x-input-label for="paymentImage" class="mb-2" :value="__('Изображение об оплате')" />

  <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
    <div id="lfm-preview-paymentImage" class="rounded-md overflow-hidden w-20" data-name="image_data[paymentImage]">
      @if(isset($content->image_data['paymentImage']['img']))
        <input type="hidden" name="image_data[paymentImage][img]" id="input-lfm-paymentImage" value="{{ old('image_data')['paymentImage']['img'] ?? $content->image_data['paymentImage']['img'] ?? '' }}">
        <input type="hidden" name="image_data[paymentImage][thumb]" id="input-lfm-thumb-paymentImage" value="{{ old('image_data')['paymentImage']['thumb'] ?? $content->image_data['paymentImage']['thumb'] ?? '' }}">

        <a href="javascript:;" data-fancybox="true" data-src="{{ $content->image_data['paymentImage']['img'] }}" style="display: block;">
          <img src="{{ $content->image_data['paymentImage']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
        </a>
      @endif
    </div>
    <div class="mb-2">
      <div class="mb-2 text-gray-500 text-sm">468x596px</div>
      <button
        type="button"
        id="paymentImage"
        class="button button-secondary"
        data-lfm="image"
        data-preview="lfm-preview-paymentImage">Выбрать изображение</button>
    </div>

  </div>
</div>
