@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <div>
    <x-search-form :route="url()->current()">
      <div class="p-1 w-full lg:w-1/2">
        <div class="form-group">
          <x-input-label for="date_from" :value="__('От даты')"/>
          <x-text-input type="text" name="date_from" id="date_from" value="{{ request()->get('date_from') }}" data-minDate="false" placeholder="{{ now()->subMonth()->format('d.m.Y') }}" data-timepicker="0" class="mt-1 block w-full datepicker"/>
        </div>
      </div>
      <div class="p-1 w-full lg:w-1/2">
        <div class="form-group">
          <x-input-label for="date_to" :value="__('До даты')"/>
          <x-text-input type="text" name="date_to" id="date_to" value="{{ request()->get('date_to') }}" data-minDate="false" placeholder="{{ now()->subMonth()->format('d.m.Y') }}" data-timepicker="0" class="mt-1 block w-full datepicker"/>
        </div>
      </div>
    </x-search-form>
    <div id="statsSummary" class="grid grid-cols-1 md:grid-cols-3 gap-4 my-6">
      <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-sm">
        <p class="text-sm text-gray-500">Всего посещений</p>
        <p id="totalViews" class="text-2xl font-bold text-gray-800">0</p>
      </div>
      <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-sm">
        <p class="text-sm text-gray-500">Всего заказов</p>
        <p id="totalOrders" class="text-2xl font-bold text-gray-800">0</p>
      </div>
      <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-sm">
        <p class="text-sm text-gray-500">Сумма заказов</p>
        <p id="totalAmount" class="text-2xl font-bold text-green-600">0</p>
      </div>
    </div>
    <canvas id="pageViewsChart"></canvas>

  </div>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.0/dist/chart.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
  <script>
    var pageViews = @json($viewsByDate);
    var orders = @json($ordersByDate);
    var ordersSum = @json($ordersSumByDate);
  </script>
  <script>
    // Форматирование суммы (при необходимости замените RUB на нужную валюту)
    function formatMoney(value) {
      return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: 'RUB',
        maximumFractionDigits: 0
      }).format(Number(value || 0));
    }

    function sumObjectValues(obj){
      return Object.values(obj).reduce((acc, v) => acc + Number(v || 0), 0);
    }

    // --- Сводные показатели ---
    document.getElementById('totalViews').textContent  = sumObjectValues(pageViews);
    document.getElementById('totalOrders').textContent = sumObjectValues(orders);
    document.getElementById('totalAmount').textContent = formatMoney(sumObjectValues(ordersSum));
  </script>

  <script>
    var ctx1 = document.getElementById('pageViewsChart').getContext('2d');
    var chart1 = new Chart(ctx1, {
      type: 'line',
      plugins: [ChartDataLabels],
      data: {
        labels: Object.keys(pageViews),
        datasets: [
          {
            label: 'Переходы на сайт',
            data: Object.values(pageViews),
            backgroundColor: 'rgba(145, 145, 145, 0.5)',
            borderColor: 'rgba(145, 145, 145, 1)',
            borderWidth: 1
          },
          {
            label: 'Заказы',
            data: Object.values(orders),
            backgroundColor: 'rgba(0, 123, 255, 0.5)',
            borderColor: 'rgba(0, 123, 255, 1)',
            borderWidth: 1
          },
        ]
      },
      options: {
        scales: {
          y: { beginAtZero: true }
        },
        plugins: {
          legend: { position: 'top' },
          tooltip: {
            enabled: true,
            callbacks: {
              // основной текст
              label: function(context) {
                let datasetLabel = context.dataset.label || '';
                let value = context.parsed.y;

                if (datasetLabel === 'Заказы') {
                  // достаём сумму по дате
                  let dateKey = context.label; // формат d.m.Y
                  let sumValue = ordersSum[dateKey] ?? 0;
                  let formattedSum = new Intl.NumberFormat('ru-RU', {
                    style: 'currency',
                    currency: 'RUB',
                    maximumFractionDigits: 0
                  }).format(sumValue);

                  return datasetLabel + ': ' + value + ' (на ' + formattedSum + ')';
                }

                return datasetLabel + ': ' + value;
              }
            }
          },
          datalabels: {
            display: true,
            backgroundColor: function(ctx){ return ctx.dataset.borderColor; },
            borderRadius: 4,
            color: 'white',
            font: { weight: 'bold' },
            formatter: (value) => value > 0 ? value : null
          }
        }
      }
    });
  </script>

</x-admin-layout>
