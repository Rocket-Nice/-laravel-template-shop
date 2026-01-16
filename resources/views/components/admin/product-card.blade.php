@props(['index' => '{number}'])
@props(['disabled' => false])
@props(['card' => null])

<div class="product-card_item mb-3 last:mb-0 bg-blue-100 rounded-lg">
  <input type="hidden" name="card_data" value="{{ e(json_encode($card, JSON_UNESCAPED_UNICODE)) }}">
  <input type="hidden" data-name="card_style">
  <div class="p-4 border border-gray-200 rounded-lg bg-white">
    <div class="w-full">
      <div class="form-group lfm-box">
        <div class="product_card-style-1 relative bg-slate-100 lfm-preview rounded-md mb-2 overflow-hidden max-w-sm">
          <div class="card_fields z-10 absolute top-0 left-0 w-full h-full"></div>
          <div class="img product_card_preview item-square"></div>
        </div>
        <input type="hidden" data-name="img">
        <input type="hidden" data-name="thumb">
      </div>
      <div class="flex space-x-1 mb-2">
        <button type="button" class="addImage bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 text-sm rounded">Загрузить изображение</button>
        <button type="button" class="addField bg-orange-500 hover:bg-orange-700 text-white font-bold py-1 px-3 text-sm rounded">Добавить поле</button>
      </div>
      <div class="fields"></div>
    </div>
  </div>
  <div class="px-2">
    <a href="#" class="btn-up whitespace-nowrap text-slate-700 hover:text-slate-500 no-underline">Наверх</a>
    <a href="#" class="btn-down whitespace-nowrap text-slate-700 hover:text-slate-500 no-underline">Вниз</a>
    <a href="#" class="btn-remove whitespace-nowrap text-slate-700 hover:text-slate-500 no-underline">Удалить</a>
  </div>
</div>

