
<div class="form-group">
  <x-input-label for="text_data-headline1" :value="__('Заголовок')" />
  <x-text-input type="text" name="text_data[headline1]" id="text_data-headline1" value="{{ old('text_data')['headline1'] ?? $content->text_data['headline1'] ?? '' }}" class="mt-1 block w-full"/>
</div>
<div class="form-group">
  <x-input-label for="text_data-subtitle1" :value="__('Подзаголовок')" />
  <x-textarea name="text_data[subtitle1]" id="text_data-subtitle1" class="mt-1 block w-full">{{ old('text_data')['subtitle1'] ?? $content->text_data['subtitle1'] ?? '' }}</x-textarea>
</div>
<div class="form-group">
  <x-input-label for="text_data-fofWhat" :value="__('Текст «Для чего?»')" />
  <x-textarea name="text_data[fofWhat]" id="text_data-fofWhat" class="mt-1 block w-full tinymce-textarea">{{ old('text_data')['fofWhat'] ?? $content->text_data['fofWhat'] ?? '' }}</x-textarea>
</div>
<div class="form-group">
  <x-input-label for="text_data-aboutXenon" :value="__('Текст о ксеноне')" />
  <x-textarea name="text_data[aboutXenon]" id="text_data-aboutXenon" class="mt-1 block w-full tinymce-textarea">{{ old('text_data')['aboutXenon'] ?? $content->text_data['aboutXenon'] ?? '' }}</x-textarea>
</div>
<div class="form-group">
  <x-input-label for="text_data-greenLine" :value="__('Строка текста на зеленом фоне')" />
  <x-textarea name="text_data[greenLine]" id="text_data-greenLine" class="mt-1 block w-full">{{ old('text_data')['greenLine'] ?? $content->text_data['greenLine'] ?? '' }}</x-textarea>
</div>
<div class="form-group">
  <x-input-label for="text_data-headline2" :value="__('Заголовок после зеленого тектса')" />
  <x-text-input type="text" name="text_data[headline2]" id="text_data-headline2" value="{{ old('text_data')['headline2'] ?? $content->text_data['headline2'] ?? '' }}" class="mt-1 block w-full"/>
</div>
<div class="form-group">
  <x-input-label for="text_data-headline2text" :value="__('Текст под заголовком')" />
  <x-textarea name="text_data[headline2text]" id="text_data-headline2text" class="mt-1 block w-full tinymce-textarea">{{ old('text_data')['headline2text'] ?? $content->text_data['headline2text'] ?? '' }}</x-textarea>
</div>
<div class="form-group">
  <x-input-label for="text_data-beigeText" :value="__('Текст на бежевом фоне')" />
  <x-textarea name="text_data[beigeText]" id="text_data-beigeText" class="mt-1 block w-full">{{ old('text_data')['beigeText'] ?? $content->text_data['beigeText'] ?? '' }}</x-textarea>
</div>


<div class="form-group">
  <x-input-label for="text_data-headline3" :value="__('Заголовок рядом с фото')" />
  <x-text-input type="text" name="text_data[headline3]" id="text_data-headline3" value="{{ old('text_data')['headline3'] ?? $content->text_data['headline3'] ?? '' }}" class="mt-1 block w-full"/>
</div>
<div class="form-group">
  <x-input-label for="text_data-headline3text" :value="__('Текст под заголовком рядом с фото')" />
  <x-textarea name="text_data[headline3text]" id="text_data-headline3text" class="mt-1 block w-full tinymce-textarea">{{ old('text_data')['headline3text'] ?? $content->text_data['headline3text'] ?? '' }}</x-textarea>
</div>
<div class="form-group">
  <x-input-label for="text_data-beigeText2" :value="__('Текст на бежевом фоне под фото')" />
  <x-textarea name="text_data[beigeText2]" id="text_data-beigeText2" class="mt-1 block w-full">{{ old('text_data')['beigeText2'] ?? $content->text_data['beigeText2'] ?? '' }}</x-textarea>
</div>

<div class="form-group">
  <x-input-label for="text_data-aboutXenon2" :value="__('Текст о ксеноне 2')" />
  <x-textarea name="text_data[aboutXenon2]" id="text_data-aboutXenon2" class="mt-1 block w-full tinymce-textarea">{{ old('text_data')['aboutXenon2'] ?? $content->text_data['aboutXenon2'] ?? '' }}</x-textarea>
</div>

<div class="form-group">
  <x-input-label for="text_data-beigeTextAboutXenon1" :value="__('Текст на бежевом фоне «о ксеноне» 1')" />
  <x-textarea name="text_data[beigeTextAboutXenon1]" id="text_data-beigeTextAboutXenon1" class="mt-1 block w-full">{{ old('text_data')['beigeTextAboutXenon1'] ?? $content->text_data['beigeTextAboutXenon1'] ?? '' }}</x-textarea>
</div>
<div class="form-group">
  <x-input-label for="text_data-beigeTextAboutXenon2" :value="__('Текст на бежевом фоне «о ксеноне» 2')" />
  <x-textarea name="text_data[beigeTextAboutXenon2]" id="text_data-beigeTextAboutXenon2" class="mt-1 block w-full">{{ old('text_data')['beigeTextAboutXenon2'] ?? $content->text_data['beigeTextAboutXenon2'] ?? '' }}</x-textarea>
</div>
<div class="form-group">
  <x-input-label for="text_data-beigeTextAboutXenon3" :value="__('Текст на бежевом фоне «о ксеноне» 3')" />
  <x-textarea name="text_data[beigeTextAboutXenon3]" id="text_data-beigeTextAboutXenon3" class="mt-1 block w-full">{{ old('text_data')['beigeTextAboutXenon3'] ?? $content->text_data['beigeTextAboutXenon3'] ?? '' }}</x-textarea>
</div>

<div class="form-group">
  <x-input-label for="text_data-cosmeceuticalsText" :value="__('Текст между двух линий')" />
  <x-textarea name="text_data[cosmeceuticalsText]" id="text_data-cosmeceuticalsText" class="mt-1 block w-full tinymce-textarea">{{ old('text_data')['cosmeceuticalsText'] ?? $content->text_data['cosmeceuticalsText'] ?? '' }}</x-textarea>
</div>

<div class="form-group">
  <x-input-label for="text_data-greenLine2" :value="__('Строка текста на зеленом фоне 2')" />
  <x-textarea name="text_data[greenLine2]" id="text_data-greenLine2" class="mt-1 block w-full">{{ old('text_data')['greenLine2'] ?? $content->text_data['greenLine2'] ?? '' }}</x-textarea>
</div>

<div class="form-group">
  <x-input-label for="text_data-aboutXenon3" :value="__('Текст после зеленого фона')" />
  <x-textarea name="text_data[aboutXenon3]" id="text_data-aboutXenon3" class="mt-1 block w-full tinymce-textarea">{{ old('text_data')['aboutXenon3'] ?? $content->text_data['aboutXenon3'] ?? '' }}</x-textarea>
</div>
<div class="form-group">
  <x-input-label for="text_data-linksMoreInfo" :value="__('Ссылки подробнее')" />
  <x-textarea name="text_data[linksMoreInfo]" id="text_data-linksMoreInfo" class="mt-1 block w-full tinymce-textarea">{{ old('text_data')['linksMoreInfo'] ?? $content->text_data['linksMoreInfo'] ?? '' }}</x-textarea>
</div>
