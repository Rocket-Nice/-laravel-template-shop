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
        <p>На данный момент выполняется задача по выгрузке данных</p>
      </div>
    @endif

    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md">
        <thead>
        <tr>
          <th class="bg-gray-100 border p-2">#</th>
          <th class="bg-gray-100 border p-2 text-left">Имя</th>
          <th class="bg-gray-100 border p-2">Размер</th>
          <th class="bg-gray-100 border p-2">Строк в файле</th>
          <th class="bg-gray-100 border p-2">Дата создания</th>
          <th class="bg-gray-100 border p-2">Пользователь</th>
          <th class="bg-gray-100 border p-2">Файл</th>
        </tr>
        </thead>
        <tbody>
        @foreach($export_files as $file)
          <tr>
            <td class="border p-2">{{ $file->id }}</td>
            <td class="border p-2 text-left">{{ $file->name }}</td>
            <td class="border p-2">{{ $file->size ? get_size($file->size) : '' }}</td>
            <td class="border p-2">{{ $file->lines_count }}</td>
            <td class="border p-2">{{ $file->created_at->format('d.m.Y H:i') }}</td>
            <td class="border p-2">{{ $file->creator->email }}</td>
            <td class="border p-2"><a href="{{ Storage::url($file->path)}}" download target="_blank">Скачать</a></td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
</x-admin-layout>
