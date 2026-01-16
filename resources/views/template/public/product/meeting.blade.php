@section('title', $seo['title'] ?? config('app.name'))
<x-app-layout>
  @include('_parts.public.pageTopBlock')

  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 pt-6">
    <div>
      <div class="bg-myBrown text-myBrown bg-opacity-[0.18] text-32 text-center py-4 font-semibold">
        Всего <span class="cormorantInfant">20</span> мест
      </div>
    </div>
  </div>
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-6 sm:py-10 md:py-14 lg:py-16 xl:py-[86px]">
    <div>
      <div class="border-t border-myBrown mb-6 mx-auto w-[186px] sm:w-[292px]"></div>
      <ul class="list-disc list-inside text-center mx-auto md:text-2xl text-xl lh-outline-none uppercase">
        <li><span class="cormorantInfant">19</span> марта в <span class="cormorantInfant">16:00</span></li>
        <li>г. Москва, Пресненская набережная, <span class="cormorantInfant">12</span>, Комплекс «Федерация» </li>
      </ul>
      <div class="border-t border-myBrown mt-6 mx-auto w-[248px] sm:w-[353px]"></div>
    </div>
  </div>
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 pt-6 sm:pt-0 pb-6 sm:pb-10 md:pb-14 lg:pb-16 xl:pb-[86px]">
    <div class="text-center">
      @if($product->getStock())
      <x-public.primary-button href="{{ route('order.meeting') }}" class="md:w-full max-w-[357px] md:text-2xl md:h-14">Забронировать место</x-public.primary-button>
      @else
        <div class="d-text-body m-text-body">Продажи закрыты</div>
      @endif
    </div>
  </div>
</x-app-layout>
