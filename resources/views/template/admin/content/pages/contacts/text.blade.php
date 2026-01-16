
<div class="form-group">
  <x-input-label for="text_data-headline1" :value="__('Заголовок')" />
  <x-text-input type="text" name="text_data[headline1]" id="text_data-headline1" value="{{ old('text_data')['headline1'] ?? $content->text_data['headline1'] ?? '' }}" class="mt-1 block w-full"/>
</div>
<div class="form-group">
  <x-input-label for="text_data-subtitle1" :value="__('Подзаголовок')" />
  <x-textarea name="text_data[subtitle1]" id="text_data-subtitle1" class="mt-1 block w-full">{{ old('text_data')['subtitle1'] ?? $content->text_data['subtitle1'] ?? '' }}</x-textarea>
</div>

