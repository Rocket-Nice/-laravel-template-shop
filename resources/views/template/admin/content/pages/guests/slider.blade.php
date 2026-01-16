<div class="carousel-contructor mb-5">
  <div class="flex justify-between items-end">
    <x-input-label for="ourGuestsAddSlide" class="mb-2" :value="__('Элементы с фото')" />
    <button type="button" id="ourGuestsAddSlide" class="addSlide button button-success button-sm mb-2" data-id="ourGuests">Добавить слайд</button>
  </div>
  <div id="ourGuests_donor" style="display: none;">
    <x-admin.content.home-slider-1>
      <div class="slideImage">
        <x-input-label class="image-label mb-2" :value="__('Изображение')" />

        <div class="form-group">
          <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
            <div class="lfm-preview flex flex-wrap"></div>
            <div class="mb-2">
              <div class="mb-2 text-gray-500 text-sm">671x987px</div>
              <button
                type="button"
                id="ourGuests"
                data-multiple="1"
                class="button button-secondary"
                data-lfm="image">Выбрать изображение</button>
            </div>

          </div>

        </div>

      </div>
      <div class="form-group">
        <x-input-label :value="__('Имя')" />
        <x-text-input type="text" data-name="name" class="mt-1 block w-full" disabled/>
      </div>
      <div class="form-group">
        <x-input-label :value="__('Дата')" />
        <x-text-input type="text" data-name="date" class="mt-1 block w-full" disabled/>
      </div>
    </x-admin.content.home-slider-1>
  </div>
  <div class="carousel space-y-3 mb-1 p-2 border-gray-300 rounded-md border">
    @if(isset($content->carousel_data['ourGuests']))
      @foreach($content->carousel_data['ourGuests'] as $index => $slide)
        <x-admin.content.home-slider-1>
          <div class="slideImage">
            <x-input-label class="image-label mb-2" :value="__('Изображение')" />
            <div class="form-group">
              <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
                <div id="lfm-preview-ourGuests-{{ $index }}" class="lfm-preview flex flex-wrap" data-name="carousel_data[ourGuests][{{ $index }}][image]">
                  @if (isset($slide['image']['img'])&&!empty($slide['image']['img']))
                    <input type="hidden" name="carousel_data[ourGuests][{{ $index }}][image][img]" value="{{ $slide['image']['img'] }}">
                    <input type="hidden" name="carousel_data[ourGuests][{{ $index }}][image][thumb]" value="{{ $slide['image']['thumb'] }}">
                    <a href="javascript:;" data-fancybox="true" data-src="{{ $slide['image']['img'] }}" class="block rounded-md overflow-hidden w-20 m-2">
                      <img src="{{ $slide['image']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
                    </a>
                  @elseif(isset($slide['image'])&&is_array($slide['image']))
                    @foreach($slide['image'] as $key => $image)
                      <input type="hidden" name="carousel_data[ourGuests][{{ $index }}][image][{{ $key }}][img]" value="{{ $image['img'] }}">
                      <input type="hidden" name="carousel_data[ourGuests][{{ $index }}][image][{{ $key }}][thumb]" value="{{ $image['thumb'] }}">
                      <a href="javascript:;" data-fancybox="true" data-src="{{ $image['img'] }}" class="block rounded-md overflow-hidden w-20 m-2">
                        <img src="{{ $image['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
                      </a>
                    @endforeach
                  @endif
                </div>
                <div class="mb-2">
                  <div class="mb-2 text-gray-500 text-sm">546x658px</div>
                  <button
                    type="button"
                    id="ourGuests"
                    class="button button-secondary"
                    data-lfm="image"
                    data-multiple="1"
                    data-preview="lfm-preview-ourGuests-{{ $index }}">Выбрать изображение</button>
                </div>
              </div>
            </div>

          </div>
          <div class="form-group">
            <x-input-label :value="__('Заголовок')" />
            <x-text-input type="text" name="carousel_data[ourGuests][{{ $index }}][name]" id="carousel_data-ourGuests-{{ $index }}-name" data-name="name" class="mt-1 block w-full" value="{{ $slide['name'] }}"/>
          </div>
          <div class="form-group">
            <x-input-label :value="__('Подпись')" />
            <x-text-input type="text" name="carousel_data[ourGuests][{{ $index }}][date]" id="carousel_data-ourGuests-{{ $index }}-date" data-name="date" class="mt-1 block w-full" value="{{ $slide['date'] }}"/>
          </div>
        </x-admin.content.home-slider-1>
      @endforeach
    @endif
  </div>
</div>
