@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <div class="mx-auto py-4">
    <div id="action-box" class="flex items-center flex-wrap -mx-1 hidden">
      <div class="p-1 w-full lg:w-1/3">
        <div class="form-group">
          <select name="action" form="action" class="form-control" id="do_action">
            <option>Действие с выбранными</option>
            <optgroup label="Включить">

              <option value="toggle|{{ implode(',', $shipping_methods->pluck('code')->toArray()) }}|1">Включить для всех</option>
            </optgroup>
            <optgroup label="Выключить">
              @foreach($shipping_methods as $shipping_method)
                <option value="toggle|{{ $shipping_method->code }}|0">Выключить "{{ $shipping_method->name }}"</option>
              @endforeach
              <option value="toggle|{{ implode(',', $shipping_methods->pluck('code')->toArray()) }}|0">Выключить для всех</option>
            </optgroup>
          </select>
        </div>
      </div>
      <div class="p-1 w-full lg:w-2/3 flex justify-end">
        <button class="button button-success" id="actioncell_submit" form="action">Применить</button>
      </div>
    </div>

    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
        <thead>
        <tr>
          <th class="bg-gray-100 border p-2" style="width: 2%">
            <input type="checkbox" class="action" id="check_all">
          </th>
          <th class="bg-gray-100 border p-2" style="width: 5%">#</th>
          <th class="bg-gray-100 border p-2">Страна</th>
          <th class="bg-gray-100 border p-2">Код страны</th>
          <th class="bg-gray-100 border p-2">Статус</th>
        </tr>
        </thead>
        <tbody>
        @foreach($countries as $country)
          <tr>
            <td class="border p-2">
              <input type="checkbox" name="country_ids[]" form="action" value="{{ $country->id }}" class="action" id="checkbox_{{ $country->id }}">
            </td>
            <td class="border p-2">
              {{ $country->id }}
            </td>
            <td class="border p-2">
              {{ $country->name }}
            </td>
            <td class="border p-2">
              {{ $country->options['pochta_code'] ?? '' }}
            </td>
            <td class="border p-2">
              @foreach($shipping_methods as $shipping_method)
                @if(isset($country->options['status'])&&in_array($shipping_method->code, $country->options['status']))
                  <span class="badge badge-green">{{ $shipping_method->name }}</span>
                @else
                  <span class="badge badge-red">{{ $shipping_method->name }}</span>
                @endif
              @endforeach
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    <div class="p-2">
      {{ $countries->appends(request()->input())->links('pagination::tailwind') }}
    </div>
  </div>
  <form action="{{ route('admin.shipping.countries.updateCounties') }}" id="action" method="POST">
    @csrf
    @method('PUT')
  </form>
</x-admin-layout>


