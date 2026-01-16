<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <div class="flex-1 flex justify-end items-center relative" style="z-index: 100">
    @include('template.admin.reports._parts.reports-menu')
  </div>
  <x-search-form :route="url()->current()">
    <div class="p-1 w-full">
      <div class="form-group">
        <x-input-label for="link" :value="__('Ссылка')"/>
        <x-text-input type="text" name="link" id="link" value="{{ request()->get('link') }}" class="mt-1 block w-full"/>
      </div>
    </div>
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
  </x-search-form>
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
{{--    <a href="{{ route('admin.reports.export', ['report' => 'links']) }}&{{ parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY) ?? '' }}" class="block bg-gray-200 rounded-md p-1">--}}
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
{{--        <path d="M12.5 21h-7.5a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v7.5" />--}}
{{--        <path d="M3 10h18" />--}}
{{--        <path d="M10 3v18" />--}}
{{--        <path d="M16 19h6" />--}}
{{--        <path d="M19 16l3 3l-3 3" />--}}
{{--      </svg>--}}

{{--    </a>--}}
  </div>
  <div class="text-sm rounded-xl border border-gray-400 bg-gray-200 p-4 mt-4">
    <ul>
      <li class="font-bold">Всего переходов: <span class="font-normal total_views"></span></li>
      <li class="font-bold">Всего заказов: <span class="font-normal total_orders"></span></li>
      <li class="font-bold">Общая сумма: <span class="font-normal total_orders_amount"></span></li>
    </ul>
  </div>
  <div class="w-full mx-auto">
    <!-- Таблица под графиком -->
    <table id="statsTable" class="min-w-full w-full text-sm text-left mt-6 border">
      <thead class="bg-gray-100">
      <tr>
        <th class="bg-gray-100 border p-2">Наименование</th>
        <th class="bg-gray-100 border p-2">Переходы</th>
        <th class="bg-gray-100 border p-2">Заказы</th>
        <th class="bg-gray-100 border p-2">Сумма, ₽</th>
        <th class="bg-gray-100 border p-2">Конверсия</th>
        <th class="bg-gray-100 border p-2"></th>
      </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js/dist/chart.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/dayjs@1/locale/ru.js"></script>
  <script>
    const fmtNum = n => n
      .toString()
      .replace(/\B(?=(\d{3})+(?!\d))/g, ' ');

    (async () => {
      const res  = await fetch('/admin/reports/links-data' + window.location.search);
      const rows = await res.json();

      const tbody = document.querySelector('#statsTable tbody');

      Object.entries(rows.stats).forEach(([key, { partner, partner_info, views_count, orders_count, orders_total_sum, conversion }]) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
      <td class="border p-2">${partner}</td>
      <td class="border p-2">${fmtNum(views_count)}</td>
      <td class="border p-2">${fmtNum(orders_count)}</td>
      <td class="border p-2">${fmtNum(orders_total_sum)}</td>
      <td class="border p-2">${fmtNum(conversion)}%</td>
      <td class="border p-2"><a href="${partner_info}"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
        <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
      </svg></a></td>`;
        tbody.appendChild(tr);
      });
      document.querySelector('.total_views').textContent = fmtNum(rows.total_views);
      document.querySelector('.total_orders').textContent = `${fmtNum(rows.total_orders)}`;
      document.querySelector('.total_orders_amount').textContent = `${fmtNum(rows.total_orders_amount)} ₽`;
    })();
  </script>
</x-admin-layout>
