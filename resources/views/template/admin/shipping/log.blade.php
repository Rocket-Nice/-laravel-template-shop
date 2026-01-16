@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <x-admin.search-form :route="route('admin.shipping.log')">
    <div class="p-1 w-full">
      <div class="form-group">
        <x-input-label for="title" :value="__('Заголовок сообщения')" />
        <x-text-input type="text" name="title" id="title" value="{{ request()->get('title') }}" class="mt-1 block w-full" />
      </div>
    </div>
    <div class="p-1 w-full">
      <div class="form-group">
        <label>Доставка</label>
        <select class="form-control" name="type" style="width: 100%;">
          <option value="{all}" @if(!request()->get('type')){!! 'selected' !!}@endif>Все</option>
          <option value="cdek" @if(request()->get('type')&&request()->get('type')=='cdek'){!! 'selected' !!}@endif>СДЭК</option>
          <option value="yandex" @if(request()->get('type')&&request()->get('type')=='yandex'){!! 'selected' !!}@endif>Яндекс Доставка</option>
          <option value="pochta" @if(request()->get('type')&&request()->get('type')=='pochta'){!! 'selected' !!}@endif>Почта</option>
        </select>
      </div>
    </div>
  </x-admin.search-form>
  <div class="mx-auto mt-4">
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
        <thead>
        <tr>
          <th class="bg-gray-100 border p-2" style="width: 10%">Доставка</th>
          <th class="bg-gray-100 border p-2" style="width: 20%">Заголовок</th>
          <th class="bg-gray-100 border p-2">Комментарий</th>
          <th class="bg-gray-100 border p-2" style="width: 20%">Дата</th>
        </tr>
        </thead>
        <tbody>
        @forelse($log as $note)
          <tr class="@if(Str::contains(Str::lower($note->title), Str::lower('ошибка'))) bg-red-100 @endif">
            <td class="border p-2">
              @if($note->code == 'cdek')
                СДЭК
              @elseif($note->code == 'boxberry')
                Boxberry
              @elseif($note->code == 'yandex')
                Яндекс Доставка
              @elseif($note->code == 'x5post')
                5 Пост
              @elseif($note->code == 'pochta')
                Почта
              @endif
            </td>
            <td class="border p-2">
              {{ $note->title }}
            </td>
            <td class="border p-2 text-left">
              {{ $note->text }}
            </td>
            <td class="border p-2">
              {{ \Carbon\Carbon::create($note->created_at)->format('d.m.Y H:i:s') }}
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
