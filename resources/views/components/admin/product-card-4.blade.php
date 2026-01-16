@props(['index' => '{number}'])
@props(['disabled' => false])
@props(['card' => null])
@props(['card_index' => null])

<div class="product-card_item mb-3 last:mb-0 bg-blue-100 rounded-lg">
  @if($card)
    <input type="hidden" name="card_data" value="{{ e(json_encode($card, JSON_UNESCAPED_UNICODE)) }}">
  @endif
  @if($card_index)
    <input type="hidden" name="card_index" value="@json($card_index)">
  @endif
  <input type="hidden" data-name="card_style">
  <div class="p-4 border border-gray-200 rounded-lg bg-white">
    <div class="w-full">
      <div class="form-group lfm-box">
        <div class="product_card-style-4 relative bg-slate-100 lfm-preview rounded-md mb-2 overflow-hidden max-w-sm">
          <div class="card_fields z-10 absolute left-0 right-0 w-full h-full"></div>
          <div class="img product_card_preview item-square"></div>
        </div>
        <input type="hidden" data-name="img">
        <input type="hidden" data-name="thumb">
      </div>
      <div class="flex space-x-1 mb-2">
        <button type="button" class="addImage bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 text-sm rounded">Загрузить изображение</button>
      </div>
      <div class="fields">
        <div class="field mb-3 last:mb-0 bg-slate-300 rounded-lg field-input">
          <div class="p-4 border border-gray-200 bg-white rounded-lg dynamic-item_container">
            <div class="w-full">
              <div class="form-group">
                <x-input-label :value="__('Текст')" />
                <x-textarea data-name="text" class="w-full"/>
              </div>
              <div class="space-y-1">
                <div class="relative form-group !mb-0">
                  <input type="text" data-name="color" class="card-action rounded-md bg-blue-100 block border border-2 border-blue-100 p-1 h-8 max-w-xs" placeholder="Цвет текста">
                </div>
                <div class="relative form-group !mb-0">
                  <input type="text" data-name="background" class="field-action rounded-md bg-blue-100 block border border-2 border-blue-100 p-1 h-8 max-w-xs" placeholder="#ffffff">
                </div>
                <div class="flex space-x-1">
                  <div class="relative radio-group">
                    <input type="radio" value="v-pos-top" class="field-action radio-icon" data-name="vertical-align">
                    <label>
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler pointer-events-none icon-tabler-box-align-top" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M4 9.505h16v-5a1 1 0 0 0 -1 -1h-14a1 1 0 0 0 -1 1v5z" />
                        <path d="M4 14.505v-.01" />
                        <path d="M4 19.505v-.01" />
                        <path d="M9 19.505v-.01" />
                        <path d="M15 19.505v-.01" />
                        <path d="M20 19.505v-.01" />
                        <path d="M20 14.505v-.01" />
                      </svg>
                    </label>
                  </div>
                  <div class="relative radio-group">
                    <input type="radio" value="v-pos-bottom" class="field-action radio-icon" data-name="vertical-align">
                    <label>
                      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler pointer-events-none icon-tabler-box-align-bottom" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M4 14h16v5a1 1 0 0 1 -1 1h-14a1 1 0 0 1 -1 -1v-5z" />
                        <path d="M4 9v.01" />
                        <path d="M4 4v.01" />
                        <path d="M9 4v.01" />
                        <path d="M15 4v.01" />
                        <path d="M20 4v.01" />
                        <path d="M20 9v.01" />
                      </svg>
                    </label>
                  </div>
                  <div class="relative form-group !mb-0">
                    <input type="text" data-name="vertical-align-value" data-max-value="100" class="field-action numeric-field rounded-md bg-blue-100 block border border-2 border-blue-100 p-1 h-8 max-w-xs" placeholder="0">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="px-2">
    <a href="#" class="btn-up whitespace-nowrap text-slate-700 hover:text-slate-500 no-underline">Наверх</a>
    <a href="#" class="btn-down whitespace-nowrap text-slate-700 hover:text-slate-500 no-underline">Вниз</a>
    <a href="#" class="btn-remove whitespace-nowrap text-slate-700 hover:text-slate-500 no-underline">Удалить</a>
  </div>
</div>

