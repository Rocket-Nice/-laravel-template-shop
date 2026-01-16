@section('title', $seo['title'] ?? config('app.name'))
<x-cabinet-layout>


  <div class="px-6 md:px-8 lg:px-14 xl:px-21.5 pb-12 d-text-body m-text-body">
    <h2 class="uppercase text-sm sm:text-base md:text-lg lg:text-2xl xl:text-4xl my-5 md:my-9 lg:my-12 text-customBrown text-center">Партнерский кабинет</h2>
    <div>
      <div class="p-4">
        Ваша ссылка: {{ route('partner', $partner->slug) }}<br/>

        <div>Всего платежей: {{ $orders->total() }} на сумму: {{ formatPrice($total) }}</div>
      </div>
      <table class="text-customBrown border-t border-customBrownOpacity text-sm sm:text-md md:text-lg lg:text-xl table-auto w-full text-center border-collapse leading-none">
        <tbody>
        @forelse($orders as $order)
          <tr>
            <td class="border-b border-customBrownOpacity py-6 md:py-12">
              <div class="flex space-x-1 items-center justify-center">
                {{ $order->getOrderNumber() }}
              </div>
            </td>
            <td class="border-b border-customBrownOpacity py-6 md:py-12">
              {!! mb_substr($order->email, 0, 2).'******@*****' !!}
            </td>
            <td class="border-b border-customBrownOpacity py-6 md:py-12">
              {{ formatPrice($order->total) }}
            </td>
            <td class="border-b border-customBrownOpacity py-6 md:py-12">
              {{ date('d.m.Y H:i', strtotime($order->created_at)) }}
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="p-4 text-gray-300">
              Пока нет заказов
            </td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>

  </div>
</x-cabinet-layout>
