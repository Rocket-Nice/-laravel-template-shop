<div class="form-group">

  <x-input-label for="bottomImage" class="mb-2" :value="__('Изображение внизу страницы')" />

  <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
    <div id="lfm-preview-bottomImage" class="rounded-md overflow-hidden w-20" data-name="image_data[bottomImage]">
      @if(isset($content->image_data['bottomImage']['img']))
        <input type="hidden" name="image_data[bottomImage][img]" id="input-lfm-bottomImage" value="{{ old('image_data')['bottomImage']['img'] ?? $content->image_data['bottomImage']['img'] ?? '' }}">
        <input type="hidden" name="image_data[bottomImage][thumb]" id="input-lfm-thumb-bottomImage" value="{{ old('image_data')['bottomImage']['thumb'] ?? $content->image_data['bottomImage']['thumb'] ?? '' }}">

        <a href="javascript:;" data-fancybox="true" data-src="{{ $content->image_data['bottomImage']['img'] }}" style="display: block;">
          <img src="{{ $content->image_data['bottomImage']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
        </a>
      @endif
    </div>
    <div class="mb-2">
      <div class="mb-2 text-gray-500 text-sm">622x702px</div>
      <button
        type="button"
        id="bottomImage"
        class="button button-secondary"
        data-lfm="image"
        data-preview="lfm-preview-bottomImage">Выбрать изображение</button>
    </div>

  </div>
</div>
