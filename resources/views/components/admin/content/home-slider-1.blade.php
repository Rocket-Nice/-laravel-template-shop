@props(['index' => '{number}'])
@props(['disabled' => false])
@props(['card' => null])

<div class="carousel-slide mb-3 last:mb-0 bg-blue-100 rounded-lg">
  <div class="p-4 border border-gray-200 rounded-lg bg-white">
    {{ $slot }}
  </div>
  <div class="px-2">
    <a href="#" class="btn-up whitespace-nowrap text-slate-700 hover:text-slate-500 no-underline">Наверх</a>
    <a href="#" class="btn-down whitespace-nowrap text-slate-700 hover:text-slate-500 no-underline">Вниз</a>
    <a href="#" class="btn-remove whitespace-nowrap text-slate-700 hover:text-slate-500 no-underline">Удалить</a>
  </div>
</div>
