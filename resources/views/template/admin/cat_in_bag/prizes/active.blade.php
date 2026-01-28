@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>

  <x-admin.search-form :route="route('admin.cat-in-bag.prizes.active')">
    <div class="p-1 w-full">
      <div class="form-group">
        <x-input-label for="keyword" :value="__('Наименование')" />
        <x-text-input type="text" name="keyword" id="keyword" value="{{ request()->get('keyword') }}" class="mt-1 block w-full" />
      </div>
    </div>
  </x-admin.search-form>

  <div class="mx-auto py-4">
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
        <thead>
        <tr>
          <th class="bg-gray-100 border p-2 text-sm" style="width: 5%;">#</th>
          <th class="bg-gray-100 border p-2">Наименование</th>
          <th class="bg-gray-100 border p-2">Товар</th>
          <th class="bg-gray-100 border p-2">Разыграно</th>
          <th class="bg-gray-100 border p-2">Разыгрываются</th>
          <th class="bg-gray-100 border p-2">Всего</th>
        </tr>
        </thead>
        <tbody>
        @foreach($prizes as $prize)
          <tr>
            <td class="border p-2">{{ $prize->id }}</td>
            <td class="border p-2" style="max-width:220px">
              {{ $prize->name }}
            </td>
            <td class="border p-2" style="max-width:220px">
              {{ $prize->product?->name ?? '—' }}
            </td>
            <td class="border p-2">{{ $prize->used_qty }}</td>
            <td class="border p-2">{{ $prize->available_qty }}</td>
            <td class="border p-2">{{ $prize->total_qty }}</td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    <div class="p-2">
      {{ $prizes->appends(request()->input())->links('pagination::tailwind') }}
    </div>
  </div>
</x-admin-layout>
