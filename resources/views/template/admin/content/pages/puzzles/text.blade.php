
<div class="form-group">
  <x-input-label for="text_data-headline1" :value="__('Заголовок')" />
  <x-text-input type="text" name="text_data[headline1]" id="text_data-headline1" value="{{ old('text_data')['headline1'] ?? $content->text_data['headline1'] ?? '' }}" class="mt-1 block w-full"/>
</div>

<div class="form-group">
  <x-input-label for="text_data-subtitle1" :value="__('Подзаголовок')" />
  <x-textarea name="text_data[subtitle1]" id="text_data-subtitle1" class="mt-1 block w-full">{{ old('text_data')['subtitle1'] ?? $content->text_data['subtitle1'] ?? '' }}</x-textarea>
</div>
<div class="form-group">
  <x-input-label for="text_data-prizes" :value="__('Список подарков')" />
  <x-textarea type="text" name="text_data[prizes]" id="text_data-prizes" class="mt-1 block w-full  tinymce-textarea">{{ old('text_data')['prizes'] ?? $content->text_data['prizes'] ?? '' }}</x-textarea>
</div>
<div class="form-group">
  <x-input-label for="text_data-bottomText" :value="__('Текст после кнопки “загрузить“')" />
  <x-textarea type="text" name="text_data[bottomText]" id="text_data-bottomText" class="mt-1 block w-full  tinymce-textarea">{{ old('text_data')['bottomText'] ?? $content->text_data['bottomText'] ?? '' }}</x-textarea>
</div>

