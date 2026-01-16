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
  <x-admin.search-form :route="route('admin.happy_coupones.stores')">
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="date_from" :value="__('Дата использования от')"/>
        <x-text-input type="text" name="date_from" id="date_from" value="{{ request()->get('date_from') }}" data-minDate="false" placeholder="{{ now()->startOfMonth()->format('d.m.Y H:i') }}" class="mt-1 block w-full datepicker"/>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="date_until" :value="__('Дата использования до')"/>
        <x-text-input type="text" name="date_until" id="date_until" value="{{ request()->get('date_until') }}" placeholder="{{ now()->format('d.m.Y H:i') }}" data-minDate="false" class="mt-1 block w-full datepicker"/>
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
            <x-input-label for="filter-pickups" :value="__('Магазин')"/>
            <select id="filter-pickups" name="pickups[]" multiple class="multipleSelect form-control">
              @foreach($pickups as $pickup)
                <option value="{{ $pickup->id }}" @if(is_array(request()->get('pickups'))&&in_array($pickup->id, request()->get('pickups'))){!! 'selected' !!}@endif>{{ $pickup->name }} (id {{ $pickup->id }})</option>
              @endforeach
            </select>
          </div>
        </div>
    <div class="p-1 w-full">
      <div class="form-group">
        <div class="flex items-center">
          <x-checkbox id="shorPrizes" name="shorPrizes" value="1"
                      :checked="request()->shorPrizes ? true : false"/>
          <x-input-label for="shorPrizes" class="ml-2" :value="__('Показать подарки')"/>
        </div>
      </div>
    </div>
  </x-admin.search-form>
  <div class="mx-auto py-4">
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
        <thead>
        <tr>
          <th class="bg-gray-100 border p-2">#</th>
          <th class="bg-gray-100 border p-2">Код купона</th>
          <th class="bg-gray-100 border p-2">Магазин</th>
          <th class="bg-gray-100 border p-2">Пользователь</th>
          <th class="bg-gray-100 border p-2">Заказ</th>
          @if(request()->shorPrizes)
            <th class="bg-gray-100 border p-2">Подарок 1</th>
            <th class="bg-gray-100 border p-2">Подарок 2</th>
            <th class="bg-gray-100 border p-2">Подарок 3</th>
          @endif
        </tr>
        </thead>
        <tbody>
        @foreach($storeCoupons as $storeCoupon)
          @php($storeCoupon->user)
          <tr>
            <td class="border p-2">{{ $storeCoupon->id }}</td>
            <td class="border p-2">{{ $storeCoupon->code }}</td>
            <td class="border p-2">{{ $storeCoupon->pickup->params['short_name'] ?? $storeCoupon->pickup->name ?? '' }}</td>
            <td class="border p-2">@if($storeCoupon->user_email)<a href="{{ route('admin.users.edit', $storeCoupon->user_id) }}">{{ $storeCoupon->user_email }}</a>@endif</td>
            <td class="border p-2">@if($storeCoupon->order_id)<a href="{{ route('admin.orders.show', $storeCoupon->order_slug) }}">{{ $storeCoupon->order_id }}</a>@endif</td>
            @if(request()->shorPrizes)
              @php($prizes = $storeCoupon->giftCoupons()->where('prize_id', '!=', 146)->get())
              @foreach($prizes as $prize)
                <td class="border p-2">{{ $prize->prize->name ?? '' }}</td>
              @endforeach
              @php($i = 3 - $prizes->count())

              @while($i > 0)
                <td class="border p-2"></td>
                @php($i--)
              @endwhile
            @endif
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    <div class="p-2">
      {{ $storeCoupons->appends(request()->input())->links('pagination::tailwind') }}
    </div>
  </div>
</x-admin-layout>


