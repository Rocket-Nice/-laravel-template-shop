
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
  <x-textarea name="text_data[cosmeceuticalsText]" id="text_data-cosmeceuticalsText" class="mt-1 block w-full tinymce-textarea">{{ old('text_data')['cosmeceuticalsText'] ?? $content->text_data['cosmeceuticalsText'] ?? '' }}</x-textarea>
</div>
<div class="form-group">
  <x-input-label for="text_data-lemousseText" :value="__('Текст о Lemousse')" />
  <x-textarea name="text_data[lemousseText]" id="text_data-lemousseText" class="mt-1 block w-full tinymce-textarea">{{ old('text_data')['lemousseText'] ?? $content->text_data['lemousseText'] ?? '' }}</x-textarea>
</div>
<div class="form-group">
  <x-input-label for="text_data-productionText" :value="__('Текст о производстве')" />
  <x-textarea name="text_data[productionText]" id="text_data-productionText" class="mt-1 block w-full tinymce-textarea">{{ old('text_data')['productionText'] ?? $content->text_data['productionText'] ?? '' }}</x-textarea>
</div>
<div class="form-group">
  <x-input-label for="text_data-greenLine" :value="__('Строка текста на зеленом фоне')" />
  <x-textarea name="text_data[greenLine]" id="text_data-greenLine" class="mt-1 block w-full">{{ old('text_data')['greenLine'] ?? $content->text_data['greenLine'] ?? '' }}</x-textarea>
</div>
<div class="form-group">
  <x-input-label for="text_data-productionList" :value="__('Кориченвый список')" />
  <x-textarea name="text_data[productionList]" id="text_data-productionList" class="mt-1 block w-full tinymce-textarea">{{ old('text_data')['productionList'] ?? $content->text_data['productionList'] ?? '' }}</x-textarea>
</div>
<div class="form-group">
  <x-input-label for="text_data-production" :value="__('Текст после списка')" />
  <x-textarea name="text_data[production]" id="text_data-production" class="mt-1 block w-full tinymce-textarea">{{ old('text_data')['production'] ?? $content->text_data['production'] ?? '' }}</x-textarea>
</div>
<div class="form-group">
  <x-input-label for="text_data-nechaeva_quote" :value="__('Текст от Ольги Нечаевой')" />
  <x-textarea name="text_data[nechaeva_quote]" id="text_data-nechaeva_quote" class="mt-1 block w-full tinymce-textarea">{{ old('text_data')['nechaeva_quote'] ?? $content->text_data['nechaeva_quote'] ?? '' }}</x-textarea>
</div>
