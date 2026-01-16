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
  <form action="{{ route('admin.pages.update', $page->slug) }}" method="post">
    @csrf
    @method('PUT')
    <div class="mx-auto px-3 sm:px-4 lg:px-6 py-6">
      <div class="sm:w-[75%]">
        <div class="form-group">
          <x-input-label for="title" :value="__('Наименование страницы')"/>
          <x-text-input type="text" name="title" id="title" value="{{ old('title') ?? $page->title }}" class="mt-1 block w-full" required/>
        </div>
        <div class="form-group">
          <x-input-label for="content" :value="__('Описание')" />
          <x-textarea name="content" id="content" class="mt-1 block w-full tinymce-textarea">{{ old('content') ?? $page->content }}</x-textarea>
        </div>
      </div>
    </div>
    <div class="px-3 sm:px-4 lg:px-6 py-6 flex justify-end">
      <button class="button button-success">Сохранить</button>
    </div>
  </form>
</x-admin-layout>

