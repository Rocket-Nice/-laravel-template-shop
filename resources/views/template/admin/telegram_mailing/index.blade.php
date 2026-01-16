@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
      <a href="{{ route('admin.telegram_mailing.create') }}" class="button button-success">Создать новую рассылку</a>
      {{--      <a href="{{ route('admin.mailing_lists.create') }}" class="button button-success">Создать новую рассылку</a>--}}
    @endif
  </x-slot>
  <div class="mx-auto py-4">
    <div class="bg-blue-100 border-l-4 border-blue-500 text-gray-700 p-4 mb-6">
      @if($jobs->count())
        <strong>Рассылки</strong><br/>
        <div class="space-y-2">
          @foreach($jobs as $mailing)
            <div class="border border-gray-400 rounded-md p-2">
              Количество сообщений: {{ $mailing->total_count }}<br/>
              Статус: {{ $mailing->has_reserved ? 'Запущена' : 'Запланирована на '.date('d.m.Y H:i', $mailing->min_available_at) }}<br/>
              <form action="{{ route('admin.telegram_mailing.cancel') }}" id="cancel-mailing-{{ $mailing->min_available_at }}" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="available_at" value="{{ $mailing->min_available_at }}">
              </form>
              <a href="#" onclick="if(confirm('Отменить рассылку?'))document.getElementById('cancel-mailing-{{ $mailing->min_available_at }}').submit();" class="underline hover:no-underline">Отменить расссылку</a>
            </div>
          @endforeach
        </div>
      @else
        <div>Нет запущенных или запланированных рассылок</div>
      @endif
    </div>
    <style>
      .short-text {
        display: block;
        max-width: 290px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }
    </style>
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
        <thead>
        <tr>
          <th class="bg-gray-100 border p-2" style="width: 5%">#</th>
          <th class="bg-gray-100 border p-2">Дата</th>
          <th class="bg-gray-100 border p-2">Количество получателей</th>
          <th class="bg-gray-100 border p-2">Текст</th>
          <th class="bg-gray-100 border p-2" style="width:120px"></th>
        </tr>
        </thead>
        <tbody>
        @foreach($mailing_lists as $mailing_list)
          <tr>
            <td class="border p-2">{{ $mailing_list->id }}</td>
            <td class="border p-2">{{ $mailing_list->send_at ? $mailing_list->send_at->format('d.m.Y H:i') : '' }}</td>
            <td class="border p-2">{{ $mailing_list->mailing ?  $mailing_list->mailing->users()->count() : null }}</td>
            <td class="border p-2"><span class="short-text">{!! $mailing_list->message !!}</span></td>
            <td class="border p-2">
              <form action="{{ route('admin.telegram_mailing.destroy', $mailing_list->id) }}" id="delete-mailing_list-{{ $mailing_list->id }}" method="POST">
                @csrf
                @method('DELETE')
              </form>
              <div class="hidden">
                <div id="send-mailing-{{ $mailing_list->id }}" class="hidden w-full max-w-2xl">

                  <form action="{{ route('admin.telegram_mailing.send', $mailing_list->id) }}" class="p-4" method="POST">
                    <div class="mb-10 rounded-md p-4 border border-gray-400 max-w-2xl">
                      @if($mailing_list->image)
                        <img src="{{ $mailing_list->image }}" alt="" class="w-full mb-4">
                      @elseif($mailing_list->video)
                        <video controls width="100%" style="max-height: 50vh">
                          <source src="{{ $mailing_list->video }}" type="video/quicktime">
                        </video>
                      @endif
                      <div>
                        {!! nl2br($mailing_list->message) !!}
                      </div>
                    </div>
                    @csrf
                    <div class="font-bold mb-4">Сообщения будут отправлены {{ $mailing_list->send_at->isFuture() ? $mailing_list->send_at->format('d.m.Y H:i') : 'сейчас' }}</div>
                    <div x-data="{type: '1'}" class="mb-4">
                      <div class="form-group">
                        <x-input-label for="type" :value="__('Тип отправки')" />
                        <select id="type" name="type" x-model="type" class="form-control w-full">
                          <option value="1">Тестовая</option>
                          <option value="2">Прод</option>
                        </select>
                      </div>
                      <div class="form-group" x-show="type === '1'">
                        @foreach($test_users as $user)
                          <div class="flex items-center">
                            <x-checkbox id="user-{{ $user->id }}" name="users[]" value="{{ $user->id }}" :checked="true"/>
                            <x-input-label for="user-{{ $user->id }}" class="ml-2" :value="$user->name"/>
                          </div>
                        @endforeach
                      </div>
                    </div>
                    <x-primary-button>Отправить</x-primary-button>
                  </form>
                </div>
              </div>
              <x-dropdown_menu>
                <x-slot name="content">
                  <div class="py-1" role="none">
                    <a href="javascript:;" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" data-fancybox data-src="#send-mailing-{{ $mailing_list->id }}" role="menuitem" tabindex="-1">Отправить рассылку</a>
                    <a href="{{ route('admin.telegram_mailing.edit', $mailing_list->id) }}" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1">Редактировать</a>
                    <a href="#" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1" onclick="if(confirm('Удалить запись «{{ $mailing_list->name }}»?'))document.getElementById('delete-mailing_list-{{ $mailing_list->id }}').submit();">Удалить</a>
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
      {{ $mailing_lists->appends(request()->input())->links('pagination::tailwind') }}
    </div>
  </div>
</x-admin-layout>

