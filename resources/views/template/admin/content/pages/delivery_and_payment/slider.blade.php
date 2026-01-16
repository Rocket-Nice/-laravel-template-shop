<div class="carousel-contructor mb-5">
  <div class="flex justify-between items-end">
    <x-input-label for="deliveryElemsAddSlide" class="mb-2" :value="__('Иконки доставки')"/>
    <button type="button" id="deliveryElemsAddSlide" class="addSlide button button-success button-sm mb-2"
            data-id="deliveryElems">Добавить элемент
    </button>
  </div>
  <div id="deliveryElems_donor" style="display: none;">
    <x-admin.content.home-slider-1>
      <div class="form-group">
        <x-input-label :value="__('Иконка')"/>
        <select data-name="icon" data-field="carousel_data" class="choisesImgSelect form-control" disabled>
          @include('_parts.admin.icons')
        </select>
      </div>
      <div class="form-group">
        <x-input-label :value="__('Текст')"/>
        <x-textarea data-name="text" data-field="carousel_data" class="mt-1 block w-full" disabled></x-textarea>
      </div>
    </x-admin.content.home-slider-1>
  </div>
  <div class="carousel space-y-3 mb-1 p-2 border-gray-300 rounded-md border">
    @if(isset($content->carousel_data['deliveryElems']))
      @foreach($content->carousel_data['deliveryElems'] as $index => $elem)
        <x-admin.content.home-slider-1>
          <div class="form-group">
            <x-input-label for="carousel_data-deliveryElems-{{ $index }}-icon" :value="__('Иконка')"/>
            <select name="carousel_data[deliveryElems][{{ $index }}][icon]"
                    id="carousel_data-deliveryElems-{{ $index }}-icon" data-name="icon" data-field="carousel_data"
                    class="choisesImgSelect form-control">
              @include('_parts.admin.icons', ['selected' => $elem['icon']])
            </select>
          </div>
          <div class="form-group">
            <x-input-label for="carousel_data-deliveryElems-{{ $index }}-text" :value="__('Текст')"/>
            <x-textarea name="carousel_data[deliveryElems][{{ $index }}][text]"
                        id="carousel_data-deliveryElems-{{ $index }}-text" data-name="text" data-field="carousel_data"
                        class="mt-1 block w-full">{{ $elem['text'] }}</x-textarea>
          </div>
        </x-admin.content.home-slider-1>
      @endforeach
    @endif
  </div>
</div>
