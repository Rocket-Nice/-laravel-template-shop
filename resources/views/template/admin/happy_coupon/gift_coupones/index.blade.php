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
  <x-admin.search-form :route="route('admin.happy_coupones.index')">
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="date_from" :value="__('Дата от')"/>
        <x-text-input type="text" name="date_from" id="date_from" value="{{ request()->get('date_from') }}" data-minDate="false" placeholder="{{ now()->startOfMonth()->format('d.m.Y H:i') }}" class="mt-1 block w-full datepicker"/>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="date_until" :value="__('Дата до')"/>
        <x-text-input type="text" name="date_until" id="date_until" value="{{ request()->get('date_until') }}" placeholder="{{ now()->format('d.m.Y H:i') }}" data-minDate="false" class="mt-1 block w-full datepicker"/>
      </div>
    </div>
    <div class="p-1 w-full">
      <div class="form-group">
        <x-input-label for="order_id" :value="__('ID заказа')" />
        <x-text-input type="text" name="order_id" id="order_id" value="{{ request()->get('order_id') }}" class="mt-1 block w-full" />
      </div>
    </div>
    <div class="p-1 w-full">
      <div class="form-group">
        <x-input-label for="code" :value="__('Код купона')" />
        <x-text-input type="text" name="code" id="code" value="{{ request()->get('code') }}" class="mt-1 block w-full" />
      </div>
    </div>
    <div class="p-1 w-full">
      <div class="form-group">
        <x-input-label for="keyword" :value="__('Имя, email или телефон')" />
        <x-text-input type="text" name="keyword" id="keyword" value="{{ request()->get('keyword') }}" class="mt-1 block w-full" />
      </div>
    </div>
    <div class="p-1 w-full">
      <div class="form-group">
        <x-input-label for="prize" :value="__('Подарок')" />
        <select id="prize" name="prize" class="form-control w-full">
          <option value="">Выбрать</option>
          @foreach($prizes as $prize)
            <option value="{{ $prize->id }}"  @if(request()->prize==$prize->id){{ 'selected' }}@endif>{{ $prize->name }}</option>
          @endforeach
        </select>
      </div>
    </div>
{{--    <div class="p-1 w-full">--}}
{{--      <div class="form-group">--}}
{{--        <x-input-label for="filter-partners" :value="__('Партнер')"/>--}}
{{--        <select id="filter-partners" name="partners[]" multiple class="multipleSelect form-control">--}}
{{--          @foreach($partners as $partner)--}}
{{--            <option value="{{ $partner->id }}" @if(is_array(request()->get('partners'))&&in_array($partner->id, request()->get('partners'))){!! 'selected' !!}@endif>{{ $partner->name }} (id {{ $partner->id }})</option>--}}
{{--          @endforeach--}}
{{--        </select>--}}
{{--      </div>--}}
{{--    </div>--}}
  </x-admin.search-form>
  <div class="mx-auto py-4">
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
        <thead>
        <tr>
          <th class="bg-gray-100 border p-2">Заказ</th>
          <th class="bg-gray-100 border p-2">Дата заказа</th>
          <th class="bg-gray-100 border p-2">Имя</th>
          <th class="bg-gray-100 border p-2">Email</th>
          <th class="bg-gray-100 border p-2">Телефон</th>
          <th class="bg-gray-100 border p-2">Код</th>
          <th class="bg-gray-100 border p-2">Подарок</th>
          <th class="bg-gray-100 border p-2" style="width:60px"></th>
        </tr>
        </thead>
        <tbody>
        @foreach($giftCoupones as $giftCoupon)
          <tr>
            <td class="border p-2">{{ $giftCoupon->order_id }}</td>
            <td class="border p-2 text-left" style="max-width:220px">
              {{ \Carbon\Carbon::parse($giftCoupon->created_at)->format('d.m.Y H:i') }}<br/>{!! getStatusBadge($giftCoupon->status) !!}
            </td>
            <td class="border p-2">{{ $giftCoupon->name }}</td>
            <td class="border p-2">{{ $giftCoupon->email }}</td>
            <td class="border p-2">{{ $giftCoupon->phone }}</td>
            <td class="border p-2">{{ $giftCoupon->code }}</td>
            <td class="border p-2">{{ $giftCoupon->prize }}</td>
            <td class="border p-2 text-right">
              <x-dropdown_menu>
                <x-slot name="content">
                  <div class="py-1" role="none">
                    <a href="{{ route('admin.orders.show', $giftCoupon->slug) }}" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1">Открыть заказ</a>
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
      {{ $giftCoupones->appends(request()->input())->links('pagination::tailwind') }}
    </div>
  </div>
</x-admin-layout>


