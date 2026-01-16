@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <form action="{{ route('admin.users.store') }}" method="post">
    @csrf
    <div class="border-b">
      <div class="mx-auto px-2 sm:px-3 lg:px-4">
        <nav class="-mb-px flex flex-col sm:flex-row sm:justify-start space-y-2 sm:space-y-0 sm:space-x-4"
             aria-label="Tabs" role="tablist">
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-1" aria-selected="true" role="tab" aria-controls="tab-1-content">Общие данные
          </button>
        </nav>
      </div>
    </div>

    <div class="mx-auto px-2 sm:px-3 lg:px-4 py-6" id="tab-content">
      <div id="tab-1-content" role="tabpanel">
        <div class="sm:w-[50%]">
          <div class="form-group">
            <x-input-label for="name" :value="__('Имя')" />
            <x-text-input type="text" name="name" id="name" value="{{ old('name') }}" class="mt-1 block w-full" required />
          </div>
          <div class="form-group">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input type="text" name="email" id="email" value="{{ old('email') }}" class="mt-1 block w-full" required />
          </div>
          <div class="form-group">
            <x-input-label for="phone" :value="__('Телефон')" />
            <x-text-input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="mt-1 block w-full" required />
          </div>

          <div class="form-group">
            <x-input-label for="password" :value="__('Пароль')" />
            <x-text-input type="text" name="password" id="password" value="{{ old('password') ?? mb_strtolower(getCode(6)) }}" class="mt-1 block w-full" required />
          </div>
          <div class="form-group">
            <x-input-label for="role" :value="__('Роль')" />
            <select id="role" name="role" class="form-control w-full">
              <option value="">Выбрать</option>
              @foreach($roles as $role)
                <option value="{{ $role->id }}">{{ $role->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
    </div>
    <div class="px-3 sm:px-4 lg:px-6 py-6 flex justify-end">
      <x-primary-button>Сохранить</x-primary-button>
    </div>
  </form>
</x-admin-layout>
