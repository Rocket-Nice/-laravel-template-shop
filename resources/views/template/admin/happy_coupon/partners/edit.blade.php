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
    <div class="border-b">
      <div class="mx-auto px-2 sm:px-3 lg:px-4">
        <nav class="-mb-px flex flex-col sm:flex-row sm:justify-start space-y-2 sm:space-y-0 sm:space-x-4"
             aria-label="Tabs" role="tablist">
          @if(auth()->user()->hasPermissionTo('Редактирование партнеров'))
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-1" aria-selected="true" role="tab" aria-controls="tab-1-content">Общие данные
          </button>
          @endif
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-2" aria-selected="true" role="tab" aria-controls="tab-2-content">Статистика
          </button>
        </nav>
      </div>
    </div>

    <div class="mx-auto px-2 sm:px-3 lg:px-4 py-6" id="tab-content">
      @if(auth()->user()->hasPermissionTo('Редактирование партнеров'))
      <div id="tab-1-content" role="tabpanel">
        <div class="mb-6">
          <span class="font-bold">Партнерская ссылка: </span>{{ route('partner', $partner->slug) }}<br/>
          @if(isset($partner->data['qrcode']))
          <span class="font-bold">QR код: </span><br/>

          <div class="img" style="width:100px">
            <a href="{{ $partner->data['qrcode'] }}" download><img src="{{ $partner->data['qrcode'] }}" alt="qrcode"></a>
          </div>
          @endif
        </div>
        <form action="{{ route('admin.partners.update', $partner->slug) }}" method="post" id="update">
          @csrf
          @method('PUT')
        <div class="sm:w-[50%]">
          <div class="form-group">
            <x-input-label for="name" :value="__('Наименование')" />
            <x-text-input type="text" name="name" id="name" value="{{ old('name') ?? $partner->name }}" class="mt-1 block w-full" required />
          </div>
          <div class="form-group">
            <x-input-label for="code" :value="__('Код партнера')" />
            <x-text-input type="text" name="code" id="code" value="{{ old('code') ?? $partner->slug }}" class="mt-1 block w-full" required />
          </div>
          <div class="form-group">
            <x-input-label for="redirect" :value="__('Ссылка для перенаправления')" />
            <x-text-input type="text" name="redirect" id="redirect" value="{{ old('redirect') ?? $partner->redirect }}" class="mt-1 block w-full" />
          </div>
          <div class="form-group">
            <x-input-label for="email" :value="__('Email пользователя')" />
            <x-text-input type="text" name="email" id="email" value="{{ old('email') ?? $partner->user->email }}" class="mt-1 block w-full" required />
          </div>
          <div class="form-group">
            <x-input-label for="description" :value="__('Описание')" />
            <x-textarea name="description" id="description" class="mt-1 block w-full">{{ old('description') ?? $partner->description }}</x-textarea>
          </div>
          <div class="form-group">
            <div class="flex items-center">
              <x-checkbox id="promocode" name="promocode" value="1"
                          :checked="old('promocode') ? true : false"/>
              <x-input-label for="promocode" class="ml-2" :value="__('Создать промокод')"/>
            </div>
          </div>
        </div>
        </form>
      </div>
      @endif
      <div id="tab-2-content" role="tabpanel">
        <x-admin.search-form :route="route('admin.partners.edit', $partner->slug).'#tab-2'">
          <div class="p-1 w-full">
            <div class="form-group">
              <x-input-label for="date_from" :value="__('Начальная дата статистики')"/>
              <x-text-input type="text" name="date_from" id="date_from" value="{{ request()->get('date_from') }}" data-minDate="false" placeholder="{{ now()->subMonth()->format('d.m.Y') }}" data-timepicker="0" class="mt-1 block w-full datepicker"/>
            </div>
          </div>
        </x-admin.search-form>
        <canvas id="pageViewsChart"></canvas>
      </div>
    </div>
    <div class="px-3 sm:px-4 lg:px-6 py-6 flex justify-end">
      <x-primary-button type="submit" form="update">Сохранить</x-primary-button>
    </div>
    </div>
  <script src="{{ asset('libraries/chart.js/dist/chart.umd.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
  <script>
    var pageViews = @json($viewsByDate);
    var orders = @json($ordersByDate);
    var paidOrders = @json($paidOrdersByDate);
  </script>
  <script>
    var ctx = document.getElementById('pageViewsChart').getContext('2d');
    var chart = new Chart(ctx, {
      type: 'line', // Тип графика
      plugins: [ChartDataLabels],
      data: {
        labels: Object.keys(pageViews), // Даты
        datasets: [
          {
            label: 'Переходы на сайт',
            data: Object.values(pageViews), // Количество просмотров
            color: '#000',
            backgroundColor: 'rgba(145, 145, 145, 0.3)',
            borderColor: 'rgba(145, 145, 145, 0.5)',
            borderWidth: 1
          },
          {
            label: 'Заказы всего',
            data: Object.values(orders), // Количество просмотров
            color: '#000',
            backgroundColor: 'rgba(200, 79, 0, 0.3)',
            borderColor: 'rgba(200, 79, 0, 0.5)',
            borderWidth: 1
          },
          {
            label: 'Оплаченные заказы',
            data: Object.values(paidOrders), // Количество просмотров
            color: '#000',
            backgroundColor: 'rgba(0, 123, 255, 0.3)',
            borderColor: 'rgba(0, 123, 255, 0.5)',
            borderWidth: 1
          },
        ]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true
          }
        },
        plugins: {
          legend: {
            positions: 'top',
          },
          tooltips: {
            enabled: false // Отключение подсказок
          },
          datalabels: {
            display: true,
            backgroundColor: function(context) {
              return context.dataset.borderColor;
            },
            borderRadius: 4,
            color: 'white',
            font: {
              weight: 'bold'
            },
            formatter: (value, context) => {
              if(value > 0){
                return value; // Форматируйте значение как нужно
              }else{
                return null;
              }

            }
          }
        }
      }
    });
  </script>
</x-admin-layout>
