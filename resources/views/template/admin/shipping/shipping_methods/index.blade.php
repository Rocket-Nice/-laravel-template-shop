@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
      <div class="flex justify-end">
        <a href="javascript:;" data-fancybox data-src="#import-new-territories" class="button button-warning">Импорт новых территорий</a>
        <a href="{{ route('admin.shipping_methods.create') }}" class="button button-success">Добавить способ доставки</a>
      </div>

    @endif
  </x-slot>
  <div style="display: none;" id="import-new-territories" class="w-full  !max-w-2xl">
    <form action="{{ route('admin.cdek.import-new-territories') }}" method="POST" class="p-2 sm:p-4 md:p-6 lg:p-9" enctype="multipart/form-data">
      @csrf
      <div>
        <label for="file" class="block text-sm font-medium text-gray-700 mb-1">
          Загрузить файл
        </label>

        <input
          type="file"
          name="file"
          id="file"
          class="block w-full text-sm text-gray-900
                   file:mr-4 file:py-2 file:px-4
                   file:rounded-md file:border-0
                   file:text-sm file:font-semibold
                   file:bg-blue-50 file:text-blue-700
                   hover:file:bg-blue-100
                   border border-gray-300 rounded-md
                   focus:outline-none focus:ring-2 focus:ring-blue-500"
        >

        @error('file')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>
      <div class="px-3 sm:px-4 lg:px-6 py-6 flex justify-end">
        <x-primary-button>Импортировать</x-primary-button>
      </div>
    </form>
  </div>
  <div class="mx-auto py-4">
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
        <thead>
        <tr>
          <th class="bg-gray-100 border p-2" style="width: 5%">#</th>
          <th class="bg-gray-100 border p-2">Наименование</th>
          <th class="bg-gray-100 border p-2">Добавленная стоимость</th>
          <th class="bg-gray-100 border p-2">Статус</th>
          <th class="bg-gray-100 border p-2" style="width:120px"></th>
        </tr>
        </thead>
        <tbody>
        @foreach($shipping_methods as $shippingMethod)
          <tr>
            <td class="border p-2">{{ $shippingMethod->code }}</td>
            <td class="border p-2">{{ $shippingMethod->name }}</td>
            <td class="border p-2">{{ $shippingMethod->add_price }}</td>
            <td class="border p-2">{!! $shippingMethod->active ? '<span class="badge badge-green">Активен</span>' : '<span class="badge badge-red">Выключен</span>' !!}</td>
            <td class="border p-2">

              <x-dropdown_menu>
                <x-slot name="content">
                  <div class="py-1" role="none">
                    <a href="{{ route('admin.shipping_methods.edit', $shippingMethod->id) }}" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1">Редактировать</a>
                  </div>
                </x-slot>
              </x-dropdown_menu>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    <div class="p-2">
      {{ $shipping_methods->appends(request()->input())->links('pagination::tailwind') }}
    </div>
  </div>
</x-admin-layout>

