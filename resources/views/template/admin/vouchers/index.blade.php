<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
      <div class="flex flex-col md:flex-row -m-1 space-x-0 md:space-x-2 spacey-y-2 md:space-y-0">
        @if(auth()->user()->hasPermissionTo('Пакетное добавление сертификатов'))
          <div>
            <a href="{{ route('admin.vouchers.batch_create') }}" class="m-1 button button-warning">
              Пакетное добавление
            </a>
          </div>
        @endif
        @if(auth()->user()->hasPermissionTo('Создание подарочных сертификатов'))
          <div>
            <a href="{{ route('admin.vouchers.create') }}" class="m-1 button button-success">Добавить подарочный сертификат</a>
          </div>
        @endif
      </div>
    @endif
  </x-slot>
  <x-admin.search-form :route="route('admin.vouchers.index')">
    <div class="p-1 w-full">
      <div class="form-group">
        <x-input-label for="voucher" :value="__('Подарочный сертификат')"/>
        <x-text-input type="text" name="voucher" id="voucher" value="{{ request()->get('voucher') }}"
                      class="mt-1 block w-full"/>
      </div>
    </div>
  </x-admin.search-form>
  <div class="mx-auto py-4">
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
        <thead>
        <tr>
          <th class="bg-gray-100 border p-2">#</th>
          <th class="bg-gray-100 border p-2">Код</th>
          <th class="bg-gray-100 border p-2">Баланс</th>
          <th class="bg-gray-100 border p-2">Срок действия</th>
          <th class="bg-gray-100 border p-2">Заказ</th>
          <th class="bg-gray-100 border p-2">Дата использования</th>
          <th class="bg-gray-100 border p-2" style="width:120px"></th>
        </tr>
        </thead>
        <tbody>
        @foreach($vouchers as $voucher)
          <tr>
            <td class="border p-2">{{ $voucher->id }}</td>
            <td class="border p-2 text-left">{{ $voucher->code }}</td>
            <td class="border p-2">{{ formatPrice($voucher->amount) }}</td>
            <td class="border p-2">
              @if($voucher->available_until)
                @if($voucher->available_until->lt(now()))
                  <span class="text-red-600">{{ $voucher->available_until->format('d.m.Y') }}</span>
                @else
                  <span class="text-green-600">{{ $voucher->available_until->format('d.m.Y') }}</span>
                @endif
              @endif
            </td>

            <td class="border p-2">
              @if($voucher->order_id)
                <a href="{{ route('admin.orders.show', $voucher->order->slug) }}">#{{ $voucher->order_id }}</a>
              @endif
            </td>
            <td class="border p-2">
              @if($voucher->used_at)
                {{ $voucher->used_at->format('d.m.Y H:i') }}
              @endif
            </td>
            <td class="border p-2 text-right">
              @if(auth()->user()->hasPermissionTo('Обнуление подарочных сертификатов'))
                <div id="reset-voucher-{{ $voucher->id }}" class="hidden w-full max-w-2xl">
                  <form action="{{ route('admin.vouchers.reset', ['voucher' => $voucher->id]) }}" class="p-4 reset_voucher" method="POST">
                    @csrf
                    <div class="font-bold mb-4">Обнолить подарочный сертификат «{{ $voucher->code }}»</div>
                    <div class="form-group">
                      <x-input-label for="comment" :value="__('Комментарий')" />
                      <x-textarea name="comment" id="comment" class="mt-1 w-full"></x-textarea>
                    </div>
                    <x-primary-button>Обнулить</x-primary-button>
                  </form>
                </div>
              @endif
              @if(auth()->user()->hasPermissionTo('edit_vouchers')||auth()->user()->hasPermissionTo('Обнуление подарочных сертификатов'))
              <x-dropdown_menu>
                <x-slot name="content">
                  <div class="py-1" role="none">
                    @if(auth()->user()->hasPermissionTo('Редактирование подарочных сертификатов'))
                      <a href="{{ route('admin.vouchers.edit', $voucher->id) }}" class="hover:bg-gray-100 no-underline text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1">Редактировать</a>
                    @endif
                    @if(auth()->user()->hasPermissionTo('Обнуление подарочных сертификатов'))
                      <a href="javascript:;" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1" data-fancybox="reset-voucher-{{ $voucher->id }}" data-src="#reset-voucher-{{ $voucher->id }}">Обнулить</a>
                    @endif
                  </div>
                </x-slot>
              </x-dropdown_menu>
                @endif
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    <div class="p-2">
      {{ $vouchers->appends(request()->input())->links('pagination::tailwind') }}
    </div>
  </div>
</x-admin-layout>
