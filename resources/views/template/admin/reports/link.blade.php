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
  </x-search-form>
  <div style="display: none;">
    <!-- Canvas для графика -->
    <div id="chart" class="w-screen h-screen">
      <canvas id="revenueChart"></canvas>
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
  </div>

  <div class="text-sm rounded-xl border border-gray-400 bg-gray-200 p-4 mt-4">
    <ul>
      <li class="font-bold">Всего просмотров: <span class="font-normal total_period_views"></span></li>
      <li class="font-bold">Всего заказов: <span class="font-normal total_period_orders"></span></li>
      <li class="font-bold">Общая сумма: <span class="font-normal total_period_sum"></span></li>
    </ul>
  </div>

  <div class="w-full mx-auto">
    <!-- Таблица под графиком -->
    <table id="statsTable" class="min-w-full w-full text-sm text-left mt-6 border">
      <thead class="bg-gray-100">
      <tr>
        <th class="bg-gray-100 border p-2">Период</th>
        <th class="bg-gray-100 border p-2">Просмотры</th>
        <th class="bg-gray-100 border p-2">Просмотры (всего)</th>
        <th class="bg-gray-100 border p-2">Заказы</th>
        <th class="bg-gray-100 border p-2">Заказы (всего)</th>
        <th class="bg-gray-100 border p-2">Сумма, ₽</th>
        <th class="bg-gray-100 border p-2">Сумма, ₽ (всего)</th>
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

    const fmtNum = n => n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');

    (async () => {
      const res  = await fetch('/admin/reports/links-data/{{ $link }}' + window.location.search);
      const rows = await res.json();

      const labels = rows.map(r => r.bucket_label);
      const views  = rows.map(r => Number(r.views_total));
      const orders = rows.map(r => Number(r.orders_total));
      const sums   = rows.map(r => Number(r.orders_sum_total));

      const viewsDiff  = rows.map(r => Number(r.views_diff));
      const ordersDiff = rows.map(r => Number(r.orders_diff));
      const sumsDiff   = rows.map(r => Number(r.orders_sum_diff));

      /* --- Диаграмма --- */
      new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: {
          labels,
          datasets: [
            { // сумма
              label: 'Сумма, ₽',
              data: sums,
              yAxisID: 'money',
              backgroundColor: 'rgba(54,162,235,0.6)',
              borderWidth: 1
            },
            { // заказы
              type: 'line',
              label: 'Заказы',
              data: orders,
              yAxisID: 'count',
              tension: 0.3,
              borderColor: 'rgba(255,99,132,0.9)',
              borderWidth: 2,
              pointRadius: 3
            },
            { // визиты
              type: 'line',
              label: 'Просмотры',
              data: views,
              yAxisID: 'count',
              tension: 0.3,
              borderColor: 'rgba(99,99,99,0.9)',
              borderDash: [5, 5],
              borderWidth: 2,
              pointRadius: 2
            }
          ]
        },
        options: {
          responsive: true,
          interaction: { mode: 'index', intersect: false },
          scales: {
            money: { beginAtZero: true, ticks: { callback: v => v.toLocaleString() } },
            count: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false } }
          },
          plugins: {
            tooltip: { callbacks: {
                label: ctx =>
                  ctx.dataset.label === 'Сумма, ₽'
                    ? fmtNum(ctx.parsed.y) + ' ₽'
                    : fmtNum(ctx.parsed.y)
              }}
          }
        }
      });

      /* --- Таблица --- */
      const tbody = document.querySelector('#statsTable tbody');
      rows.forEach((r, i) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
      <td class="border p-2">${labels[i]}</td>
      <td class="border p-2">${viewsDiff[i] > 0 ? '+'+fmtNum(viewsDiff[i]) : '0' }</td>
      <td class="border p-2">${fmtNum(views[i])}</td>
      <td class="border p-2">${ordersDiff[i] > 0 ? '+'+fmtNum(ordersDiff[i]) : '0' }</td>
      <td class="border p-2">${fmtNum(orders[i])}</td>
      <td class="border p-2">${sumsDiff[i] > 0 ? '+'+fmtNum(sumsDiff[i]) : '' }</td>
      <td class="border p-2">${fmtNum(sums[i])}</td>`;
        tbody.appendChild(tr);

        document.querySelector('.total_period_views').textContent  = fmtNum(r.total_period_views);
        document.querySelector('.total_period_orders').textContent = fmtNum(r.total_period_orders);
        document.querySelector('.total_period_sum').textContent    = fmtNum(r.total_period_sum) + ' ₽';
      });
    })();
  </script>
</x-admin-layout>
