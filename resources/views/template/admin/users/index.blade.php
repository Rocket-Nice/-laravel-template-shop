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
  <x-admin.search-form :route="route('admin.users.index')">
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="date_from" :value="__('Дата создания от')"/>
        <x-text-input type="text" name="date_from" id="date_from" value="{{ request()->get('date_from') }}" data-minDate="false" placeholder="{{ now()->startOfMonth()->format('d.m.Y H:i') }}" class="mt-1 block w-full datepicker"/>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="date_until" :value="__('Дата создания до')"/>
        <x-text-input type="text" name="date_until" id="date_until" value="{{ request()->get('date_until') }}" placeholder="{{ now()->format('d.m.Y H:i') }}" data-minDate="false" class="mt-1 block w-full datepicker"/>
      </div>
    </div>
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

    <div class="p-1 w-full lg:w-1/3">
      <div class="form-group">
        <x-input-label for="users-filter-country" :value="__('Страна')"/>
        <select id="users-filter-country" name="country" class="form-control w-full">
          <option value="">Выбрать</option>
          @foreach($countries as $country)
            <option value="{{ $country->id }}" @if(request()->get('country')&&request()->get('country')==$country->id){!! 'selected' !!}@endif>{{ $country->name }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/3">
      <div class="form-group">
        <x-input-label for="users-filter-region" :value="__('Регион')"/>
        <select id="users-filter-region" name="region" class="form-control w-full">
          <option value="" disabled>Сначала выберите страну</option>
        </select>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/3">
      <div class="form-group">
        <x-input-label for="users-filter-cities" :value="__('Город')"/>
        <select id="users-filter-cities" name="cities[]" class="form-control w-full">
          <option value="" disabled>Сначала выберите регион</option>
        </select>
      </div>
    </div>


    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="lastOrderDateFrom" :value="__('Дата последнего заказа от')"/>
        <x-text-input type="text" name="lastOrderDateFrom" id="lastOrderDateFrom" value="{{ request()->get('lastOrderDateFrom') }}" data-minDate="false" placeholder="{{ now()->startOfMonth()->format('d.m.Y H:i') }}" class="mt-1 block w-full datepicker"/>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="lastOrderDateTo" :value="__('Дата последнего заказа до')"/>
        <x-text-input type="text" name="lastOrderDateTo" id="lastOrderDateTo" value="{{ request()->get('lastOrderDateTo') }}" placeholder="{{ now()->format('d.m.Y H:i') }}" data-minDate="false" class="mt-1 block w-full datepicker"/>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="minAvgOrderTotal" :value="__('Средняя стоимость заказа от')"/>
        <x-text-input type="text" name="minAvgOrderTotal" id="minAvgOrderTotal" value="{{ request()->get('minAvgOrderTotal') }}" class="mt-1 block w-full numeric-field"/>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="maxAvgOrderTotal" :value="__('Средняя стоимость заказа до')"/>
        <x-text-input type="text" name="maxAvgOrderTotal" id="maxAvgOrderTotal" value="{{ request()->get('maxAvgOrderTotal') }}" class="mt-1 block w-full numeric-field"/>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="hasConfirmedOrder" :value="__('Есть оплаченный заказ')"/>
        <select id="hasConfirmedOrder" name="hasConfirmedOrder" class="form-control w-full">
          <option value="">Выбрать</option>
          <option value="n" @if(request()->get('hasConfirmedOrder')&&request()->get('hasConfirmedOrder')=='n'){!! 'selected' !!}@endif>Нет</option>
          <option value="y" @if(request()->get('hasConfirmedOrder')&&request()->get('hasConfirmedOrder')=='y'){!! 'selected' !!}@endif>Да</option>
        </select>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="hasRefundedOrder" :value="__('Есть возвраты')"/>
        <select id="hasRefundedOrder" name="hasRefundedOrder" class="form-control w-full">
          <option value="">Выбрать</option>
          <option value="n" @if(request()->get('hasRefundedOrder')&&request()->get('hasRefundedOrder')=='n'){!! 'selected' !!}@endif>Нет</option>
          <option value="y" @if(request()->get('hasRefundedOrder')&&request()->get('hasRefundedOrder')=='y'){!! 'selected' !!}@endif>Да</option>
        </select>
      </div>
    </div>
    @if(auth()->id()==1&&auth()->user()->hasPermissionTo('Управление рассылками'))
      <div class="p-1 w-full lg:w-1/2">
        <div class="form-group">
          <x-input-label for="mailing_list" :value="__('Получал рассылку')"/>
          <select id="mailing_list" name="mailing_list[]" multiple class="form-control w-full multipleSelect">
            <option value="">Выбрать</option>
            @foreach($mailing_lists as $mailing_list)
              <option value="{{ $mailing_list->id }}" @if(is_array(request()->get('mailing_list'))&&in_array($mailing_list->id, request()->get('mailing_list'))){!! 'selected' !!}@endif>{{ $mailing_list->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="p-1 w-full lg:w-1/2">
        <div class="form-group">
          <x-input-label for="mailing_list" :value="__('Не получал рассылку')"/>
          <select id="mailing_list" name="not_mailing_list[]" multiple class="form-control w-full multipleSelect">
            <option value="">Выбрать</option>
            @foreach($mailing_lists as $mailing_list)
              <option value="{{ $mailing_list->id }}" @if(is_array(request()->get('not_mailing_list'))&&in_array($mailing_list->id, request()->get('not_mailing_list'))){!! 'selected' !!}@endif>{{ $mailing_list->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
    @endif

    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="tg_notices" :value="__('Подключены уведомления в ТГ')"/>
        <select id="tg_notices" name="tg_notices" class="form-control w-full">
          <option value="">Выбрать</option>
          <option value="n" @if(request()->get('tg_notices')&&request()->get('tg_notices')=='n'){!! 'selected' !!}@endif>Нет</option>
          <option value="y" @if(request()->get('tg_notices')&&request()->get('tg_notices')=='y'){!! 'selected' !!}@endif>Да</option>
        </select>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="happy_coupon" :value="__('Участие в акции СК')"/>
        <select id="happy_coupon" name="happy_coupon" class="form-control w-full">
          <option value="">Выбрать</option>
          <option value="n" @if(request()->get('happy_coupon')&&request()->get('happy_coupon')=='n'){!! 'selected' !!}@endif>Нет</option>
          <option value="y" @if(request()->get('happy_coupon')&&request()->get('happy_coupon')=='y'){!! 'selected' !!}@endif>Да</option>
        </select>
      </div>
    </div>
    <div class="p-1 w-full">
      <div class="form-group">
        <div class="flex items-center">
          <x-checkbox id="has_children" name="has_children" value="1"
                      :checked="request()->has_children ? true : false"/>
          <x-input-label for="has_children" class="ml-2" :value="__('Покупали детскую серию')"/>
        </div>
      </div>
    </div>
    @if(auth()->user()->hasPermissionTo('Выгрузка прав пользователей'))
      <div class="p-1 w-full">
        <div class="form-group">
          <x-input-label for="filter-admin" :value="__('Доступ в админпанель')"/>
          <select id="filter-admin" name="is_admin" class="form-control w-full">
            <option @if(request()->hidden===null)
              {!! 'selected' !!}
              @endif>Все
            </option>
            <option value="1" @if(request()->get('is_admin')==='1')
              {!! 'selected' !!}
              @endif>Есть доступ
            </option>
            <option value="0" @if(request()->get('is_admin')==='0')
              {!! 'selected' !!}
              @endif>Нет доступа
            </option>
          </select>
        </div>
      </div>
    @endif
    @if(auth()->user()->hasPermissionTo('Управление рассылками'))
{{--    <div class="p-1 w-full">--}}
{{--      <div class="form-group">--}}
{{--        <div class="flex items-center">--}}
{{--          <x-checkbox id="no_managers" name="no_managers" value="1"--}}
{{--                      :checked="request()->no_managers ? true : false"/>--}}
{{--          <x-input-label for="no_managers" class="ml-2" :value="__('Исключить менеджеров')"/>--}}
{{--        </div>--}}
{{--      </div>--}}
{{--    </div>--}}
      <div class="p-1 w-full">
        <div class="form-group">
          <div class="flex items-center">
            <x-checkbox id="is_subscribed_to_marketing" name="is_subscribed_to_marketing" value="1"
                        :checked="request()->is_subscribed_to_marketing ? true : false"/>
            <x-input-label for="is_subscribed_to_marketing" class="ml-2" :value="__('Подписан на рассылки')"/>
          </div>
        </div>
      </div>

      <div class="p-1 w-full">
        <div class="form-group">
          <div class="flex items-center">
            <x-checkbox id="is_denied_to_marketing" name="is_denied_to_marketing" value="1"
                        :checked="request()->is_denied_to_marketing ? true : false"/>
            <x-input-label for="is_denied_to_marketing" class="ml-2" :value="__('Отказался от рассылок')"/>
          </div>
        </div>
      </div>
    @endif
  </x-admin.search-form>
  <div class="mx-auto py-4">
    <div class="flex justify-between py-2">
      <div>Всего {{ formatPrice($users->total(), false, '') }}</div>
      @if(auth()->user()->hasPermissionTo('Выгрузка пользователей'))
        <form action="{{ route('admin.users.export')  }}{{ stristr($_SERVER['REQUEST_URI'], '?') }}" id="export-users" method="POST">
          @csrf
        </form>

        @if(auth()->user()->hasPermissionTo('Выгрузка пользователей'))
          <form action="{{ route('admin.users.export-permissions')  }}" id="export-user-permissions" method="POST">
            @csrf
          </form>
        @endif
        <x-ui.dropdown :trigger="'Действие'">
          <x-ui.dropdown-link href="#" onclick="document.getElementById('export-users').submit();">Выгрузить в Excel</x-ui.dropdown-link>
          <x-ui.dropdown-link href="#" onclick="document.getElementById('export-user-permissions').submit();">Выгрузить права в Excel</x-ui.dropdown-link>
          <x-ui.dropdown-link href="{{ route('admin.export_data.index')  }}">Открыть выгруженные файлы</x-ui.dropdown-link>
        </x-ui.dropdown>
      @endif
    </div>
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
        <thead>
        <tr>
          <th class="bg-gray-100 border p-2">#</th>
          <th class="bg-gray-100 border p-2">Имя</th>
          <th class="bg-gray-100 border p-2">Email</th>
          <th class="bg-gray-100 border p-2">Телефон</th>
          <th class="bg-gray-100 border p-2">Бонусы</th>
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
            <td class="border p-2">{{ formatPrice($user->getBonuses(), false, '') }}</td>
            <td class="border p-2 text-right">
              <form action="{{ route('admin.users.auth', $user->id) }}" id="auth-form-{{ $user->id }}" method="POST">
                @csrf
              </form>
              <x-dropdown_menu>
                <x-slot name="content">
                  <div class="py-1" role="none">
                    <a href="{{ route('admin.users.edit', $user->id)  }}" class="hover:bg-gray-100 no-underline text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1">Редактировать</a>
                    @if(!$user->hasPermissionTo('Доступ к админпанели')||auth()->user()->hasRole('admin'))
                    <a href="#" class="hover:bg-gray-100 no-underline text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1" onclick="if(confirm('Авторизоваться от имени «{{ $user->name }}»?'))document.getElementById('auth-form-{{ $user->id }}').submit();">Авторизоваться</a>
                    @endif
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
  @if(auth()->id()==1&&auth()->user()->hasPermissionTo('Управление рассылками'))
    <div id="mailing_list_form" class="hidden w-full max-w-2xl" style="display: none;">
      <form action="{{ route('admin.mailing_lists.add_users') }}{{ stristr($_SERVER['REQUEST_URI'], '?') }}" class="p-4" method="POST">
        @csrf
        <div class="form-group">
          <x-input-label for="mailing_list_id" :value="__('Выберите рассылку')"/>
          <select id="mailing_list_id" name="mailing_list_id" class="form-control w-full" required>
            <option value="">Выбрать</option>
            @foreach($mailing_lists as $mailing_list)
              <option value="{{ $mailing_list->id }}">{{ $mailing_list->name }}</option>
            @endforeach
          </select>
        </div>
        <x-primary-button>Добавить</x-primary-button>
      </form>
    </div>
  @endif
  <script>
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    var region;
    var cities = [];
    var citiesChoise = null;

    // end
    window.load_regions_route = @json(route('admin.users.regions'));
    window.load_cities_route = @json(route('admin.users.cities'));
    const countryField = document.getElementById('users-filter-country');
    const regionField = document.getElementById('users-filter-region');
    const citiesField = document.getElementById('users-filter-cities');

    countryField.addEventListener('change', function(){
      if(countryField.value == ''){
        return false;
      }
      const requestParams = {
        country: countryField.value
      }
      window.ajax.post(window.load_regions_route, requestParams, (response) => {
        if(response){
          var html = '<option value="" selected>Выберите регион</option>'
          if (urlParams.has('region')) {
            region = urlParams.get('region');
          }
          response.forEach((item) => {
            if(region == item.id){
              html += `<option value="${item.id}" selected>${item.name}</option>`
            }else{
              html += `<option value="${item.id}">${item.name}</option>`
            }
          })
          regionField.innerHTML = html
          if(region){
            const eventChange = new Event('change')
            regionField.dispatchEvent(eventChange)
          }
        }
      })
    })
    regionField.addEventListener('change', function(){
      if(countryField.value == ''){
        return false;
      }
      if(regionField.value == ''){
        return false;
      }
      const requestParams = {
        country: countryField.value,
        region: regionField.value
      }
      window.ajax.post(window.load_cities_route, requestParams, (response) => {
        if(response){
          console.log('response', response)
          if (citiesChoise !== null){
            citiesChoise.passedElement.element.removeEventListener('showDropdown', window.checkChoisesDropdown);
            citiesChoise.destroy();
          }

          var html = '<option value="" selected>Выберите город</option>'
          if (urlParams.has('cities[]')) {
            cities = urlParams.getAll('cities[]');
          }

          if(!citiesField.multiple){
            citiesField.multiple = true;
          }
          response.forEach((item) => {
            if(cities.includes(String(item.id))){
              html += `<option value="${item.id}" selected>${item.name}</option>`
            }else{
              html += `<option value="${item.id}">${item.name}</option>`
            }
          })
          citiesField.innerHTML = html

          citiesChoise = new Choices(citiesField, {
            removeItemButton: true,
            shouldSort: false,
            noChoicesText: 'Пусто',
            itemSelectText: ''
          })
          citiesChoise.passedElement.element.addEventListener('showDropdown', window.checkChoisesDropdown);
        }
      })
    })
    document.addEventListener('DOMContentLoaded', function() {
      if(countryField.value != ''){
        const eventChange = new Event('change')
        countryField.dispatchEvent(eventChange)
      }
    })

  </script>
</x-admin-layout>
