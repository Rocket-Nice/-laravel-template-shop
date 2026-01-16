@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>

  <x-slot name="header">
    @if(isset($seo['title']))
    <h1 class="text-3xl font-semibold m-2">
      {{ $seo['title'] }}
    </h1>
    @endif
      <div class="text-center">
        <a href="{{ route('admin.permissions.create') }}" class="button button-success m-2">Добавить разрешение</a>
      </div>
  </x-slot>
  <div class="mx-auto">
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
        <thead>
        <tr>
          <th class="bg-gray-100 border p-2" style="width:5%">#</th>
          <th class="bg-gray-100 border p-2 text-left" style="width:15%">Наименование</th>
          <th class="bg-gray-100 border p-2">Роли</th>
          <th class="bg-gray-100 border p-2" style="width:5%"></th>
        </tr>
        </thead>
        <tbody>
        @forelse($permissions as $permission)
          <tr>
            <td class="border p-2">{{ $permission->id }}</td>
            <td class="border p-2 text-left">
              {{ $permission->name }}
            </td>
            <td class="border p-2">
              @foreach($permission->roles as $role)
                <span class="badge-yellow text-xs">{{ $role->name }}</span>
              @endforeach
            </td>
            <td class="border p-2 text-center">
              <a class="button button-light-secondary button-sm" href="{{ route('admin.permissions.edit', $permission->id) }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pencil" width="24" height="24" viewBox="0 0 24 24" stroke-width="1" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                  <path d="M4 20h4l10.5 -10.5a1.5 1.5 0 0 0 -4 -4l-10.5 10.5v4" />
                  <path d="M13.5 6.5l4 4" />
                </svg>
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7">
              <div class="text-gray-400 text-2xl p-5 text-center">Нет разрешений</div>
            </td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>
    <div class="p-2">
      {{ $permissions->appends(request()->input())->links('pagination::tailwind') }}
    </div>
  </div>
</x-admin-layout>
