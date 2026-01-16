
<div class="form-group">
  <x-input-label for="text_data-headline1" :value="__('Заголовок')" />
  <x-text-input type="text" name="text_data[headline1]" id="text_data-headline1" value="{{ old('text_data')['headline1'] ?? $content->text_data['headline1'] ?? '' }}" class="mt-1 block w-full"/>
</div>
<div class="form-group">
  <x-input-label for="text_data-subtitle1" :value="__('Подзаголовок')" />
  <x-textarea name="text_data[subtitle1]" id="text_data-subtitle1" class="mt-1 block w-full">{{ old('text_data')['subtitle1'] ?? $content->text_data['subtitle1'] ?? '' }}</x-textarea>
</div>
<div class="form-group">
  <x-input-label for="text_data-activeComponentsPercentage" :value="__('Строка про активыне компоненты')" />
  <x-textarea name="text_data[activeComponentsPercentage]" id="text_data-activeComponentsPercentage" class="mt-1 block w-full">{{ old('text_data')['activeComponentsPercentage'] ?? $content->text_data['activeComponentsPercentage'] ?? '' }}</x-textarea>
</div>
<div class="form-group">
  <x-input-label for="text_data-activeComponentsPercentage2" :value="__('Подпись про активыне компоненты')" />
  <x-textarea name="text_data[activeComponentsPercentage2]" id="text_data-activeComponentsPercentage2" class="mt-1 block w-full">{{ old('text_data')['activeComponentsPercentage2'] ?? $content->text_data['activeComponentsPercentage2'] ?? '' }}</x-textarea>
</div>
<div class="form-group">
  <x-input-label for="text_data-production" :value="__('Строка о производстве')" />
  <x-textarea name="text_data[production]" id="text_data-production" class="mt-1 block w-full">{{ old('text_data')['production'] ?? $content->text_data['production'] ?? '' }}</x-textarea>
</div>
<div class="form-group">
  <x-input-label for="text_data-production2" :value="__('Строка о производстве на картинке')" />
  <x-textarea name="text_data[production2]" id="text_data-production2" class="mt-1 block w-full">{{ old('text_data')['production2'] ?? $content->text_data['production2'] ?? '' }}</x-textarea>
</div>
<div class="form-group">
  <x-input-label for="text_data-new_products_line" :value="__('Строка о новой линейке продуктов')" />
  <x-textarea name="text_data[new_products_line]" id="text_data-new_products_line" class="mt-1 block w-full">{{ old('text_data')['new_products_line'] ?? $content->text_data['new_products_line'] ?? '' }}</x-textarea>
</div>
<div class="form-group">
  <x-input-label for="text_data-new_products_line2" :value="__('Описание о новой линейке продуктов')" />
  <x-textarea name="text_data[new_products_line2]" id="text_data-new_products_line2" class="mt-1 block w-full">{{ old('text_data')['new_products_line2'] ?? $content->text_data['new_products_line2'] ?? '' }}</x-textarea>
</div>
<div class="form-group">
  <x-input-label for="text_data-dermatologists-text" :value="__('Текст в блок про наших специалистов')" />
  <x-textarea name="text_data[dermatologists-text]" id="text_data-dermatologists-text" class="mt-1 block w-full tinymce-textarea">{{ old('text_data')['dermatologists-text'] ?? $content->text_data['dermatologists-text'] ?? '' }}</x-textarea>
</div>
<div class="form-group">
  <x-input-label for="text_data-dermatologists-img-text" :value="__('Текст на изображении в блок про наших специалистов')" />
  <x-textarea name="text_data[dermatologists-img-text]" id="text_data-dermatologists-img-text" class="mt-1 block w-full">{{ old('text_data')['dermatologists-img-text'] ?? $content->text_data['dermatologists-img-text'] ?? '' }}</x-textarea>
</div>
<div class="slideFile">
  <x-input-label class="image-label mb-2" :value="__('Чек-лист')" />
  <div class="form-group">
    <div class="mb-1 p-2 border-gray-300 rounded-md border">
      <div id="lfm-preview-checklist" class="lfm-preview flex flex-wrap" data-name="text_data[checklist]">
        @if (isset($content->text_data['checklist']['file'])&&!empty($content->text_data['checklist']['file']))
          <input type="hidden" name="text_data[checklist][file]" value="{{ $content->text_data['checklist']['file'] }}">
          <div>
            <ul class="max-w-md space-y-1 text-gray-500 list-disc list-inside mt-2 mb-4">
              <li>{{ filenameFromUrl($content->text_data['checklist']['file']) }}</li>
            </ul>
          </div>
        @endif
      </div>
      <div class="mb-2">
        <div class="mb-2 text-gray-500 text-sm">pdf, jpg, png</div>
        <button
          type="button"
          id="checklist"
          class="button button-secondary"
          data-lfm="file"
          data-preview="lfm-preview-checklist">Выбрать чек-лист</button>
      </div>
    </div>
  </div>

</div>
