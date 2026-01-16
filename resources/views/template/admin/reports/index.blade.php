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
      <script>
        const exceptions = @json(request()->exceptions ?? []);
      </script>
      <div
        class="w-full p-1"
        x-data="exceptionsForm(exceptions, '.datepicker')"
        x-init="init()"
      >
        <div class="space-y-1">

          <div class="p-1 w-full flex justify-end gap-2">
            <button type="button" class="button button-sm button-light-secondary"
                    @click="add()">
              Добавить исключение
            </button>
            <template x-if="exceptions.length">
              <button type="button" class="button button-sm button-light-danger"
                      @click="clearAll()">
                Очистить все
              </button>
            </template>
          </div>

          {{-- Динамические исключения --}}
          <template x-for="(item, i) in exceptions" :key="i">
            <div class="w-full flex flex-wrap -m-1 p-1">
              <div class="p-1 border border-gray-200 rounded-xl w-full flex flex-wrap -m-1">
                <div class="p-1 w-full flex justify-between items-center">
                  <div class="font-medium">Исключение <span x-text="i + 1"></span></div>
                  <button type="button"
                          @click="remove(i)">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="24"
                      height="24"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="#ddd"
                      stroke-width="1"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    >
                      <path d="M18 6l-12 12" />
                      <path d="M6 6l12 12" />
                    </svg>

                  </button>
                </div>

                <div class="p-1 w-full lg:w-1/2">
                  <div class="form-group">
                    <label :for="'exceptions_'+i+'_date_from'">Дата от</label>
                    <input type="text"
                           class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm datepicker"
                           :id="'exceptions_'+i+'_date_from'"
                           :name="'exceptions['+i+'][date_from]'"
                           x-model="item.date_from"
                           data-minDate="false"
                           placeholder="{{ now()->startOfMonth()->format('d.m.Y H:i') }}"
                           required
                    />
                    @isset($errors)
                      {{-- Показ ошибок на конкретную строку --}}
                      <template x-if="$store?.errors?.['exceptions.'+i+'.date_from']">
                        <p class="text-red-600 text-sm mt-1"
                           x-text="$store.errors['exceptions.'+i+'.date_from']"></p>
                      </template>
                    @endisset
                  </div>
                </div>

                <div class="p-1 w-full lg:w-1/2">
                  <div class="form-group">
                    <label :for="'exceptions_'+i+'_date_until'">Дата до</label>
                    <input type="text"
                           class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm datepicker"
                           :id="'exceptions_'+i+'_date_until'"
                           :name="'exceptions['+i+'][date_until]'"
                           x-model="item.date_until"
                           data-minDate="false"
                           placeholder="{{ now()->format('d.m.Y H:i') }}"
                           required
                    />
                    @isset($errors)
                      <template x-if="$store?.errors?.['exceptions.'+i+'.date_until']">
                        <p class="text-red-600 text-sm mt-1"
                           x-text="$store.errors['exceptions.'+i+'.date_until']"></p>
                      </template>
                    @endisset
                  </div>
                </div>
              </div>


            </div>
          </template>

        </div>
      </div>
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="filter-shipping" :value="__('Способ доставки')"/>
        <select id="filter-shipping" name="shipping[]" multiple class="multipleSelect form-control">
          @foreach($shipping_methods as $shipping_method)
            <option
              value="{{ $shipping_method->code }}" @if(is_array(request()->get('shipping'))&&in_array($shipping_method->code, request()->get('shipping')))
              {!! 'selected' !!}
              @endif>{{ $shipping_method->name }}
            </option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="filter-status" :value="__('Статус заказа')"/>
        <select id="filter-status" name="status[]" multiple class="multipleSelect form-control">
          <option value="is_processing" @if(is_array(request()->get('status'))&&in_array('is_processing', request()->get('status'))){!! 'selected' !!}@endif>В обработке</option>
          <option value="is_waiting" @if(is_array(request()->get('status'))&&in_array('is_waiting', request()->get('status'))){!! 'selected' !!}@endif>В ожидании</option>
          <option value="was_processed" @if(is_array(request()->get('status'))&&in_array('was_processed', request()->get('status'))){!! 'selected' !!}@endif>Обработан</option>
          <option value="was_sended_to_store" @if(is_array(request()->get('status'))&&in_array('was_sended_to_store', request()->get('status'))){!! 'selected' !!}@endif>Отправлен в сборку на склад</option>
          <option value="is_assembled" @if(is_array(request()->get('status'))&&in_array('is_assembled', request()->get('status'))){!! 'selected' !!}@endif>Собран на складе</option>
          <option value="is_ready" @if(is_array(request()->get('status'))&&in_array('is_ready', request()->get('status'))){!! 'selected' !!}@endif>Готов к выдачи</option>
          <option value="was_delivered" @if(is_array(request()->get('status'))&&in_array('was_delivered', request()->get('status'))){!! 'selected' !!}@endif>Выдан</option>
          <option value="refund" @if(is_array(request()->get('status'))&&in_array('refund', request()->get('status'))){!! 'selected' !!}@endif>Возврат</option>
          <option value="cdek_CREATED" @if(is_array(request()->get('status'))&&in_array('cdek_CREATED', request()->get('status'))){!! 'selected' !!}@endif>Сдэк: Создан</option>
          <option value="boxberry_загружен реестр им" @if(is_array(request()->get('status'))&&in_array('boxberry_загружен реестр им', request()->get('status'))){!! 'selected' !!}@endif>Boxberry: Загружен реестр ИМ</option>
          <option value="has_error" @if(is_array(request()->get('status'))&&in_array('has_error', request()->get('status'))){!! 'selected' !!}@endif>Ошибка в заказе</option>
          <option value="no_gift" @if(is_array(request()->get('status'))&&in_array('no_gift', request()->get('status'))){!! 'selected' !!}@endif>Не выбран подарок</option>
          <option value="address_error" @if(is_array(request()->get('status'))&&in_array('address_error', request()->get('status'))){!! 'selected' !!}@endif>Ошибка в адресе</option>
          <option value="test" @if(is_array(request()->get('status'))&&in_array('test', request()->get('status'))){!! 'selected' !!}@endif>Тест</option>
          <option value="not_in_demand" @if(is_array(request()->get('status'))&&in_array('not_in_demand', request()->get('status'))){!! 'selected' !!}@endif>Не востребован</option>
        </select>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="filter-unit" :value="__('Период')"/>
        <select id="filter-unit" name="unit" class="form-control w-full">
          <option value="day" @if(request()->get('unit')&&request()->get('unit')=='day'){!! 'selected' !!}@endif>День</option>
          <option value="hour" @if(!request()->get('unit')||request()->get('unit')=='hour'){!! 'selected' !!}@endif>Час</option>
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
    <div class="p-1 w-full">
      <div class="form-group">
        <div class="flex items-center">
          <x-checkbox id="has_paid" name="has_paid" value="1"
                      :checked="request()->has_paid ? true : false"/>
          <x-input-label for="has_paid" class="ml-2" :value="__('Показать неоплаченные')"/>
        </div>
      </div>
    </div>
    <div class="p-1 w-full">
      <div class="form-group">
        <div class="flex items-center">
          <x-checkbox id="preorder" name="preorder" value="1"
                      :checked="request()->preorder ? true : false"/>
          <x-input-label for="preorder" class="ml-2" :value="__('Показать предзаказы')"/>
        </div>
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
    <a href="{{ route('admin.reports.export') }}{{ stristr($_SERVER['REQUEST_URI'], '?') }}" class="block bg-gray-200 rounded-md p-1">
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
      <li class="font-bold">Всего заказов: <span class="font-normal total_period_order_count"></span></li>
      <li class="font-bold">Всего товаров: <span class="font-normal total_period_item_count"></span></li>
      <li class="font-bold">Общая сумма: <span class="font-normal total_period_sum"></span></li>
    </ul>
  </div>
  <div class="w-full mx-auto">
    <!-- Таблица под графиком -->
    <table id="statsTable" class="min-w-full w-full text-sm text-left mt-6 border">
      <thead class="bg-gray-100">
      <tr>
        <th class="bg-gray-100 border p-2">Период</th>
        <th class="bg-gray-100 border p-2">Заказы</th>
        <th class="bg-gray-100 border p-2">Заказы (всего)</th>
        <th class="bg-gray-100 border p-2">Товары</th>
        <th class="bg-gray-100 border p-2">Товары (всего)</th>
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

    const fmtNum = n => n
      .toString()
      .replace(/\B(?=(\d{3})+(?!\d))/g, ' ');

    (async () => {
      const res  = await fetch('/admin/reports/chart1' + window.location.search);
      const rows = await res.json();

      /* --- 2. Формируем массивы для чарта и таблицы --- */
      // const isDay  = (new URLSearchParams(location.search).get('unit') ?? 'hour') === 'day';
      const labels = rows.map(r => r.bucket_label);
      const sums   = rows.map(r => Number(r.total_sum));
      const orders = rows.map(r => Number(r.order_count));
      const items  = rows.map(r => Number(r.item_count));
      const sumsDiff   = rows.map(r => Number(r.total_sum_diff));
      const ordersDiff = rows.map(r => Number(r.order_count_diff));
      const itemsDiff  = rows.map(r => Number(r.item_count_diff));

      /* --- 3. Диаграмма (столбцы ― сумма, линии ― количество заказов) --- */
      new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: {
          labels,
          datasets: [
            {     // сумма
              label: 'Сумма, ₽',
              data: sums,
              yAxisID: 'money',
              backgroundColor: 'rgba(54,162,235,0.6)',
              borderWidth: 1
            },
            {     // количество заказов
              type: 'line',
              label: 'Заказы',
              data: orders,
              yAxisID: 'count',
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
            money: { beginAtZero: true, ticks: { callback: v => v.toLocaleString() } },
            count: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false } }
          },
          plugins: {
            tooltip: { callbacks: {
                label: ctx =>
                  ctx.dataset.label === 'Сумма, ₽'
                    ? fmtNum(ctx.parsed.y) + ' ₽'
                    : fmtNum(ctx.parsed.y) + ' зак.'
              }}
          }
        }
      });

      /* --- 4. Заполняем таблицу под графиком --- */
      const tbody = document.querySelector('#statsTable tbody');
      rows.forEach((r, i) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
      <td class="border p-2">${labels[i]}</td>
      <td class="border p-2">${ordersDiff[i] > 0 ? '+'+fmtNum(ordersDiff[i]) : '0' }</td>
      <td class="border p-2">${fmtNum(orders[i])}</td>
      <td class="border p-2">${itemsDiff[i] > 0 ? '+'+fmtNum(itemsDiff[i]) : '0' }</td>
      <td class="border p-2">${fmtNum(items[i])}</td>
      <td class="border p-2">${sumsDiff[i] > 0 ? '+'+fmtNum(sumsDiff[i]) : '' }</td>
      <td class="border p-2">${fmtNum(sums[i])}</td>`;
        tbody.appendChild(tr);
        document.querySelector('.total_period_order_count').textContent = fmtNum(r.total_period_order_count);
        document.querySelector('.total_period_item_count').textContent = fmtNum(r.total_period_item_count);
        document.querySelector('.total_period_sum').textContent = `${fmtNum(r.total_period_sum)} ₽`;
      });
    })();
  </script>
</x-admin-layout>
