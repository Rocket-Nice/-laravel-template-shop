
<div class="form-group">
  <x-input-label for="text_data-headline1" :value="__('Заголовок')" />
  <x-text-input type="text" name="text_data[headline1]" id="text_data-headline1" value="{{ old('text_data')['headline1'] ?? $content->text_data['headline1'] ?? '' }}" class="mt-1 block w-full"/>
</div>
<div class="form-group">
  <x-input-label for="text_data-subtitle1" :value="__('Подзаголовок')" />
  <x-textarea name="text_data[subtitle1]" id="text_data-subtitle1" class="mt-1 block w-full">{{ old('text_data')['subtitle1'] ?? $content->text_data['subtitle1'] ?? '' }}</x-textarea>
</div>
<div class="form-group">
  <x-input-label for="text_data-cosmeceuticalsText" :value="__('Текст между двух линий')" />
  <x-textarea name="text_data[cosmeceuticalsText]" id="text_data-cosmeceuticalsText" class="mt-1 block w-full tinymce-textarea" data-toolbar="clearstyles | styles fontfamily bold italic letter-case headlines | align bullist forecolor | code | arrowBottom addSpace addHalfSpace">{{ old('text_data')['cosmeceuticalsText'] ?? $content->text_data['cosmeceuticalsText'] ?? '' }}</x-textarea>
</div>
