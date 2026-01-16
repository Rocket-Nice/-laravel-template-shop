@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
      <a href="{{ route('admin.tg_notifications.settings') }}" class="button button-success mb-2 md:mb-0">Настройки</a>
    @endif
  </x-slot>
  <div class="mx-auto py-4">
    @if(isset($bot['result']['id']))
    Бот <a href="https://t.me/{{ $bot['result']['username'] }}?start={{ auth()->user()->uuid }}" target="_blank">{{ $bot['result']['first_name'] }}</a>
    @endif
      <div class="relative overflow-x-auto min-h-[500px] mt-4">
        <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md">
          <thead>
          <tr>
            <th class="bg-gray-100 border p-2">#</th>
            <th class="bg-gray-100 border p-2">Чат</th>
            <th class="bg-gray-100 border p-2">Пользователь</th>
            <th class="bg-gray-100 border p-2">Последнее сообщение</th>
            <th class="bg-gray-100 border p-2">Статус</th>
            <th class="bg-gray-100 border p-2" style="width:120px"></th>
          </tr>
          </thead>
          <tbody>
          @forelse($tgChats as $tgChat)
            <tr>
              <td class="border p-2">{{ ($tgChats->currentPage()-1) * $tgChats->perPage() + $loop->index + 1 }}</td>
              <td class="border p-2">{{ $tgChat->getChatName() }}</td>
              <td class="border p-2"><a href="{{ route('admin.users.edit', $tgChat->user_id) }}">{{ $tgChat->user }}</a></td>
              <td class="border p-2">{{ $tgChat->last_message ? \Carbon\Carbon::parse($tgChat->last_message)->format('d.m.Y H:i:s') : '' }}</td>
              <td class="border p-2">{!! $tgChat->active ? '<span class="badge-green text-xs">Включен</span>' : '<span class="badge-red text-xs">Выключен</span>' !!}</td>
              <td class="border p-2" class="text-right">
{{--                <form action="{{ route('admin.sytstem_settings.destroy', $setting->id) }}" id="delete-setting-{{ $setting->id }}" method="POST">--}}
{{--                  @csrf--}}
{{--                  @method('DELETE')--}}
{{--                </form>--}}
                <x-dropdown_menu>
                  <x-slot name="content">
                    <div class="py-1" role="none">
                      <a href="{{ route('admin.tg_notifications.show', $tgChat->id)  }}" class="hover:bg-gray-100 no-underline text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1">Открыть чат</a>
                      <a href="https://t.me/{{ $tgChat->username }}" class="hover:bg-gray-100 no-underline text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1">Открыть в телеграм</a>
{{--                  <a href="#" class="hover:bg-gray-100 no-underline text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1" onclick="if(confirm('Удалить запись «{{ $setting->key }}»?'))document.getElementById('delete-setting-{{ $setting->id }}').submit();">Удалить</a>--}}
                    </div>
                  </x-slot>
                </x-dropdown_menu>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7">
                <div class="text-gray-400 text-2xl p-5 text-center">Нет настроек</div>
              </td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>
      <div class="p-2">
        {{ $tgChats->appends(request()->input())->links('pagination::tailwind') }}
      </div>
  </div>
</x-admin-layout>
