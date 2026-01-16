<div class="carousel-contructor mb-5">
  <div class="flex justify-between items-end">
    <x-input-label for="ourAwardsAddSlide" class="mb-2" :value="__('Элементы с фото')" />
    <button type="button" id="ourAwardsAddSlide" class="addSlide button button-success button-sm mb-2" data-id="ourAwards">Добавить слайд</button>
  </div>
  <div id="ourAwards_donor" style="display: none;">
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
                id="ourAwards"
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

      <div class="form-group">
        <x-input-label :value="__('Описание')" />
        <x-text-input type="text"  data-name="description" class="mt-1 block w-full" disabled/>
      </div>
      <div class="form-group">
        <x-input-label :value="__('Ссылка')" />
        <x-text-input type="text"  data-name="link" class="mt-1 block w-full" disabled/>
      </div>
    </x-admin.content.home-slider-1>
  </div>
  <div class="carousel space-y-3 mb-1 p-2 border-gray-300 rounded-md border">
    @if(isset($content->carousel_data['ourAwards']))
      @foreach($content->carousel_data['ourAwards'] as $index => $slide)
        <x-admin.content.home-slider-1>
          <div class="slideImage">
            <x-input-label class="image-label mb-2" :value="__('Изображение')" />
            <div class="form-group">
              <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
                <div id="lfm-preview-ourAwards-{{ $index }}" class="lfm-preview flex flex-wrap" data-name="carousel_data[ourAwards][{{ $index }}][image]">
                  @if (isset($slide['image']['img'])&&!empty($slide['image']['img']))
                    <input type="hidden" name="carousel_data[ourAwards][{{ $index }}][image][img]" data-name="img" value="{{ $slide['image']['img'] }}">
                    <input type="hidden" name="carousel_data[ourAwards][{{ $index }}][image][thumb]" data-name="thumb" value="{{ $slide['image']['thumb'] }}">
                    <a href="javascript:;" data-fancybox="true" data-src="{{ $slide['image']['img'] }}" class="block rounded-md overflow-hidden w-20 m-2">
                      <img src="{{ $slide['image']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
                    </a>
                  @elseif(isset($slide['image'])&&is_array($slide['image']))
                    @foreach($slide['image'] as $key => $image)
                      <input type="hidden" name="carousel_data[ourAwards][{{ $index }}][image][{{ $key }}][img]" data-name="img" value="{{ $image['img'] }}">
                      <input type="hidden" name="carousel_data[ourAwards][{{ $index }}][image][{{ $key }}][thumb]" data-name="thumb" value="{{ $image['thumb'] }}">
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
                    id="ourAwards"
                    class="button button-secondary"
                    data-lfm="image"
                    data-multiple="1"
                    data-preview="lfm-preview-ourAwards-{{ $index }}">Выбрать изображение</button>
                </div>
              </div>
            </div>

          </div>
          <div class="form-group">
            <x-input-label :value="__('Заголовок')" />
            <x-text-input type="text" name="carousel_data[ourAwards][{{ $index }}][name]" id="carousel_data-ourAwards-{{ $index }}-name" data-name="name" class="mt-1 block w-full" value="{{ $slide['name'] }}"/>
          </div>
          <div class="form-group">
            <x-input-label :value="__('Подпись')" />
            <x-text-input type="text" name="carousel_data[ourAwards][{{ $index }}][date]" id="carousel_data-ourAwards-{{ $index }}-date" data-name="date" class="mt-1 block w-full" value="{{ $slide['date'] }}"/>
          </div>
          <div class="form-group">
            <x-input-label :value="__('Описание')" />
            <x-text-input type="text" name="carousel_data[ourAwards][{{ $index }}][description]" id="carousel_data-ourAwards-{{ $index }}-description" data-name="description" class="mt-1 block w-full" value="{{ $slide['description'] }}"/>
          </div>
          <div class="form-group">
            <x-input-label :value="__('Ссылка')" />
            <x-text-input type="text" name="carousel_data[ourAwards][{{ $index }}][link]" id="carousel_data-ourAwards-{{ $index }}-link" data-name="link" class="mt-1 block w-full" value="{{ $slide['link'] }}"/>
          </div>
        </x-admin.content.home-slider-1>
      @endforeach
    @endif
  </div>
</div>
