@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>

  <div class="mx-auto mt-4">
    <div class="mb-4">Всего памяти memcached: {{ get_size($memcached_data['totalMemory']) }}</div>
    <div class="mb-4">Используется памяти memcached: {{ get_size($memcached_data['usedMemory']) }} ({{ $memcached_data['percentUsed'] }}%)</div>
    <div class="mb-4">Сессий в memcached: {{ formatPrice($memcached_data['total_sessions'], '', '') }}</div>
    <div class="mb-4">Текущих процессов в очереди: {{ formatPrice($jobs->sum('total'), '', '') }}</div>
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
        <thead>
        <tr>
          <th class="bg-gray-200 border p-2 text-left">Имя процесса</th>
          <th class="bg-gray-200 border p-2 text-left">Количество в очереди</th>
          <th class="bg-gray-200 border p-2 text-center">Статус</th>
        </tr>
        </thead>
        <tbody>
        @forelse($jobs as $k => $item)
          <tr class="@if(!($k & 1)) bg-gray-200 @endif">
            <td class="border p-2 text-left">
              {{ $item->queue }}
            </td>
            <td class="border p-2 text-left">
              {{ $item->total }}
            </td>
            <td class="border p-2">
              @if($item->active)
                <div class="badge badge-green">Запущен</div>
              @else
                <div class="badge badge-orange">Ожидает</div>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="11">
              <div class="text-gray-400 text-2xl p-5 text-center">Очередь пуста</div>
            </td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>
</x-admin-layout>
