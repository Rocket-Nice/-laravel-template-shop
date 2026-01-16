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
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="filter-unit" :value="__('Период')"/>
        <select id="filter-unit" name="unit" class="form-control w-full">
          <option value="day" @if(!request()->get('unit')||request()->get('unit')=='day'){!! 'selected' !!}@endif>День</option>
          <option value="hour" @if(request()->get('unit')&&request()->get('unit')=='hour'){!! 'selected' !!}@endif>Час</option>
          <option value="minute" @if(request()->get('unit')&&request()->get('unit')=='minute'){!! 'selected' !!}@endif>Минута</option>
        </select>
      </div>
    </div>
      <div class="p-1 w-full lg:w-1/2">
        <div class="form-group">
          <x-input-label for="step" :value="__('Количество')"/>
          <x-text-input type="text" name="step" id="step" value="{{ request()->get('step') }}" class="mt-1 block w-full numeric-field"/>
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
    <a href="javascript:;" data-fancybox data-src="#chart" class="block bg-gray-200 rounded-md p-1">
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
        <path d="M3 13a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
        <path d="M15 9a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
        <path d="M9 5a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
        <path d="M4 20h14" />
      </svg>

    </a>
    <a href="{{ route('admin.reports.export', ['report' => 'average-check']) }}&{{ parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY) ?? '' }}" class="block bg-gray-200 rounded-md p-1">
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
  <div class="text-sm rounded-xl border border-gray-400 bg-gray-200 p-4 mt-4">
    <ul>
      <li class="font-bold">Всего заказов: <span class="font-normal total_orders"></span></li>
      <li class="font-bold">Ср. стоимость заказа: <span class="font-normal total_basket"></span></li>
      <li class="font-bold">Ср. стоимость доставки: <span class="font-normal total_shipping"></span></li>
    </ul>
  </div>
  <div class="w-full mx-auto">
    <!-- Таблица под графиком -->
    <table id="statsTable" class="min-w-full w-full text-sm text-left mt-6 border">
      <thead class="bg-gray-100">
      <tr>
        <th class="bg-gray-100 border p-2">Период</th>
        <th class="bg-gray-100 border p-2">Количество заказов</th>
        <th class="bg-gray-100 border p-2">Ср. стоимость заказа</th>
        <th class="bg-gray-100 border p-2">Ср. стоимость доставки</th>
      </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js/dist/chart.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/dayjs@1/locale/ru.js"></script>
  <script>
    dayjs.locale('ru');

    const fmtNum = n => n
      .toString()
      .replace(/\B(?=(\d{3})+(?!\d))/g, ' ');

    (async () => {
      const res  = await fetch('/admin/reports/average-check-data' + window.location.search);
      const rows = await res.json();

      /* --- 2. Формируем массивы для чарта и таблицы --- */
      // const isDay  = (new URLSearchParams(location.search).get('unit') ?? 'hour') === 'day';
      const labels = rows.map(r => r.bucket_label);
      const orderCount   = rows.map(r => Number(r.basket_count));
      const avgCart   = rows.map(r => Number(r.basket_avg));
      const avgShipping   = rows.map(r => Number(r.shipping_avg));

      /* --- 3. Диаграмма (столбцы ― сумма, линии ― количество заказов) --- */
      new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: {
          labels,
          datasets: [
            {     // сумма
              label: 'Ср. стоимость заказа',
              data: avgCart,
              yAxisID: 'count',
              backgroundColor: 'rgba(54,162,235,0.6)',
              borderWidth: 1
            },
            {     // количество заказов
              type: 'line',
              label: 'Ср. стоимось доставки',
              data: avgShipping,
              yAxisID: 'shipping',
              tension: 0.3,
              borderWidth: 2,
              pointRadius: 3
            }
          ]
        },
        options: {
          responsive: true,
          interaction: { mode: 'index', intersect: false },
          scales: {
            shipping: { beginAtZero: true, ticks: { callback: v => v.toLocaleString() } },
            count: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false } }
          }
        }
      });

      /* --- 4. Заполняем таблицу под графиком --- */
      const tbody = document.querySelector('#statsTable tbody');
      rows.forEach((r, i) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
      <td class="border p-2">${labels[i]}</td>
      <td class="border p-2">${fmtNum(orderCount[i])}</td>
      <td class="border p-2">${fmtNum(avgCart[i])}</td>
      <td class="border p-2">${fmtNum(avgShipping[i])}</td>`;
        tbody.appendChild(tr);
        document.querySelector('.total_orders').textContent = fmtNum(r.total_orders);
        document.querySelector('.total_basket').textContent = `${fmtNum(r.total_basket)} ₽`;
        document.querySelector('.total_shipping').textContent = `${fmtNum(r.total_shipping)} ₽`;
      });
    })();
  </script>
</x-admin-layout>
