<div class="carousel-contructor mb-5">
  <div class="flex justify-between items-end">
    <x-input-label for="ourStoresAddSlide" class="mb-2" :value="__('Наши магазины')" />
    <button type="button" id="ourStoresAddSlide" class="addSlide button button-success button-sm mb-2" data-id="ourStores">Добавить магазин</button>
  </div>
  <div id="ourStores_donor" style="display: none;">
    <x-admin.content.home-slider-1>
      <div class="form-group">
        <x-input-label :value="__('Текст')" />
        <x-textarea data-name="text" class="mt-1 block w-full tinymce-textarea" disabled></x-textarea>
      </div>
      <div class="form-group">
        <x-input-label :value="__('Долгота и широта')" />
        <x-text-input type="text" data-name="latlon" class="mt-1 block w-full" disabled/>
        <div class="hint">Через запятую</div>
      </div>
    </x-admin.content.home-slider-1>
  </div>
  <div class="carousel space-y-3 mb-1 p-2 border-gray-300 rounded-md border">
    @if(isset($content->carousel_data['ourStores']))
      @foreach($content->carousel_data['ourStores'] as $index => $slide)
        <x-admin.content.home-slider-1>
          <div class="form-group">
            <x-input-label :value="__('Текст')" />
            <x-textarea name="carousel_data[ourStores][{{ $index }}][text]" id="carousel_data-ourStores-{{ $index }}-text" data-name="text" class="mt-1 block w-full tinymce-textarea">{{ $slide['text'] }}</x-textarea>
          </div>
          <div class="form-group">
            <x-input-label :value="__('Долгота и широта')" />
            <x-text-input name="carousel_data[ourStores][{{ $index }}][latlon]" id="carousel_data-ourStores-{{ $index }}-latlon" type="text" data-name="latlon" class="mt-1 block w-full" value="{{ $slide['latlon'] }}"/>
            <div class="hint">Через запятую</div>
          </div>
        </x-admin.content.home-slider-1>
      @endforeach
    @endif
  </div>
</div>
