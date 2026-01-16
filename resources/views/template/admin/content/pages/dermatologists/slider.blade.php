<div class="carousel-contructor mb-5">
  <div class="flex justify-between items-end">
    <x-input-label for="technologistsAddSlide" class="mb-2" :value="__('Технологи')" />
    <button type="button" id="technologistsAddSlide" class="addSlide button button-success button-sm mb-2" data-id="technologists">Добавить технолога</button>
  </div>
  <div id="technologists_donor" style="display: none;">
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
                id="technologists"
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
        <x-input-label :value="__('Описание')" />
        <x-textarea data-name="description" class="mt-1 block w-full tinymce-textarea" disabled/>
      </div>
    </x-admin.content.home-slider-1>
  </div>
  <div class="carousel space-y-3 mb-1 p-2 border-gray-300 rounded-md border">
    @if(isset($content->carousel_data['technologists']))
      @foreach($content->carousel_data['technologists'] as $index => $slide)
        <x-admin.content.home-slider-1>
          <div class="slideImage">
            <x-input-label class="image-label mb-2" :value="__('Изображение')" />
            <div class="form-group">
              <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
                <div id="lfm-preview-technologists-{{ $index }}" class="lfm-preview flex flex-wrap" data-name="carousel_data[technologists][{{ $index }}][image]">
                  @if (isset($slide['image']['img'])&&!empty($slide['image']['img']))
                    <input type="hidden" name="carousel_data[technologists][{{ $index }}][image][img]" data-name="img" value="{{ $slide['image']['img'] }}">
                    <input type="hidden" name="carousel_data[technologists][{{ $index }}][image][thumb]" data-name="thumb" value="{{ $slide['image']['thumb'] }}">
                    <a href="javascript:;" data-fancybox="true" data-src="{{ $slide['image']['img'] }}" class="block rounded-md overflow-hidden w-20 m-2">
                      <img src="{{ $slide['image']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
                    </a>
                  @elseif(isset($slide['image'])&&is_array($slide['image']))
                    @foreach($slide['image'] as $key => $image)
                      <input type="hidden" name="carousel_data[technologists][{{ $index }}][image][{{ $key }}][img]" data-name="img" value="{{ $image['img'] }}">
                      <input type="hidden" name="carousel_data[technologists][{{ $index }}][image][{{ $key }}][thumb]" data-name="thumb" value="{{ $image['thumb'] }}">
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
                    id="technologists"
                    class="button button-secondary"
                    data-lfm="image"
                    data-multiple="1"
                    data-preview="lfm-preview-technologists-{{ $index }}">Выбрать изображение</button>
                </div>
              </div>
            </div>

          </div>
          <div class="form-group">
            <x-input-label :value="__('Имя')" />
            <x-text-input type="text" name="carousel_data[technologists][{{ $index }}][name]" id="carousel_data-technologists-{{ $index }}-name" data-name="name" class="mt-1 block w-full" value="{{ $slide['name'] }}"/>
          </div>
          <div class="form-group">
            <x-input-label :value="__('Описание')" />
            <x-textarea name="carousel_data[technologists][{{ $index }}][description]" id="carousel_data-technologists-{{ $index }}-description" data-name="description" class="mt-1 block w-full tinymce-textarea">{{ $slide['description'] }}</x-textarea>
          </div>
        </x-admin.content.home-slider-1>
      @endforeach
    @endif
  </div>
</div>

<div class="carousel-contructor mb-5">
  <div class="flex justify-between items-end">
    <x-input-label for="dermatologistsAddSlide" class="mb-2" :value="__('Дерматологи')" />
    <button type="button" id="dermatologistsAddSlide" class="addSlide button button-success button-sm mb-2" data-id="dermatologists">Добавить дерматолога</button>
  </div>
  <div id="dermatologists_donor" style="display: none;">
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
                id="dermatologists"
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
        <x-input-label :value="__('Описание')" />
        <x-textarea  data-name="description" class="mt-1 block w-full tinymce-textarea" disabled/>
      </div>
    </x-admin.content.home-slider-1>
  </div>
  <div class="carousel space-y-3 mb-1 p-2 border-gray-300 rounded-md border">
    @if(isset($content->carousel_data['dermatologists']))
      @foreach($content->carousel_data['dermatologists'] as $index => $slide)
        <x-admin.content.home-slider-1>
          <div class="slideImage">
            <x-input-label class="image-label mb-2" :value="__('Изображение')" />
            <div class="form-group">
              <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
                <div id="lfm-preview-dermatologists-{{ $index }}" class="lfm-preview flex flex-wrap" data-name="carousel_data[dermatologists][{{ $index }}][image]">
                  @if (isset($slide['image']['img'])&&!empty($slide['image']['img']))
                    <input type="hidden" name="carousel_data[dermatologists][{{ $index }}][image][img]" data-name="img" value="{{ $slide['image']['img'] }}">
                    <input type="hidden" name="carousel_data[dermatologists][{{ $index }}][image][thumb]" data-name="thumb" value="{{ $slide['image']['thumb'] }}">
                    <a href="javascript:;" data-fancybox="true" data-src="{{ $slide['image']['img'] }}" class="block rounded-md overflow-hidden w-20 m-2">
                      <img src="{{ $slide['image']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
                    </a>
                  @elseif(isset($slide['image'])&&is_array($slide['image']))
                    @foreach($slide['image'] as $key => $image)
                      <input type="hidden" name="carousel_data[dermatologists][{{ $index }}][image][{{ $key }}][img]" data-name="img" value="{{ $image['img'] }}">
                      <input type="hidden" name="carousel_data[dermatologists][{{ $index }}][image][{{ $key }}][thumb]" data-name="thumb" value="{{ $image['thumb'] }}">
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
                    id="dermatologists"
                    class="button button-secondary"
                    data-lfm="image"
                    data-multiple="1"
                    data-preview="lfm-preview-dermatologists-{{ $index }}">Выбрать изображение</button>
                </div>
              </div>
            </div>

          </div>
          <div class="form-group">
            <x-input-label :value="__('Имя')" />
            <x-text-input type="text" name="carousel_data[dermatologists][{{ $index }}][name]" id="carousel_data-dermatologists-{{ $index }}-name" data-name="name" class="mt-1 block w-full" value="{{ $slide['name'] }}"/>
          </div>
          <div class="form-group">
            <x-input-label :value="__('Описание')" />
            <x-textarea name="carousel_data[dermatologists][{{ $index }}][description]" id="carousel_data-dermatologists-{{ $index }}-description" data-name="description" class="mt-1 block w-full tinymce-textarea">{{ $slide['description'] }}</x-textarea>
          </div>
        </x-admin.content.home-slider-1>
      @endforeach
    @endif
  </div>
</div>

