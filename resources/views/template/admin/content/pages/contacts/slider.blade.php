<div class="carousel-contructor mb-5">
  <div class="flex justify-between items-end">
    <x-input-label for="contactsSwiperAddSlide" class="mb-2" :value="__('Слайдер')" />
    <button type="button" id="contactsSwiperAddSlide" class="addSlide button button-success button-sm mb-2" data-id="contactsSwiper">Добавить слайд</button>
  </div>
  <div id="contactsSwiper_donor" style="display: none;">
    <x-admin.content.home-slider-1>
      <div class="slideImage">
        <x-input-label class="image-label mb-2" :value="__('Изображение')" />

        <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
          <div class="lfm-preview flex flex-wrap"></div>
          <div class="mb-2">
            <div class="mb-2 text-gray-500 text-sm">546x658px</div>
            <button
              type="button"
              id="contactsSwiper"
              class="button button-secondary"
              data-lfm="image">Выбрать изображение</button>
          </div>

        </div>
      </div>
      <div class="form-group">
        <x-input-label :value="__('Заголовок')" />
        <x-text-input type="text" data-name="headline" class="mt-1 block w-full" disabled/>
      </div>
      <div class="form-group">
        <x-input-label :value="__('Текст')" />
        <x-textarea data-name="text" class="mt-1 block w-full tinymce-textarea" disabled></x-textarea>
      </div>
    </x-admin.content.home-slider-1>
  </div>
  <div class="carousel space-y-3 mb-1 p-2 border-gray-300 rounded-md border">
    @if(isset($content->carousel_data['contactsSwiper']))
      @foreach($content->carousel_data['contactsSwiper'] as $index => $slide)
        <x-admin.content.home-slider-1>
          <div class="slideImage">
            <x-input-label class="image-label mb-2" :value="__('Изображение')" />

            <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
              <div id="lfm-preview-contactsSwiper-{{ $index }}" class="lfm-preview flex flex-wrap" data-name="carousel_data[contactsSwiper][{{ $index }}][image]">
                <input type="hidden" name="carousel_data[contactsSwiper][{{ $index }}][image][img]" id="carousel_data-contactsSwiper-{{ $index }}-[image]img" data-name="img" data-parent="image" value="{{ $slide['image']['img'] ?? '' }}">
                <input type="hidden" name="carousel_data[contactsSwiper][{{ $index }}][image][thumb]" id="carousel_data-contactsSwiper-{{ $index }}-[image]thumb" data-name="thumb" data-parent="image" value="{{ $slide['image']['thumb'] ?? '' }}">

                <a href="javascript:;" data-fancybox="true" data-src="{{ $slide['image']['img'] ?? '' }}" style="display: block;">
                  <img src="{{ $slide['image']['thumb'] ?? '' }}" class="overflow-hidden max-w-full object-cover object-center">
                </a>
              </div>
              <div class="mb-2">
                <div class="mb-2 text-gray-500 text-sm">546x658px</div>
                <button
                  type="button"
                  id="contactsSwiper"
                  class="button button-secondary"
                  data-lfm="image"
                  data-preview="lfm-preview-contactsSwiper-{{ $index }}">Выбрать изображение</button>
              </div>
            </div>
          </div>
          <div class="form-group">
            <x-input-label :value="__('Заголовок')" />
            <x-text-input type="text" name="carousel_data[contactsSwiper][{{ $index }}][headline]" id="carousel_data-contactsSwiper-{{ $index }}-headline" data-name="headline" class="mt-1 block w-full" value="{{ $slide['headline'] }}"/>
          </div>
          <div class="form-group">
            <x-input-label :value="__('Текст')" />
            <x-textarea name="carousel_data[contactsSwiper][{{ $index }}][text]" id="carousel_data-contactsSwiper-{{ $index }}-text" data-name="text" class="mt-1 block w-full tinymce-textarea">{{ $slide['text'] }}</x-textarea>
          </div>
        </x-admin.content.home-slider-1>
      @endforeach
    @endif
  </div>
</div>
