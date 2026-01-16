@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
      <a href="{{ route('admin.users.create') }}" class="button button-success">Добавить пользователя</a>
    @endif
  </x-slot>
  <x-admin.search-form :route="route('admin.users.admins')">
    <div class="p-1 w-full lg:w-1/3">
      <div class="form-group">
        <x-input-label for="name" :value="__('Имя')"/>
        <x-text-input type="text" name="name" id="name" value="{{ request()->get('name') }}"
                      class="mt-1 block w-full"/>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/3">
      <div class="form-group">
        <x-input-label for="email" :value="__('Email')"/>
        <x-text-input type="text" name="email" id="email" value="{{ request()->get('email') }}"
                      class="mt-1 block w-full"/>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/3">
      <div class="form-group">
        <x-input-label for="phone" :value="__('Телефон')"/>
        <x-text-input type="text" name="phone" id="phone" value="{{ request()->get('phone') }}"
                      class="mt-1 block w-full"/>
      </div>
    </div>
  </x-admin.search-form>

  <div class="mx-auto py-4">
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md">
        <thead>
        <tr>
          <th class="bg-gray-100 border p-2">#</th>
          <th class="bg-gray-100 border p-2">Имя</th>
          <th class="bg-gray-100 border p-2">Email</th>
          <th class="bg-gray-100 border p-2">Телефон</th>
          <th class="bg-gray-100 border p-2" style="width:120px"></th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
          <tr>
            <td class="border p-2">{{ $user->id }}</td>
            <td class="border p-2 text-left">{{ $user->name }}</td>
            <td class="border p-2">{{ $user->email }}</td>
            <td class="border p-2">{{ formatPhoneNumber($user->phone) }}</td>
            <td class="border p-2 text-right">
              <form action="{{ route('admin.users.auth', $user->id) }}" id="auth-form-{{ $user->id }}" method="POST">
                @csrf
              </form>
              <x-dropdown_menu>
                <x-slot name="content">
                  <div class="py-1" role="none">
                    <a href="{{ route('admin.users.edit', $user->id)  }}" class="hover:bg-gray-100 no-underline text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1">Редактировать</a>
                    <a href="#" class="hover:bg-gray-100 no-underline text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1" onclick="if(confirm('Авторизоваться от имени «{{ $user->name }}»?'))document.getElementById('auth-form-{{ $user->id }}').submit();">Авторизоваться</a>
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
      {{ $users->appends(request()->input())->links('pagination::tailwind') }}
    </div>
  </div>
</x-admin-layout>
