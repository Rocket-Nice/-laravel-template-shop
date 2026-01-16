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
