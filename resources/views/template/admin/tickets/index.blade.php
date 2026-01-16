@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <x-admin.search-form :route="route('admin.tickets.index')">
    <div class="p-1 w-full">
      <div class="form-group">
        <x-input-label for="categories" :value="__('Способ доставки')" />
        <select class="form-control w-full" name="shipping" data-placeholder="Выбрать">
          <option disabled @if(!request()->get('shipping')){!! 'selected' !!}@endif>не выбран</option>
          <option value="cdek" @if(request()->get('shipping')=='cdek'){!! 'selected' !!}@endif>СДЭК</option>
          <option value="cdek_courier" @if(request()->get('shipping')=='cdek_courier'){!! 'selected' !!}@endif>СДЭК курьер</option>
          <option value="pochta" @if(request()->get('shipping')=='pochta'){!! 'selected' !!}@endif>Почта</option>
          <option value="boxberry" @if(request()->get('shipping')=='boxberry'){!! 'selected' !!}@endif>Boxberry</option>
          <option value="yandex" @if(request()->get('shipping')=='yandex'){!! 'selected' !!}@endif>Яндекс Доставка</option>
          <option value="x5post" @if(request()->get('shipping')=='x5post'){!! 'selected' !!}@endif>5 Пост</option>
        </select>
      </div>
    </div>
    <div class="p-1 w-full">
      <div class="form-group">
        <div class="flex items-center">
          <x-checkbox id="all_managers" name="all_managers" value="1"
                      :checked="request()->all_managers ? true : false"/>
          <x-input-label for="all_managers" class="ml-2" :value="__('Все этикетки')"/>
        </div>
      </div>
    </div>
  </x-admin.search-form>
  <div class="mx-auto py-4">
    <div id="action-box" class="flex items-center flex-wrap -mx-1 hidden">
      <div class="p-1 w-full lg:w-1/3">
        <div class="form-group">
          <select name="action" form="action" class="form-control" id="do_action">
            <option>Действие с выбранными</option>
            <optgroup label="Получить файл">
              <option value="group">Объединить</option>
            </optgroup>
            <optgroup label="Установить статус">
              <option value="print_true">Напечатан</option>
              <option value="print_false">Не напечатан</option>
            </optgroup>
          </select>
        </div>
      </div>
      <div class="p-1 w-full lg:w-2/3 flex justify-end">
        <button class="button button-success" id="actioncell_submit" form="action">Применить</button>
      </div>
    </div>
    <div class="bg-yellow-200"></div>
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
        <thead>
        <tr>
          <th class="bg-gray-100 border p-2">
            <input type="checkbox" class="action" id="check_all">
          </th>
          <th class="bg-gray-100 border p-2">ID</th>
          <th class="bg-gray-100 border p-2">Файл</th>
          <th class="bg-gray-100 border p-2">Комментарий</th>
          <th class="bg-gray-100 border p-2">Количество<br/>страниц</th>
          <th class="bg-gray-100 border p-2">Уникальных<br/>заказов</th>
          <th class="bg-gray-100 border p-2">Дата создания</th>
          <th class="bg-gray-100 border p-2" style="width:120px"></th>
        </tr>
        </thead>
        <tbody>
        @foreach($tickets as $ticket)
          <tr class="file
                  @if($ticket->is_author)
                    {{ ' author' }}
                  @endif
                  @if($ticket->tickets->count()==0 && $ticket->count_pages > $ticket->orders()->count())
                    {{ ' bg-yellow-200' }}
                  @endif"
          >
            <td class="border p-2">
              @if($ticket->id)
                <input type="checkbox" name="ticket_ids[]" form="action" value="{{ $ticket->id }}" class="action" id="checkbox_{{ $ticket->id }}">
              @endif
            </td>
            <td class="border p-2">
              {{ $ticket->id }}
            </td>
            <td class="border p-2">
              @if(isset($ticket->data['printed'])&&$ticket->data['printed'])
                {{ '✅ ' }}
              @endif
              @if($ticket->tickets->count())
                <a href="javascript:;" data-src="{!! route('admin.tickets.ticket_split', $ticket->id) !!}" data-fancybox data-type="iframe" class="badge badge-green" style="font-weight:normal;font-size: 1.1em;">{{ denum($ticket->tickets->count(), $string = ['%d файл','%d файла','%d файлов']) }}</a>
              @endif
              @if($ticket->parent)
                <span class="badge badge-yellow" style="font-weight:normal;font-size: 1.1em;">Объединен</span> <a href="{{ $ticket->file_path }}" target="_blank">{{ $ticket->file_name }}</a>
              @else
                <a href="{{ $ticket->file_path }}" target="_blank">{{ $ticket->file_name }}</a> ({{ $ticket->size }})
              @endif

              @if(isset($ticket->data['cart']))
                <br/><a href="{{ url($ticket->data['cart']) }}" target="_blank" class="text-gray-400" style="text-decoration: underline">состав заказов</a>
              @endif

            </td>
            <td class="border p-2" style="font-size: .8em" id="comment-{{ $ticket->id }}">{{ $ticket->data['comment'] ?? '' }}</td>
            <td class="border p-2 text-center">{{ $ticket->count_pages }}</td>
            <td class="border p-2 text-center">
              @if($ticket->tickets->count())
                {{ $ticket->getOrders() }}
              @else
                {{ $ticket->orders()->count() }}
              @endif
            </td>
            <td class="border p-2">{{ date('d.m.Y H:i:s', strtotime($ticket->created_at)) }}</td>
            <td class="border p-2">
              @if(auth()->user()->hasPermissionTo('Комментирование заказов'))
                <div id="comment-ticket-{{ $ticket->id }}" class="hidden w-full max-w-2xl">
                  <form action="{{ route('admin.tickets.comment', $ticket->id) }}" method="POST" class="p-4">
                    @csrf
                    <div class="form-group">
                      <x-input-label for="comment" :value="__('Комментарий ')" />
                      <x-textarea name="comment" id="comment" class="mt-1 w-full"></x-textarea>
                    </div>
                    <x-primary-button>Добавить</x-primary-button>
                  </form>
                </div>

              @endif
              <x-dropdown_menu>
                <x-slot name="content">
                  <div class="py-1" role="none">
                    <a href="javascript:;" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1" data-fancybox="comment-ticket-{{ $ticket->id }}" data-src="#comment-ticket-{{ $ticket->id }}">Добавить комментарий</a>
                    <a class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1" href="{{ route('admin.tickets.invoice', $ticket->id) }}">Накладная</a>
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
      {{ $tickets->appends(request()->input())->links() }}
    </div>
  </div>
  <form action="{{ route('admin.tickets.batchUpdate') }}" id="action" method="POST">
    @csrf
    @method('PUT')
  </form>
</x-admin-layout>

