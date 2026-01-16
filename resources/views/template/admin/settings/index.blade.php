@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
      <a href="{{ route('admin.sytstem_settings.create') }}" class="button button-success">Добавить настройку</a>
    @endif
  </x-slot>
  <div class="mx-auto py-4">
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md">
        <thead>
        <tr>
          <th class="bg-gray-100 border p-2">#</th>
          <th class="bg-gray-100 border p-2">Код</th>
          <th class="bg-gray-100 border p-2">Значение</th>
          <th class="bg-gray-100 border p-2" style="width:120px"></th>
        </tr>
        </thead>
        <tbody>
        @forelse($settings as $setting)
          <tr>
            <td class="border p-2">{{ ($settings->currentPage()-1) * $settings->perPage() + $loop->index + 1 }}</td>
            <td class="border p-2">{{ $setting->key }}</td>
            <td class="border p-2 text-left">{!! $setting->value !!}</td>
            <td class="border p-2" class="text-right">
              <form action="{{ route('admin.sytstem_settings.destroy', $setting->id) }}" id="delete-setting-{{ $setting->id }}" method="POST">
                @csrf
                @method('DELETE')
              </form>
              <x-dropdown_menu>
                <x-slot name="content">
                  <div class="py-1" role="none">
                    <a href="{{ route('admin.sytstem_settings.edit', $setting->id)  }}" class="hover:bg-gray-100 no-underline text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1">Редактировать</a>
{{--                    <a href="#" class="hover:bg-gray-100 no-underline text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1" onclick="if(confirm('Удалить запись «{{ $setting->key }}»?'))document.getElementById('delete-setting-{{ $setting->id }}').submit();">Удалить</a>--}}
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
      {{ $settings->appends(request()->input())->links('pagination::tailwind') }}
    </div>
  </div>
</x-admin-layout>
