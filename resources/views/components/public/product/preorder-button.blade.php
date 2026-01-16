<x-public.primary-button href="javascript:;" data-fancybox="preorder-{{ $id }}" data-src="#preorder-{{ $id }}" class="w-full !px-2 text-center">
  <span class="text-base sm:text-xl whitespace-nowrap">Оформить предзаказ</span>
</x-public.primary-button>
<div class="hidden">
  <div id="preorder-{{ $id }}" class="d-text-body m-text-body max-w-3xl" style="display: none;">
    <div class="p-2 sm:p-4">
      <div class="mb-4">
        <h3 class="d-headline-4 m-headline-3">Внимание</h3>
        <p>Ожидаемая отправка с 5 по 17 декабря<br/>После отгрузки товара, статус вашего заказа изменится в личном кабинете</p>
      </div>
      <x-public.primary-button href="#" class="toCart w-full !px-2 text-center" data-id="{{ $id }}">Подтверждаю заказ</x-public.primary-button>
    </div>
  </div>
</div>
