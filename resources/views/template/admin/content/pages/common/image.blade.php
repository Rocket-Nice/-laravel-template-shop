<div class="form-group">

  <x-input-label for="readMoreImage" class="mb-2" :value="__('Изображение для категорий «читать далее»')" />

  <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
    <div id="lfm-preview-readMoreImage" class="rounded-md overflow-hidden w-20" data-name="image_data[readMoreImage]">
      @if(isset($content->image_data['readMoreImage']['img']))
        <input type="hidden" name="image_data[readMoreImage][img]" id="input-lfm-readMoreImage" value="{{ old('image_data')['readMoreImage']['img'] ?? $content->image_data['readMoreImage']['img'] ?? '' }}">
        <input type="hidden" name="image_data[readMoreImage][thumb]" id="input-lfm-thumb-readMoreImage" value="{{ old('image_data')['readMoreImage']['thumb'] ?? $content->image_data['readMoreImage']['thumb'] ?? '' }}">

        <a href="javascript:;" data-fancybox="true" data-src="{{ $content->image_data['readMoreImage']['img'] }}" style="display: block;">
          <img src="{{ $content->image_data['readMoreImage']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
        </a>
      @endif
    </div>
    <div class="mb-2">
      <div class="mb-2 text-gray-500 text-sm">468x690px</div>
      <button
        type="button"
        id="readMoreImage"
        class="button button-secondary"
        data-lfm="image"
        data-preview="lfm-preview-readMoreImage">Выбрать изображение</button>
    </div>

  </div>
</div>
