@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
      {{--      <a href="{{ route('admin.orders.create') }}" class="button button-success">Создать заказ</a>--}}
    @endif
  </x-slot>
  <x-admin.search-form :route="route('admin.products.notifications')">
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="keyword" :value="__('Имя, email или телефон')"/>
        <x-text-input type="text" name="keyword" id="keyword" value="{{ request()->get('keyword') }}" class="mt-1 block w-full"/>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="filter-product" :value="__('Товар')"/>
        <select id="filter-product" name="product[]" multiple class="multipleSelect form-control">
          @foreach($products as $product)
            <option value="{{ $product->id }}" @if(is_array(request()->get('product'))&&in_array($product->id, request()->get('product'))){!! 'selected' !!}@endif>{{ $product->name }} ({{ $product->sku }})</option>
          @endforeach
          {{--            <option value="scrub-kokos-bonus" @if(is_array(request()->get('product'))&&in_array('scrub-kokos-bonus', request()->get('product'))){!! 'selected' !!}@endif>Скраб кокос (бонус)</option>--}}
          {{--            <option value="hgift-bg-malina" @if(is_array(request()->get('product'))&&in_array('hgift-bg-malina', request()->get('product'))){!! 'selected' !!}@endif>Бальзам для губ (подарок)</option>--}}
          {{--            <option value="hgift-mm-kutikuli" @if(is_array(request()->get('product'))&&in_array('hgift-mm-kutikuli', request()->get('product'))){!! 'selected' !!}@endif>Масло для кутикулы (подарок)</option>--}}
        </select>
      </div>
    </div>
  </x-admin.search-form>
  <div class="mx-auto py-4">
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
        <thead>
        <tr>
          <th class="bg-gray-100 border p-2">Пользователь</th>
          <th class="bg-gray-100 border p-2">Продукт</th>
          <th class="bg-gray-100 border p-2">Статус уведомления</th>
          <th class="bg-gray-100 border p-2">Дата создания</th>
        </tr>
        </thead>
        <tbody>
        @foreach($notifications as $notificate)
          <tr>
            <td class="border p-1">
              <a href="{{ route('admin.users.edit', ['user' => $notificate->user_id]) }}">{{ $notificate->user->email }}</a>
            </td>
            <td class="border p-1">
              <a href="{{ route('product.index', $notificate->product->slug) }}" target="_blank">{{ $notificate->product->name }}</a>
            </td>
            <td class="border p-1">
              @if(!$notificate->was_noticed)
                <span class="badge-gray whitespace-nowrap">Не отправлено</span>
              @else
                <span class="badge-green whitespace-nowrap">Отправлено {{ $notificate->notice_date ? $notificate->notice_date->format('d.m.Y H:i:s') : '' }}</span>
              @endif
            </td>
            <td class="border p-1">
              {{ $notificate->created_at->format('d.m.Y H:i:s') }}
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    <div class="p-2">
      {{ $notifications->appends(request()->input())->links('pagination::tailwind') }}
    </div>
  </div>
</x-admin-layout>
