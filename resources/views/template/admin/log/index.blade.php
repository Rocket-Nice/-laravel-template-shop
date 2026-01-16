@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <x-admin.search-form :route="route('admin.log.index')">
    <div class="p-1 w-full">
      <div class="form-group">
        <x-input-label for="id" :value="__('ID')"/>
        <x-text-input type="text" name="id" id="id" value="{{ request()->get('id') }}" class="mt-1 block w-full"/>
      </div>
    </div>
    <div class="p-1 w-full">
      <div class="form-group">
        <label>Тип объекта</label>
        <select class="form-control" name="type" style="width: 100%;">
          <option value="{all}" @if(!request()->get('type'))
            {!! 'selected' !!}
            @endif>Все
          </option>
          <option value="App\Models\Order" @if(request()->get('type')&&request()->get('type')=='App\Models\Order')
            {!! 'selected' !!}
            @endif>Заказы
          </option>
          <option value="App\Models\Prize" @if(request()->get('type')&&request()->get('type')=='App\Models\Prize')
            {!! 'selected' !!}
            @endif>Подарки
          </option>
          <option
            value="App\Models\Coupone" @if(request()->get('type')&&request()->get('type')=='App\Models\Coupone')
            {!! 'selected' !!}
            @endif>Промокоды
          </option>
          <option
            value="App\Models\Voucher" @if(request()->get('type')&&request()->get('type')=='App\Models\Voucher')
            {!! 'selected' !!}
            @endif>Подарочные сертификаты
          </option>
          <option
            value="App\Models\Product" @if(request()->get('type')&&request()->get('type')=='App\Models\Product')
            {!! 'selected' !!}
            @endif>Товары
          </option>
          <option
            value="App\Models\Setting" @if(request()->get('type')&&request()->get('type')=='App\Models\Setting')
            {!! 'selected' !!}
            @endif>Настройки
          </option>
        </select>
      </div>
    </div>
  </x-admin.search-form>
  <div class="mx-auto mt-4">
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
        <thead>
        <tr>
          <th class="bg-gray-100 border p-2" style="width: 20%">Объект</th>
          <th class="bg-gray-100 border p-2" style="width: 20%">Действие</th>
          <th class="bg-gray-100 border p-2" style="width: 20%">Комментарий</th>
          <th class="bg-gray-100 border p-2">Дата</th>
          <th class="bg-gray-100 border p-2">Пользователь</th>
          <th class="bg-gray-100 border p-2" style="width: 5%"></th>
        </tr>
        </thead>
        <tbody>
        @forelse($log as $note)
          @php($loggable = $note->loggable)
          <tr>
            <td class="border p-2">
              @if($note->loggable_type == 'App\Models\Prize')
                <span class="text-secondary text-sm">Подарок</span><br/> «{{ $loggable->name?? '' }}»
              @elseif($note->loggable_type == 'App\Models\Order')
                <span class="text-secondary text-sm">Заказ</span><br/> {{ $loggable->id?? '' }}
              @elseif($note->loggable_type == 'App\Models\Voucher')
                <span class="text-secondary text-sm">Подарочный сертификат</span><br/> {{ $loggable->code?? '' }}
              @elseif($note->loggable_type == 'App\Models\Coupone')
                <span class="text-secondary text-sm">Промокод</span><br/> {{ $loggable->code ?? ''}}
              @else
                {{ $note->loggable_type ?? null }}: {{ $loggable->id ?? null }}
              @endif
            </td>
            <td class="border p-2">
              {{ $note->action }}
            </td>
            <td class="border p-2">
              {{ $note->text }}
            </td>
            <td class="border p-2">
              {{ \Carbon\Carbon::create($note->created_at)->format('d.m.Y H:i:s') }}
            </td>
            <td class="border p-2">
              {{ $note->user->email }}
            </td>
            <td class="project-actions text-center">
              @if($note->data)
                <a class="button button-light-secondary button-sm" href="javascript:;"
                   data-src="{{ route('admin.log.show', ['activity_log' => $note->id]) }}" data-type="iframe"
                   data-fancybox>
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye" width="24"
                       height="24" viewBox="0 0 24 24" stroke-width="1" stroke="#2c3e50" fill="none"
                       stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/>
                    <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/>
                  </svg>
                </a>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6">
              <div class="text-gray-400 text-2xl p-5 text-center">Нет записей</div>
            </td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>
    <div class="p-2">
      {{ $log->appends(request()->input())->links('pagination::tailwind') }}
    </div>
  </div>
</x-admin-layout>
