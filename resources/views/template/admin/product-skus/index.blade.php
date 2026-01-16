@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="title">
    {{ $seo['title'] ?? false }}
  </x-slot>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <div class="flex justify-end space-x-2">
    <a href="javascript:;" data-fancybox data-src="#add-item" class="button button-success">Добавить артикул</a>
  </div>
  <div class="mx-auto mt-4">
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
        <thead>
        <tr>
          <th class="bg-gray-200 border p-2 text-left">Наименование</th>
          <th class="bg-gray-200 border p-2 text-left">Количество товаров</th>
          <th class="bg-gray-200 border p-2" style="width: 5%"></th>
        </tr>
        </thead>
        <tbody>
        @forelse($items as $k => $item)
          <tr class="@if(!($k & 1)) bg-gray-200 @endif">
            <td class="border p-2 text-left">
              {{ $item->name }}
            </td>
            <td class="border p-2 text-left">
              {{ $item->products_count }}
            </td>
            {{--            <td class="border p-2 text-left">--}}
            {{--              @if($item->shipping_method == 1)--}}
            {{--                <span class="badge-yellow text-xs">АВТО</span>--}}
            {{--              @elseif($item->shipping_method == '2')--}}
            {{--                <span class="badge-blue text-xs">АВИА</span>--}}
            {{--              @endif--}}
            {{--            </td>--}}
            <td class="border p-2">
              <form action="{{ route('admin.product-skus.destroy', $item->id) }}" id="delete-item-{{ $item->id }}" method="POST">
                @csrf
                @method('DELETE')
              </form>
              <div class="hidden">
                <div style="display: none;" id="update-item-{{ $item->id }}" class="w-full !max-w-2xl">
                  <form action="{{ route('admin.product-skus.update', $item->id) }}" method="POST" class="p-2 sm:p-4 md:p-6 lg:p-9">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                      <x-input-label for="name-{{ $item->id }}" :value="__('Наименование')" />
                      <x-text-input type="text" name="name" id="name-{{ $item->id }}" value="{{ $item->name }}" class="mt-1 w-full" required />
                    </div>
                    <div class="px-3 sm:px-4 lg:px-6 py-6 flex justify-end">
                      <x-primary-button>Сохранить</x-primary-button>
                    </div>
                  </form>
                </div>
              </div>
              <x-ui.dropdown :trigger="'...'">
                <x-ui.dropdown-link href="javascript:;" data-fancybox data-src="#update-item-{{ $item->id }}">Редактировать</x-ui.dropdown-link>
                <x-ui.dropdown-link href="#" onclick="if(confirm('Удалить запись «{{ $item->name }}»?'))document.getElementById('delete-item-{{ $item->id }}').submit();">Удалить</x-ui.dropdown-link>
              </x-ui.dropdown>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="11">
              <div class="text-gray-400 text-2xl p-5 text-center">Нет записей</div>
            </td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>
    <div class="p-2">
      {{ $items->appends(request()->input())->links('pagination::tailwind') }}
    </div>
  </div>
  <div style="display: none;" id="add-item" class="w-full !max-w-2xl">
    <form action="{{ route('admin.product-skus.store') }}" method="POST" class="p-2 sm:p-4 md:p-6 lg:p-9">
      @csrf
      <div class="form-group">
        <x-input-label for="name" :value="__('Наименование')" />
        <x-text-input type="text" name="name" id="name" value="{{ old('name') }}" class="mt-1 w-full" required />
      </div>
      <div class="px-3 sm:px-4 lg:px-6 py-6 flex justify-end">
        <x-primary-button>Сохранить</x-primary-button>
      </div>
    </form>
  </div>
</x-admin-layout>


