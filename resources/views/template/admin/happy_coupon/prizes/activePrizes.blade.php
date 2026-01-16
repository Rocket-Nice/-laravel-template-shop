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
    <div>Всего
      заказов {{ \App\Models\Order::where('confirm', 1)->where('data->store_coupon', null)->whereNotIn('status', ['test', 'refund', 'cancelled'])->where('created_at', '>=', (new \App\Models\GiftCoupon)->getDate())->count() }}</div>
    <form action="{{ route('admin.happy_coupones.activePrizesUpdate') }}" method="post">
      @csrf

      <div class="relative overflow-x-auto min-h-[500px]">
        <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
          <thead>
          <tr>
            <th class="bg-gray-100 border p-2" style="width:8%">#</th>
            <th class="bg-gray-100 border p-2" style="width:31%">Наименоание</th>
            <th class="bg-gray-100 border p-2" style="width:31%">Товар</th>
            <th class="bg-gray-100 border p-2" style="width:10%">Розыграно</th>
            <th class="bg-gray-100 border p-2" style="width:10%">Разыгрываются</th>
            <th class="bg-gray-100 border p-2" style="width:10%">Всего</th>
          </tr>
          </thead>
          <tbody>
          {{--          @php($max = [--}}
          {{--              140 => 100,--}}
          {{--              144 => 200,--}}
          {{--              131 => 350,--}}
          {{--              141 => 500,--}}
          {{--              143 => 500,--}}
          {{--              145 => 500,--}}
          {{--              142 => 1000,--}}
          {{--          ])--}}
          @foreach($prizes as $prize)
            @if(isset($max[$prize->id]))
              @php($prize->total = $max[$prize->id])
            @endif
            <tr>
              <td class="border p-2">{{ $prize->id }}</td>
              <td class="border p-2 text-left" style="max-width:220px">
                <div class="flex items-center space-x-4">
                  <div>{{ $prize->name }}</div>

                  @if(in_array($prize->id, \App\Models\Prize::GENERAL))
                    <span class="badge-yellow text-xs whitespace-nowrap">От 3599</span>
                  @elseif(in_array($prize->id, \App\Models\Prize::GENERAL2))
                    <span class="badge-green text-xs whitespace-nowrap">От 5999</span>
                  @endif

                  @if(in_array($prize->id, \App\Models\Prize::RED))
                    <span class="badge-red text-xs whitespace-nowrap">Раздать раньше</span>
                  @endif
                </div>
              </td>
              <td class="border p-2">
                @if($prize->product_slug)
                  <a href="{{ route('admin.products.edit', $prize->product_slug) }}">{{ $prize->product_name }}</a>
                @endif
              </td>
              <td class="border p-2">{{ $prize->gift_coupons_count }}</td>
              <td class="border p-2" data-target="count[{{ $prize->id }}]">
                <div class="form-group flex items-center">
                  <input type="text"
                         class="edit_quantity numeric-field bg-transparent p-0 border-0 w-full text-center text-sm"
                         data-field-name="count[{{ $prize->id }}]" placeholder="число" value="{{ $prize->count }}"
                         @if(!$prize->active)
                           {!! 'disabled' !!}
                         @endif autocomplete="off" style="max-width: 120px;">
                </div>
              </td>
              {{--              <td class="border p-2">{{ $prize->total }} ({{ round(100 - ($prize->count/$prize->total*100), 2) }}%)</td>--}}
              <td class="border p-2">{{ $prize->total }}</td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
      <div class="text-right p-3" id="btn-update" style="display: none;">
        <x-primary-button type="submit">Обновить значения</x-primary-button>
      </div>
    </form>

  </div>
</x-admin-layout>


