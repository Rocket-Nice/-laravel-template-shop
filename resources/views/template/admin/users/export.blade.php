@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>

  <div class="mx-auto py-4">
    @if($jobsCount)
      <div class="mb-4 bg-orange-100 border-l-4 border-orange-500 text-orange-700 p-4" role="alert">
        <p class="font-bold">Экспорт в процессе</p>
        <p>На данный момент выполняется задача по выгрузке пользователей</p>
      </div>
    @endif

    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md">
        <thead>
        <tr>
          <th class="bg-gray-100 border p-2">#</th>
          <th class="bg-gray-100 border p-2 text-left">Имя</th>
          <th class="bg-gray-100 border p-2">Размер</th>
          <th class="bg-gray-100 border p-2">Дата создания</th>
          <th class="bg-gray-100 border p-2">Файл</th>
        </tr>
        </thead>
        <tbody>
        @php($i=1)
        @foreach($sortedCollection as $file)
          <tr>
            <td class="border p-2">{{ $i }}</td>
            <td class="border p-2 text-left">{{ $file['name'] }}</td>
            <td class="border p-2">{{ get_size($file['size']) }}</td>
            <td class="border p-2">{{ $file['date'] }}</td>
            <td class="border p-2"><a href="{{ $file['url'] }}" download target="_blank">Скачать</a></td>
          </tr>
          @php($i++)
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
</x-admin-layout>
