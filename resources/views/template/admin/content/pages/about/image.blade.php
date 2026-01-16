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
{{--<div class="form-group">--}}

{{--  <x-input-label for="readMore" class="mb-2" :value="__('Изображение для «читать дополнительно»')" />--}}

{{--  <input type="hidden" name="image_data[readMore][img]" id="input-lfm-readMore" value="{{ old('image_data')['readMore']['img'] ?? $content->image_data['readMore']['img'] ?? '' }}">--}}
{{--  <input type="hidden" name="image_data[readMore][thumb]" id="input-lfm-thumb-readMore" value="{{ old('image_data')['readMore']['thumb'] ?? $content->image_data['readMore']['thumb'] ?? '' }}">--}}

{{--  <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">--}}
{{--    <div id="lfm-preview-readMore" class="rounded-md overflow-hidden w-20">--}}
{{--      @if(isset($content->image_data['readMore']['img']))--}}
{{--        <a href="javascript:;" data-fancybox="true" data-src="{{ $content->image_data['readMore']['img'] }}" style="display: block;">--}}
{{--          <img src="{{ $content->image_data['readMore']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">--}}
{{--        </a>--}}
{{--      @endif--}}
{{--    </div>--}}
{{--    <div>--}}
{{--      <div class="mb-2 text-gray-500 text-sm">494x728px</div>--}}
{{--      <button--}}
{{--        type="button"--}}
{{--        id="readMore"--}}
{{--        class="button button-secondary"--}}
{{--        data-lfm="image"--}}
{{--        data-input="input-lfm-readMore"--}}
{{--        data-thumb="input-lfm-thumb-readMore"--}}
{{--        data-preview="lfm-preview-readMore">Выбрать изображение</button>--}}
{{--    </div>--}}
{{--  </div>--}}
{{--</div>--}}
<div class="form-group">

  <x-input-label for="leMousseImage" class="mb-2" :value="__('Изображение «Le mousse»')" />

  <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
    <div id="lfm-preview-leMousseImage" class="rounded-md overflow-hidden w-20" data-name="image_data[leMousseImage]">
      @if(isset($content->image_data['leMousseImage']['img']))
        <input type="hidden" name="image_data[leMousseImage][img]" id="input-lfm-leMousseImage" value="{{ old('image_data')['leMousseImage']['img'] ?? $content->image_data['leMousseImage']['img'] ?? '' }}">
        <input type="hidden" name="image_data[leMousseImage][thumb]" id="input-lfm-thumb-leMousseImage" value="{{ old('image_data')['leMousseImage']['thumb'] ?? $content->image_data['leMousseImage']['thumb'] ?? '' }}">

        <a href="javascript:;" data-fancybox="true" data-src="{{ $content->image_data['leMousseImage']['img'] }}" style="display: block;">
          <img src="{{ $content->image_data['leMousseImage']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
        </a>
      @endif
    </div>
    <div class="mb-2">
      <div class="mb-2 text-gray-500 text-sm">518x400px</div>
      <button
        type="button"
        id="leMousseImage"
        class="button button-secondary"
        data-lfm="image"
        data-input="input-lfm-leMousseImage"
        data-thumb="input-lfm-thumb-leMousseImage"
        data-preview="lfm-preview-leMousseImage">Выбрать изображение</button>
    </div>
  </div>
</div>
<div class="form-group">
  <x-input-label for="productionImage" class="mb-2" :value="__('Картинка «о производстве»')" />

  <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
    <div id="lfm-preview-productionImage" class="rounded-md overflow-hidden w-20" data-name="image_data[productionImage]">
      @if(isset($content->image_data['productionImage']['img']))
        <input type="hidden" name="image_data[productionImage][img]" id="input-lfm-productionImage" value="{{ old('image_data')['productionImage']['img'] ?? $content->image_data['productionImage']['img'] ?? '' }}">
        <input type="hidden" name="image_data[productionImage][thumb]" id="input-lfm-thumb-productionImage" value="{{ old('image_data')['productionImage']['thumb'] ?? $content->image_data['productionImage']['thumb'] ?? '' }}">

        <a href="javascript:;" data-fancybox="true" data-src="{{ $content->image_data['productionImage']['img'] }}" style="display: block;">
          <img src="{{ $content->image_data['productionImage']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
        </a>
      @endif
    </div>
    <div class="mb-2">
      <div class="mb-2 text-gray-500 text-sm">518x400px</div>
      <button
        type="button"
        id="productionImage"
        class="button button-secondary"
        data-lfm="image"
        data-input="input-lfm-productionImage"
        data-thumb="input-lfm-thumb-productionImage"
        data-preview="lfm-preview-productionImage">Выбрать изображение</button>
    </div>
  </div>
</div>
<div class="form-group">
  <x-input-label for="NechaevaPhoto" class="mb-2" :value="__('Фотография Оли')" />

  <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
    <div id="lfm-preview-NechaevaPhoto" class="rounded-md overflow-hidden w-20" data-name="image_data[NechaevaPhoto]">
      @if(isset($content->image_data['NechaevaPhoto']['img']))
        <input type="hidden" name="image_data[NechaevaPhoto][img]" id="input-lfm-NechaevaPhoto" value="{{ old('image_data')['NechaevaPhoto']['img'] ?? $content->image_data['NechaevaPhoto']['img'] ?? '' }}">
        <input type="hidden" name="image_data[NechaevaPhoto][thumb]" id="input-lfm-thumb-NechaevaPhoto" value="{{ old('image_data')['NechaevaPhoto']['thumb'] ?? $content->image_data['NechaevaPhoto']['thumb'] ?? '' }}">

        <a href="javascript:;" data-fancybox="true" data-src="{{ $content->image_data['NechaevaPhoto']['img'] }}" style="display: block;">
          <img src="{{ $content->image_data['NechaevaPhoto']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
        </a>
      @endif
    </div>
    <div class="mb-2">
      <div class="mb-2 text-gray-500 text-sm">474x576px</div>
      <button
        type="button"
        id="NechaevaPhoto"
        class="button button-secondary"
        data-lfm="image"
        data-input="input-lfm-NechaevaPhoto"
        data-thumb="input-lfm-thumb-NechaevaPhoto"
        data-preview="lfm-preview-NechaevaPhoto">Выбрать изображение</button>
    </div>
  </div>
</div>
