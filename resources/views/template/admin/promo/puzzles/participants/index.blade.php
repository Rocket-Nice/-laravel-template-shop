@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="title">
    {{ $seo['title'] ?? false }}
  </x-slot>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <x-admin.search-form :route="url()->current()">
    <div class="p-1 w-full">
      <div class="form-group">
        <x-input-label for="keyword" :value="__('Email пользователя')" />
        <x-text-input type="text" name="keyword" id="keyword" value="{{ request()->get('keyword') }}" class="mt-1 block w-full" />
      </div>
    </div>

    <div class="p-1 w-full">
      <div class="form-group">
        <div class="flex items-center">
          <x-checkbox id="only_correct" name="only_correct" value="1"
                      :checked="request()->only_correct ? true : false"/>
          <x-input-label for="only_correct" class="ml-2" :value="__('Только собранные пазлы')"/>
        </div>
      </div>
    </div>
    <div class="p-1 w-full">
      <div class="form-group">
        <div class="flex items-center">
          <x-checkbox id="only_with_prize" name="only_with_prize" value="1"
                      :checked="request()->only_with_prize ? true : false"/>
          <x-input-label for="only_with_prize" class="ml-2" :value="__('Только с подарками')"/>
        </div>
      </div>
    </div>
  </x-admin.search-form>
  <div class="mx-auto py-4">
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
        <thead>
        <tr>
          <th class="bg-gray-100 border p-2"></th>
          <th class="bg-gray-100 border p-2">Пользователь</th>
          <th class="bg-gray-100 border p-2">Дата</th>
          <th class="bg-gray-100 border p-2">Результат</th>
          <th class="bg-gray-100 border p-2">Подарок</th>
          <th class="bg-gray-100 border p-2"></th>
        </tr>
        </thead>
        <tbody>
        @foreach($puzzleImages as $puzzleImage)
          <tr>
            <td class="border p-2" style="width: 10%">
              <a href="{{ storageToAsset($puzzleImage->image_path) }}" data-fancybox="comment-{{ $puzzleImage->id }}" class="image inline-block rounded border border-myGray">
                <img src="{{ storageToAsset($puzzleImage->thumb_path) }}" alt="" class="block w-[100px] h-[100px] rounded" style="min-width: 100px;">
              </a>
            </td>
            <td class="border p-2">{{ $puzzleImage->user->email }}<br/>{{ $puzzleImage->user->name }}</td>
            <td class="border p-2">{{ \Carbon\Carbon::parse($puzzleImage->created_at)->format('d.m.Y H:i:s') }}</td>
            <td class="border p-2">
              {!! !$puzzleImage->is_correct ? '<span class="badge-red text-xs">Не собран</span>' : '<span class="badge-green text-xs">Собран</span>' !!}<br/>
              {{ $puzzleImage->result_message }}</td>
            <td class="border p-2">
              @if($puzzleImage->is_correct)
              {{ $puzzleImage->prize['name'] ?? '' }}
              @endif
            </td>
            <td class="border p-2">
              <form action="{{ route('admin.puzzle_participants.update', $puzzleImage->id) }}" id="delete-page-{{ $puzzleImage->id }}" method="POST">
                @csrf
                @method('PUT')
                @if($puzzleImage->is_correct)
                  <input type="hidden" name="is_correct" value="0">
                @else
                  <input type="hidden" name="is_correct" value="1">
                @endif
              </form>
              <x-dropdown_menu>
                <x-slot name="content">
                  <div class="py-1" role="none">
                    @if($puzzleImage->is_correct)
                      <a href="#" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1" onclick="if(confirm('Данный пазл не собран?'))document.getElementById('delete-page-{{ $puzzleImage->id }}').submit();">Пазл не собран</a>
                    @else
                      <a href="#" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1" onclick="if(confirm('Данный пазл собран?'))document.getElementById('delete-page-{{ $puzzleImage->id }}').submit();">Пазл собран</a>
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
  </div>
</x-admin-layout>


