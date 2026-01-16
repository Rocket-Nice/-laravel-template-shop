<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>

      <a href="{{ route('admin.vouchers.index') }}" class="m-1 button button-secondary">Назад</a>
    @endif
  </x-slot>
  <div class="mx-auto py-4">
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
        <thead>
        <tr>
          <th class="bg-gray-100 border p-2">Код</th>
          <th class="bg-gray-100 border p-2">Сумма</th>
          <th class="bg-gray-100 border p-2">Ссылка на сертификат</th>
        </tr>
        </thead>
        <tbody>
        @foreach($vouchers_data as $voucher)
          <tr>
            <td class="border p-2">{{ $voucher[1] }}</td>
            <td class="border p-2 text-left">{{ formatPrice($voucher[0]) }}</td>
            <td class="border p-2">{{ $voucher[2] }}</td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>
</x-admin-layout>
