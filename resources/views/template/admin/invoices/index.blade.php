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
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
        <thead>
        <tr>
          <th class="bg-gray-100 border p-2" style="width: 5%">ID</th>
          <th class="bg-gray-100 border p-2">Наименование</th>
          <th class="bg-gray-100 border p-2">Количество заказов</th>
          <th class="bg-gray-100 border p-2">Дата создания</th>
          <th class="bg-gray-100 border p-2">Открыть заказы</th>
          <th class="bg-gray-100 border p-2">Открыть накладную</th>
{{--          <th class="bg-gray-100 border p-2">Косметички</th>--}}
        </tr>
        </thead>
        <tbody>
        @foreach($invoices as $invoice)
          <tr>
            <td class="border p-2">{{ 'invoice_'.$invoice->id }}</td>
            <td class="border p-2">
              {{ $invoice->name }}
            </td>
            <td class="border p-2">
              {{ $invoice->orders()->count() }}
            </td>
            <td class="border p-2">{{ date('d.m.Y H:i:s', strtotime($invoice->created_at)) }}</td>
            <td class="border p-2 text-center">
              <a href="{!! route('admin.orders.index', ['invoice_id' => $invoice->id]) !!}">Заказы</a>
            </td>
            <td class="border p-2 text-center">
              <a href="{!! route('admin.invoices.show', $invoice->id) !!}" class="button button-primary">Накладная</a>
            </td>
{{--            <td class="border p-2 text-center">--}}
{{--              @if(isset($invoice->options['builder']))--}}
{{--                <a href="{!! $invoice->options['builder'] !!}" class="button button-secondary" target="_blank">Косметички</a>--}}
{{--              @endif--}}
{{--            </td>--}}
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    <div class="p-2">
      {{ $invoices->appends(request()->input())->links('pagination::tailwind') }}
    </div>
  </div>
</x-admin-layout>


