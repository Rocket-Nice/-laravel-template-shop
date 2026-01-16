@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
      @if(auth()->user()->hasPermissionTo('Создание промокодов'))
        <a href="{{ route('admin.coupones.create') }}" class="button button-success">Создать промокоды</a>
      @endif
    @endif
  </x-slot>
  <x-admin.search-form :route="route('admin.coupones.index')">
    <div class="p-1 w-full">
      <div class="form-group">
        <x-input-label for="coupone" :value="__('Промокод')"/>
        <x-text-input type="text" name="coupone" id="coupone" value="{{ request()->get('coupone') }}"
                      class="mt-1 block w-full"/>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="filter-type" :value="__('Тип промокода')"/>
        <select id="filter-type" name="type" class="form-control w-full">
          <option value="" @if(request()->type===null)
            {!! 'selected' !!}
            @endif>Все
          </option>
          <option value="10" @if(request()->get('type')==='10')
            {!! 'selected' !!}
            @endif>Скидка в рублях на корзину
          </option>
          <option value="1" @if(request()->get('type')==='1')
            {!! 'selected' !!}
            @endif>Скдика в процентах на 1 товар
          </option>
          <option value="2" @if(request()->get('type')==='2')
            {!! 'selected' !!}
            @endif>Скдика в процентах на корзину
          </option>
          <option value="4" @if(request()->get('type')==='4')
            {!! 'selected' !!}
            @endif>Скдика в процентах на выбранные товары
          </option>
        </select>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="filter-is_active" :value="__('Активность')"/>
        <select id="filter-is_active" name="is_active" class="form-control w-full">
          <option value="" @if(request()->is_active===null)
            {!! 'selected' !!}
            @endif>Все
          </option>
          <option value="1" @if(request()->get('is_active')==='1')
            {!! 'selected' !!}
            @endif>Активен
          </option>
          <option value="0" @if(request()->get('is_active')==='0')
            {!! 'selected' !!}
            @endif>Неактивен
          </option>
        </select>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="available_from_1" :value="__('Срок начала действия от')"/>
        <x-text-input type="text" name="available_from_1" id="available_from_1" value="{{ request()->get('available_from_1') }}"
                      data-minDate="false" placeholder="{{ now()->startOfMonth()->format('d.m.Y H:i') }}"
                      class="mt-1 block w-full datepicker"/>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="available_from_2" :value="__('Срок начала действия до')"/>
        <x-text-input type="text" name="available_from_2" id="available_from_2" value="{{ request()->get('available_from_2') }}"
                      placeholder="{{ now()->format('d.m.Y H:i') }}" data-minDate="false"
                      class="mt-1 block w-full datepicker"/>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="available_until_1" :value="__('Срок окончания действия от')"/>
        <x-text-input type="text" name="available_until_1" id="available_until_1" value="{{ request()->get('available_until_1') }}"
                      data-minDate="false" placeholder="{{ now()->startOfMonth()->format('d.m.Y H:i') }}"
                      class="mt-1 block w-full datepicker"/>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="available_until_2" :value="__('Срок окончания действия до')"/>
        <x-text-input type="text" name="available_until_2" id="available_until_2" value="{{ request()->get('available_until_2') }}"
                      placeholder="{{ now()->format('d.m.Y H:i') }}" data-minDate="false"
                      class="mt-1 block w-full datepicker"/>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="used_at_1" :value="__('Дата использования от')"/>
        <x-text-input type="text" name="used_at_1" id="used_at_1" value="{{ request()->get('used_at_1') }}"
                      data-minDate="false" placeholder="{{ now()->startOfMonth()->format('d.m.Y H:i') }}"
                      class="mt-1 block w-full datepicker"/>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="used_at_2" :value="__('Дата использования до')"/>
        <x-text-input type="text" name="used_at_2" id="used_at_2" value="{{ request()->get('used_at_2') }}"
                      placeholder="{{ now()->format('d.m.Y H:i') }}" data-minDate="false"
                      class="mt-1 block w-full datepicker"/>
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
          <th class="bg-gray-100 border p-2">Тип промокода</th>
          <th class="bg-gray-100 border p-2">Скидка</th>
          <th class="bg-gray-100 border p-2">Количество</th>
          <th class="bg-gray-100 border p-2">Срок действия</th>
          <th class="bg-gray-100 border p-2">Заказ</th>
          <th class="bg-gray-100 border p-2">Дата использования</th>
          <th class="bg-gray-100 border p-2" style="width:120px"></th>
        </tr>
        </thead>
        <tbody>
        @foreach($coupones as $coupone)
          <tr>
            <td class="border p-2">{{ $coupone->id }}</td>
            <td class="border p-2 text-left">{{ $coupone->code }}</td>
            <td class="border p-2">
              @if(in_array($coupone->type, [1]))
                {{ 'Скидка на 1 товар' }}
              @elseif($coupone->type==4)
                {{ 'Скидка на некоторые товары' }}
              @else
                {{ 'Скидка на корзину' }}
              @endif
            </td>
            <td class="border p-2">
              @if(in_array($coupone->type, [1, 2, 4]))
                {{ $coupone->amount }}%
              @else
                {{ formatPrice($coupone->amount) }}
              @endif
            </td>
            <td class="border p-2">{!! $coupone->count > 0 ? $coupone->count : '<span style="opacity: .5">'.$coupone->count.'</span>' !!}</td>
            <td class="border p-2">
              @if($coupone->available_until)
                @if($coupone->available_until->lt(now()))
                  <span class="text-red-600">{{ $coupone->available_until->format('d.m.Y') }}</span>
                @else
                  <span class="text-green-600">{{ $coupone->available_until->format('d.m.Y') }}</span>
                @endif
              @endif
            </td>
            <td class="border p-2">
              @if($coupone->order_id)
                <a href="{{ route('admin.orders.show', $coupone->order->slug) }}">#{{ $coupone->order_id }}</a>
              @endif
            </td>
            <td class="border p-2">
              @if($coupone->used_at)
                {{ $coupone->used_at->format('d.m.Y H:i') }}
              @endif
            </td>
            <td class="border p-2">
              @if(auth()->user()->hasPermissionTo('Обнуление промокодов'))
                <div id="reset-coupone-{{ $coupone->id }}" class="hidden w-full max-w-2xl">
                  <form action="{{ route('admin.coupones.reset', ['coupone' => $coupone->id]) }}" class="p-4 reset_coupone" method="POST">
                    @csrf
                    <div class="font-bold mb-4">Обнолить промокод «{{ $coupone->code }}»</div>
                    <div class="form-group">
                      <x-input-label for="comment" :value="__('Комментарий')" />
                      <x-textarea name="comment" id="comment" class="mt-1 w-full"></x-textarea>
                    </div>
                    <x-primary-button>Обнулить</x-primary-button>
                  </form>
                </div>
              @endif
              @if(auth()->user()->hasPermissionTo('Редактирование промокодов')||auth()->user()->hasPermissionTo('Обнуление промокодов'))
              <x-dropdown_menu>
                <x-slot name="content">
                  <div class="py-1" role="none">
                      @if(auth()->user()->hasPermissionTo('Редактирование промокодов'))
                        <a href="{{ route('admin.coupones.edit', $coupone->id) }}" class="hover:bg-gray-100 no-underline text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1">Редактировать</a>
                      @endif
                      @if(auth()->user()->hasPermissionTo('Обнуление промокодов'))
                        <a href="javascript:;" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1" data-fancybox="reset-coupone-{{ $coupone->id }}" data-src="#reset-coupone-{{ $coupone->id }}">Обнулить</a>
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
      {{ $coupones->appends(request()->input())->links('pagination::tailwind') }}
    </div>
  </div>
</x-admin-layout>
