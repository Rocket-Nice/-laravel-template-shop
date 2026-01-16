@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <x-admin.search-form :route="url()->current()">
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="date_from" :value="__('Дата от')"/>
        <x-text-input type="text" name="date_from" id="date_from" value="{{ request()->get('date_from') }}"
                      data-minDate="false" placeholder="{{ now()->startOfMonth()->format('d.m.Y H:i') }}"
                      class="mt-1 block w-full datepicker"/>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="date_until" :value="__('Дата до')"/>
        <x-text-input type="text" name="date_until" id="date_until" value="{{ request()->get('date_until') }}"
                      placeholder="{{ now()->format('d.m.Y H:i') }}" data-minDate="false"
                      class="mt-1 block w-full datepicker"/>
      </div>
    </div>
    <div class="p-1 w-full lg:w-full">
      <div class="form-group">
        <x-input-label for="email" :value="__('Email пользователя')"/>
        <x-text-input type="text" name="email" id="email" value="{{ request()->get('email') }}"
                      class="mt-1 block w-full"/>
      </div>
    </div>
    <div class="p-1 w-full">
      <div class="form-group">
        <x-input-label for="filter-status" :value="__('Статус')"/>
        <select id="filter-status" name="status" class="form-control w-full">
          <option>Выбрать</option>
          @foreach(\App\Models\CustomForm::STATUS as $key => $status)
            <option value="{{ $key }}" @if(request()->get('status') !== null && request()->get('status') == $key){{ 'selected' }}@endif>{{ $status }}</option>
          @endforeach
        </select>
      </div>
    </div>
{{--    <div class="p-1 w-full">--}}
{{--      <div class="form-group">--}}
{{--        <x-input-label for="filter-orderBy" :value="__('Сортировка')"/>--}}
{{--        <select id="filter-orderBy" name="orderBy" class="form-control w-full">--}}
{{--          <option>Выбрать</option>--}}
{{--          <option value="nps_score|asc" @if(request()->get('orderBy')&&request()->get('orderBy')=='nps_score|asc')--}}
{{--            {!! 'selected' !!}--}}
{{--            @endif>Сначала низкие оценки--}}
{{--          </option>--}}
{{--          <option value="nps_score|desc" @if(request()->get('orderBy')&&request()->get('orderBy')=='nps_score|desc')--}}
{{--            {!! 'selected' !!}--}}
{{--            @endif>Сначала высокие оценки--}}
{{--          </option>--}}
{{--          <option value="created_at|asc" @if(request()->get('orderBy')&&request()->get('orderBy')=='created_at|asc')--}}
{{--            {!! 'selected' !!}--}}
{{--            @endif>Сначала старые--}}
{{--          </option>--}}
{{--          <option value="created_at|desc" @if(request()->get('orderBy')&&request()->get('orderBy')=='created_at|desc')--}}
{{--            {!! 'selected' !!}--}}
{{--            @endif>Сначала новые--}}
{{--          </option>--}}
{{--        </select>--}}
{{--      </div>--}}
{{--    </div>--}}
  </x-admin.search-form>
  <div class="mx-auto py-4">
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
        <thead>
        <tr>
          <th class="bg-gray-100 border p-2 text-left">Пользователь</th>
          <th class="bg-gray-100 border p-2">Телефон</th>
          <th class="bg-gray-100 border p-2">Адрес</th>
          <th class="bg-gray-100 border p-2">Желание</th>
          <th class="bg-gray-100 border p-2" style="width:120px"></th>
        </tr>
        </thead>
        <tbody>
        @foreach($usersData as $user)
          <tr>
            <td class="border p-2 text-left">{{ $user->formAnswers()->where('field_id', 1)->first()->value }}<br/><a href="{{ route('admin.users.edit', ['user' => $user->id]) }}">{{ $user->email }}</a></td>
            <td class="border p-2">{{ $user->formAnswers()->where('field_id', 2)->first()->value }}</td>
            <td class="border p-2">{{ $user->formAnswers()->where('field_id', 3)->first()->value }}</td>
            <td class="border p-2 relative">
              <div class="hidden">
                <div id="wish-{{ $user->id }}" class="hidden w-full max-w-2xl">
                  <form action="{{ route('admin.custom-forms.change', [
                  'form_id' => $user->formAnswers()->where('field_id', 4)->first()->form_id,
                  'field_id' => $user->formAnswers()->where('field_id', 4)->first()->field_id,
                  'user_id' => $user->id]) }}" class="p-4" method="POST">
                    @csrf
                    <div class="form-group">
                      <x-textarea name="value" id="value" class="mt-1 w-full">{{ $user->formAnswers()->where('field_id', 4)->first()->value }}</x-textarea>
                    </div>
                    <x-primary-button>Обновить</x-primary-button>
                  </form>
                </div>
              </div>
              <a href="javascript:;" data-fancybox data-src="#wish-{{ $user->id }}" class="block absolute top-2 right-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="14" height="14" stroke-width="1" style="opacity: .5"> <path d="M14 6l7 7l-4 4"></path> <path d="M5.828 18.172a2.828 2.828 0 0 0 4 0l10.586 -10.586a2 2 0 0 0 0 -2.829l-1.171 -1.171a2 2 0 0 0 -2.829 0l-10.586 10.586a2.828 2.828 0 0 0 0 4z"></path> <path d="M4 20l1.768 -1.768"></path> </svg>
              </a>
              <div>
                {{ $user->formAnswers()->where('field_id', 4)->first()->value }}
              </div>
            </td>
            <td class="border p-2">
              <select x-data="wishesStatus" x-init="init()" @change.prevent="setStatus('{{ route('admin.custom-forms.status', $user->id) }}')" name="status" :class="color" class="block rounded-md border-0 py-1 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 sm:text-sm sm:leading-6">
                @foreach(\App\Models\CustomForm::STATUS as $key => $status)
                  <option value="{{ $key }}" @if($key == $user->pivot->status){{ 'selected' }}@endif>{{ $status }}</option>
                @endforeach
              </select>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    <div class="p-2">
      {{ $usersData->appends(request()->input())->links('pagination::tailwind') }}
    </div>
  </div>
</x-admin-layout>

