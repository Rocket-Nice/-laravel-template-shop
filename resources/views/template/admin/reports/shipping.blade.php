<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
    <div class="flex-1 flex justify-end items-center relative" style="z-index: 100">
      @include('template.admin.reports._parts.reports-menu')
    </div>
  </x-slot>
  <x-admin.search-form :route="url()->current()">
    @if(request()->get('country'))
      <input type="hidden" name="country" value="{{ request()->get('country') }}">
    @endif
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="date_from" :value="__('Дата от')"/>
        <x-text-input type="text" name="date_from" id="date_from" value="{{ request()->get('date_from') }}" data-minDate="false" placeholder="{{ now()->startOfMonth()->format('d.m.Y H:i') }}" class="mt-1 block w-full datepicker"/>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="date_until" :value="__('Дата до')"/>
        <x-text-input type="text" name="date_until" id="date_until" value="{{ request()->get('date_until') }}" placeholder="{{ now()->format('d.m.Y H:i') }}" data-minDate="false" class="mt-1 block w-full datepicker"/>
      </div>
    </div>

  </x-admin.search-form>
  <div style="display: none;">
    <!-- Canvas для графика -->
    <div id="chart" class="w-screen h-screen">
      <canvas id="revenueChart" ></canvas>
    </div>
  </div>
  <div class="flex justify-end space-x-2">
{{--    <a href="javascript:;" data-fancybox data-src="#chart" class="block bg-gray-200 rounded-md p-1">--}}
{{--      <svg--}}
{{--        xmlns="http://www.w3.org/2000/svg"--}}
{{--        width="24"--}}
{{--        height="24"--}}
{{--        viewBox="0 0 24 24"--}}
{{--        fill="none"--}}
{{--        stroke="#000000"--}}
{{--        stroke-width="1"--}}
{{--        stroke-linecap="round"--}}
{{--        stroke-linejoin="round"--}}
{{--      >--}}
{{--        <path d="M3 13a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />--}}
{{--        <path d="M15 9a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />--}}
{{--        <path d="M9 5a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />--}}
{{--        <path d="M4 20h14" />--}}
{{--      </svg>--}}

{{--    </a>--}}
    <a href="{{ route('admin.reports.export', ['report' => 'order-shipping']) }}&{{ parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY) ?? '' }}" class="block bg-gray-200 rounded-md p-1">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="24"
        height="24"
        viewBox="0 0 24 24"
        fill="none"
        stroke="#000000"
        stroke-width="1"
        stroke-linecap="round"
        stroke-linejoin="round"
      >
        <path d="M12.5 21h-7.5a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v7.5" />
        <path d="M3 10h18" />
        <path d="M10 3v18" />
        <path d="M16 19h6" />
        <path d="M19 16l3 3l-3 3" />
      </svg>
    </a>
  </div>
  <div class="w-full mt-4 mx-auto flex flex-wrap gap-4" id="shipping-cards"></div>
  <script>
    (async () => {
      const res = await fetch('/admin/reports/shipping-data' + window.location.search);
      const rows = await res.json();
      const container = document.getElementById('shipping-cards');
      container.innerHTML = '';

      Object.entries(rows).forEach(([key, {total, name, shipping}]) => {

        container.innerHTML += `
            <div class="w-52 min-h-[110px] rounded-xl shadow-md flex flex-col items-center justify-center p-4 bg-gray-200">
                <a href="{{ route('admin.orders.index') }}?shipping[]=${shipping}" class="text-base text-gray-700 font-medium mb-2 text-center flex items-center gap-1">
                    ${name}
                </a>
                <div class="text-4xl font-bold text-blue-600">${total}</div>
            </div>
        `;
      });
    })();
  </script>
  <div class="bg-green-200 bg-red-200 bg-gray-200"></div>
</x-admin-layout>
