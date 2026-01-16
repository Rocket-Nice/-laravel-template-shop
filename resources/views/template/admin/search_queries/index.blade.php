<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <x-search-form :route="url()->current()">
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="date_from" :value="__('Дата от')"/>
        <x-text-input type="text" name="date_from" id="date_from" value="{{ request()->get('date_from') }}" data-minDate="false" placeholder="{{ now()->startOfMonth()->format('d.m.Y H:i') }}" class="mt-1 block w-full datepicker"/>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="date_until" :value="__('Дата до')"/>
        <x-text-input type="text" name="date_until" id="date_until" value="{{ request()->get('date_until') }}" placeholder="{{ now()->format('d.m.Y H:i') }}" data-minDate="false" class="mt-1 block w-full datepicker"/>
      </div>
    </div>
    <div class="p-1 w-full">
      <div class="form-group">
        <x-input-label for="query" :value="__('запрос')"/>
        <x-text-input type="text" name="query" id="query" value="{{ request()->get('query') }}"
                      class="mt-1 block w-full"/>
      </div>
    </div>
  </x-search-form>
  <div class="mx-auto py-4">
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
        <thead>
        <tr>
          <th class="bg-gray-100 border p-2">Запрос</th>
          <th class="bg-gray-100 border p-2">IP-адрес</th>
          <th class="bg-gray-100 border p-2">Дата</th>
        </tr>
        </thead>
        <tbody>
        @foreach($searchQueries as $searchQuery)
          <tr>
            <td class="border p-2">
              {{ $searchQuery->query }}
            </td>
            <td class="border p-2">
              {{ $searchQuery->ip }}
            </td>
            <td class="border p-2">
              {{ \Carbon\Carbon::create($searchQuery->created_at)->format('d.m.Y H:i:s') }}
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    <div class="p-2">
      {{ $searchQueries->appends(request()->input())->links('pagination::tailwind') }}
    </div>
  </div>
</x-admin-layout>
