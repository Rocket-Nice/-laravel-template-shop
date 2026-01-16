@props(['index' => '{num}'])
@props(['id' => false])
@props(['disabled' => false])

<div class="field mb-3 last:mb-0 bg-slate-300 rounded-lg">
  <div class="p-4 border border-gray-200 bg-white rounded-lg dynamic-item_container">
    <div class="w-full">
      <div class="form-group">
        <x-input-label :value="__('Текст')" />
        <x-text-input type="text" data-name="text"/>
      </div>
      <div class="space-y-1">
        <div class="flex space-x-1">
          <div class="relative radio-group">
            <input type="radio" value="left" class="field-action radio-icon" data-name="text-align">
            <label>
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler pointer-events-none icon-tabler-align-left" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M4 6l16 0" />
                <path d="M4 12l10 0" />
                <path d="M4 18l14 0" />
              </svg>
            </label>
          </div>
          <div class="relative radio-group">
            <input type="radio" value="center" class="field-action radio-icon" data-name="text-align">
            <label>
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler pointer-events-none icon-tabler-align-center" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M4 6l16 0" />
                <path d="M8 12l8 0" />
                <path d="M6 18l12 0" />
              </svg>
            </label>
          </div>
          <div class="relative radio-group">
            <input type="radio" value="right" class="field-action radio-icon" data-name="text-align">
            <label>
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler pointer-events-none icon-tabler-align-right" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M4 6l16 0" />
                <path d="M10 12l10 0" />
                <path d="M6 18l14 0" />
              </svg>
            </label>
          </div>
        </div>
        <div class="flex space-x-1">
          <div class="relative radio-group">
            <input type="radio" value="h-pos-left" class="field-action radio-icon" data-name="align">
            <label>
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler pointer-events-none icon-tabler-box-align-left" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M9.752 19.753v-16h-5a1 1 0 0 0 -1 1v14a1 1 0 0 0 1 1h5z" />
                <path d="M14.752 19.753h-.01" />
                <path d="M19.753 19.753h-.011" />
                <path d="M19.753 14.752h-.011" />
                <path d="M19.753 8.752h-.011" />
                <path d="M19.753 3.752h-.011" />
                <path d="M14.752 3.752h-.01" />
              </svg>
            </label>
          </div>

          <div class="relative radio-group">
            <input type="radio" value="h-pos-center" class="field-action radio-icon" data-name="align">
            <label>
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler pointer-events-none icon-tabler-box-align-center" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M 9.5 3.753 V 19.753 A 1 1 0 0 1 9.5 19.753 H 14.5 A 1 1 0 0 1 14.5 19.753 V 3.753 A 1 1 0 0 1 14.5 3.753 Z"/>
                <path d="M4.5 19.753h.01" />
                <path d="M19.5 19.753h-.011" />
                <path d="M19.5 14.752h-.011" />
                <path d="M19.5 8.752h-.011" />
                <path d="M19.5 3.752h-.011" />
                <path d="M4.5 3.752h.01" />
                <path d="M4.5 8.752h.01" />
                <path d="M4.5 14.752h.01" />
              </svg>
            </label>
          </div>
          <div class="relative radio-group">
            <input type="radio" value="h-pos-right" class="field-action radio-icon" data-name="align">
            <label>
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler pointer-events-none icon-tabler-box-align-right" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M14.248 19.753v-16h5a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-5z" />
                <path d="M9.248 19.753h.01" />
                <path d="M4.247 19.753h.011" />
                <path d="M4.247 14.752h.011" />
                <path d="M4.247 8.752h.011" />
                <path d="M4.247 3.752h.011" />
                <path d="M9.248 3.752h.01" />
              </svg>
            </label>
          </div>
          <div class="relative form-group !mb-0">
            <input type="text" data-name="align-value" data-max-value="100" class="field-action numeric-field rounded-md bg-blue-100 block border border-2 border-blue-100 p-1 h-8 max-w-xs" placeholder="0">
          </div>
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
            <input type="radio" value="v-pos-center" class="field-action radio-icon" data-name="vertical-align">
            <label>
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler pointer-events-none icon-tabler-box-align-center-vertical" width="28" height="28" viewBox="0 0 24 24" stroke-width="2" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M 3.753 9.5 H 19.753 A 1 1 0 0 1 19.753 9.5 V 14.5 A 1 1 0 0 1 19.753 14.5 H 3.753 A 1 1 0 0 1 3.753 14.5 Z"/>
                <path d="M19.753 4.5v.01" />
                <path d="M19.753 19.5v-.011" />
                <path d="M14.753 19.5v-.011" />
                <path d="M8.753 19.5v-.011" />
                <path d="M3.753 19.5v-.011" />
                <path d="M3.753 4.5v.01" />
                <path d="M8.753 4.5v.01" />
                <path d="M14.753 4.5v.01" />
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
  <div class="px-2">
    <a href="#" class="field-btn-up whitespace-nowrap text-slate-700 hover:text-slate-500 no-underline">Наверх</a>
    <a href="#" class="field-btn-down whitespace-nowrap text-slate-700 hover:text-slate-500 no-underline">Вниз</a>
    <a href="#" class="field-btn-remove whitespace-nowrap text-slate-700 hover:text-slate-500 no-underline">Удалить</a>
  </div>
</div>
