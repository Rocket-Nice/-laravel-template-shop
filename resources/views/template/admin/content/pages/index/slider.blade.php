<div class="carousel-contructor mb-5">
  <div class="flex justify-between items-end">
    <x-input-label for="firstSliderAddSlide" class="mb-2" :value="__('Первый слайдер')" />
    <button type="button" id="firstSliderAddSlide" class="addSlide button button-success button-sm mb-2" data-id="firstSlider">Добавить слайд</button>
  </div>
  <div id="firstSlider_donor" style="display: none;">
    <x-admin.content.home-slider-1>
      <div class="slideImage">
        <x-input-label class="image-label mb-2" :value="__('Изображение')" />

        <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
          <div class="lfm-preview flex flex-wrap"></div>
          <div class="mb-2">
            <div class="mb-2 text-gray-500 text-sm">546x658px</div>
            <button
              type="button"
              id="firstSlider"
              class="button button-secondary"
              data-lfm="image">Выбрать изображение</button>
          </div>

        </div>
      </div>
      <div class="form-group flex items-center">
        <x-checkbox data-name="button" value="1"/>
        <x-input-label class="ml-2" :value="__('Ссылка кнопкой')"/>
      </div>
      <div class="form-group">
        <x-input-label :value="__('Ссылка')" />
        <x-text-input type="text" data-name="link" class="mt-1 block w-full" disabled/>
      </div>
      <div class="form-group">
        <x-input-label :value="__('Идентификатор')" />
        <x-text-input type="text" data-name="id" class="mt-1 block w-full" disabled/>
      </div>
    </x-admin.content.home-slider-1>
  </div>
  <div class="carousel space-y-3 mb-1 p-2 border-gray-300 rounded-md border">
    @if(isset($content->carousel_data['firstSlider']))
      @foreach($content->carousel_data['firstSlider'] as $index => $slide)
        <x-admin.content.home-slider-1>
          <div class="slideImage">
            <x-input-label class="image-label mb-2" :value="__('Изображение')" />

            <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
              <div id="lfm-preview-firstSlider-{{ $index }}" class="lfm-preview flex flex-wrap" data-name="carousel_data[firstSlider][{{ $index }}][image]">
                <input type="hidden" name="carousel_data[firstSlider][{{ $index }}][image][img]" id="carousel_data-firstSlider-{{ $index }}-[image]img" data-name="img" data-parent="image" value="{{ $slide['image']['img'] ?? '' }}">
                <input type="hidden" name="carousel_data[firstSlider][{{ $index }}][image][thumb]" id="carousel_data-firstSlider-{{ $index }}-[image]thumb" data-name="thumb" data-parent="image" value="{{ $slide['image']['thumb'] ?? '' }}">

                <a href="javascript:;" data-fancybox="true" data-src="{{ $slide['image']['img'] ?? '' }}" style="display: block;">
                  <img src="{{ $slide['image']['thumb'] ?? '' }}" class="overflow-hidden max-w-full object-cover object-center">
                </a>
              </div>
              <div class="mb-2">
                <div class="mb-2 text-gray-500 text-sm">546x658px</div>
                <button
                  type="button"
                  id="firstSlider"
                  class="button button-secondary"
                  data-lfm="image"
                  data-preview="lfm-preview-firstSlider-{{ $index }}">Выбрать изображение</button>
              </div>
            </div>
          </div>

          <div class="form-group flex items-center">
            <x-checkbox name="carousel_data[firstSlider][{{ $index }}][button]" id="carousel_data-firstSlider-{{ $index }}-button" data-name="button" value="1" :checked="($slide['button'] ?? false) ? true : false"/>
            <x-input-label class="ml-2" for="carousel_data-firstSlider-{{ $index }}-button" :value="__('Ссылка кнопкой')"/>
          </div>
          <div class="form-group">
            <x-input-label :value="__('Ссылка')" />
            <x-text-input type="text" name="carousel_data[firstSlider][{{ $index }}][link]" id="carousel_data-firstSlider-{{ $index }}-link" data-name="link" class="mt-1 block w-full" value="{{ $slide['link'] }}"/>
          </div>
          <div class="form-group">
            <x-input-label :value="__('Идентификатор')" />
            <x-text-input type="text" name="carousel_data[firstSlider][{{ $index }}][id]" id="carousel_data-firstSlider-{{ $index }}-id" data-name="id" class="mt-1 block w-full" value="{{ $slide['id'] ?? '' }}"/>
          </div>
        </x-admin.content.home-slider-1>
      @endforeach
    @endif
  </div>
</div>
<div class="carousel-contructor mb-5">
  <div class="form-group">
    <x-input-label for="carousel_data-weRecommend" :value="__('Слайдер с товарами')"/>
    <select name="carousel_data[weRecommend][]" id="carousel_data-weRecommend" multiple class="multipleSelect form-control">
      @foreach($products as $product)
        <option value="{{ $product->id }}" data-keywords="{{ $product->category_title }}" @if(isset($content->carousel_data['weRecommend'])&&in_array($product->id, $content->carousel_data['weRecommend'])){!! 'selected' !!}@endif>{{ $product->id }}: {{ $product->name }} ({{ $product->sku }}, Категория "{{ $product->category_title }}")</option>
      @endforeach
    </select>
  </div>
</div>
<div class="carousel-contructor mb-5">
  <div class="form-group">
    <x-input-label for="carousel_data-weRecommend2" :value="__('ТОП продукты для отпуска')"/>
    <select name="carousel_data[weRecommend2][]" id="carousel_data-weRecommend2" multiple class="multipleSelect form-control">
      @foreach($products as $product)
        <option value="{{ $product->id }}" data-keywords="{{ $product->category_title }}" @if(isset($content->carousel_data['weRecommend2'])&&in_array($product->id, $content->carousel_data['weRecommend2'])){!! 'selected' !!}@endif>{{ $product->id }}: {{ $product->name }} ({{ $product->sku }}, Категория "{{ $product->category_title }}")</option>
      @endforeach
    </select>
  </div>
</div>
<div class="carousel-contructor mb-5">
  <div class="flex justify-between items-end">
    <x-input-label for="ourNewProductsAddSlide" class="mb-2" :value="__('Первый слайдер')" />
    <button type="button" id="ourNewProductsAddSlide" class="addSlide button button-success button-sm mb-2" data-id="ourNewProducts">Добавить слайд</button>
  </div>
  <div id="ourNewProducts_donor" style="display: none;">
    <x-admin.content.home-slider-1>
      <div class="slideImage">
        <x-input-label class="image-label mb-2" :value="__('Изображение')" />

        <div class="form-group">

          <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
            <div class="lfm-preview flex flex-wrap"></div>
            <div class="mb-2">
              <div class="mb-2 text-gray-500 text-sm">395x577px и 909x577px</div>
              <button
                type="button"
                id="ourNewProducts"
                class="button button-secondary"
                data-lfm="image">Выбрать изображение</button>
            </div>

          </div>
          <div class="hint">Четные слайды с изображением шириной 1/3, нечетные 2/3</div>

        </div>

      </div>
      <div class="form-group">
        <x-input-label :value="__('Ссылка')" />
        <x-text-input type="text" data-name="link" class="mt-1 block w-full" disabled/>
      </div>
      <div class="form-group">
        <x-input-label :value="__('Заголовок')" />
        <x-text-input type="text" data-name="headline" class="mt-1 block w-full" disabled/>
      </div>
      <div class="form-group">
        <x-input-label :value="__('Подпись')" />
        <x-text-input type="text" data-name="subtitle" class="mt-1 block w-full" disabled/>
      </div>
    </x-admin.content.home-slider-1>
  </div>
  <div class="carousel space-y-3 mb-1 p-2 border-gray-300 rounded-md border">
    @if(isset($content->carousel_data['ourNewProducts']))
      @foreach($content->carousel_data['ourNewProducts'] as $index => $slide)
        <x-admin.content.home-slider-1>
          <div class="slideImage">
            <x-input-label class="image-label mb-2" :value="__('Изображение')" />


            <div class="form-group">
              <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
                <div id="lfm-preview-ourNewProducts-{{ $index }}" class="lfm-preview flex flex-wrap" data-name="carousel_data[ourNewProducts][{{ $index }}][image]">
                  @if(isset($slide['image']['img']))
                  <input type="hidden" name="carousel_data[ourNewProducts][{{ $index }}][image][img]" id="carousel_data-ourNewProducts-{{ $index }}-[image]img" data-name="img" data-parent="image" value="{{ $slide['image']['img'] }}">
                  <input type="hidden" name="carousel_data[ourNewProducts][{{ $index }}][image][thumb]" id="carousel_data-ourNewProducts-{{ $index }}-[image]thumb" data-name="thumb" data-parent="image" value="{{ $slide['image']['thumb'] }}">

                  <a href="javascript:;" data-fancybox="true" data-src="{{ $slide['image']['img'] }}" style="display: block;">
                    <img src="{{ $slide['image']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
                  </a>
                    @endif
                </div>
                <div class="mb-2">
                  <div class="mb-2 text-gray-500 text-sm">546x658px</div>
                  <button
                    type="button"
                    id="ourNewProducts"
                    class="button button-secondary"
                    data-lfm="image"
                    data-preview="lfm-preview-ourNewProducts-{{ $index }}">Выбрать изображение</button>
                </div>
              </div>
              <div class="hint">Четные слайды с изображением шириной 1/3, нечетные 2/3</div>
            </div>

          </div>
          <div class="form-group">
            <x-input-label :value="__('Ссылка')" />
            <x-text-input type="text" name="carousel_data[ourNewProducts][{{ $index }}][link]" id="carousel_data-ourNewProducts-{{ $index }}-link" data-name="link" class="mt-1 block w-full" value="{{ $slide['link'] }}"/>
          </div>
          <div class="form-group">
            <x-input-label :value="__('Заголовок')" />
            <x-text-input type="text" name="carousel_data[ourNewProducts][{{ $index }}][headline]" id="carousel_data-ourNewProducts-{{ $index }}-headline" data-name="headline" class="mt-1 block w-full" value="{{ $slide['headline'] }}"/>
          </div>
          <div class="form-group">
            <x-input-label :value="__('Подпись')" />
            <x-text-input type="text" name="carousel_data[ourNewProducts][{{ $index }}][subtitle]" id="carousel_data-ourNewProducts-{{ $index }}-subtitle" data-name="subtitle" class="mt-1 block w-full" value="{{ $slide['subtitle'] }}"/>
          </div>
        </x-admin.content.home-slider-1>
      @endforeach
    @endif
  </div>
</div>
