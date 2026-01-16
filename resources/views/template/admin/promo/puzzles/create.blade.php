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
  <form action="{{ route('admin.puzzles.store') }}" method="post">
    @csrf
    <div class="mx-auto px-3 sm:px-4 lg:px-6 py-6">
      <div class="sm:w-[75%]">
        <div class="form-group">
          <x-input-label for="code" :value="__('Код купона')"/>
          <x-text-input type="text" name="code" id="code" value="{{ old('code') ?? getCode(6) }}" class="mt-1 block w-full" required/>
        </div>
        <div class="form-group">
          <x-input-label for="name" :value="__('Наименование подарка')"/>
          <x-text-input type="text" name="name" id="name" value="{{ old('name') }}" class="mt-1 block w-full" required/>
        </div>
        <div class="form-group">
          <x-input-label for="order" :value="__('Порядковый номер')"/>
          <x-text-input type="text" name="order" id="order" value="{{ old('order') }}" class="mt-1 block w-full" required/>
        </div>
      </div>
    </div>
    <div class="px-3 sm:px-4 lg:px-6 py-6 flex justify-end">
      <button class="button button-success">Сохранить</button>
    </div>
  </form>
</x-admin-layout>


