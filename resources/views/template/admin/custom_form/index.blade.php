@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
      <a href="{{ route('admin.custom-forms.create') }}" class="button button-success">Добавить форму</a>
    @endif
  </x-slot>
  <div class="mx-auto py-4">
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
        <thead>
        <tr>
          <th class="bg-gray-100 border p-2" style="width: 5%">#</th>
          <th class="bg-gray-100 border p-2">Наименование</th>
          <th class="bg-gray-100 border p-2">Количество вопросов</th>
          <th class="bg-gray-100 border p-2" style="width:120px"></th>
        </tr>
        </thead>
        <tbody>
        @foreach($forms as $form)
          <tr>
            <td class="border p-2">{{ $form->id }}</td>
            <td class="border p-2">{{ $form->name }}</td>
            <td class="border p-2">{{ $form->fields()->count() }}</td>
            <td class="border p-2">
              <form action="{{ route('admin.custom-forms.destroy', $form->slug) }}" id="delete-mailing_list-{{ $form->slug }}" method="POST">
                @csrf
                @method('DELETE')
              </form>
              <x-dropdown_menu>
                <x-slot name="content">
                  <div class="py-1" role="none">
                    <a href="{{ route('admin.custom-forms.edit', $form->slug) }}" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1">Редактировать</a>
                    <a href="#" class="hover:bg-gray-100 text-gray-700 block px-4 py-2 text-sm no-underline" role="menuitem" tabindex="-1" onclick="if(confirm('Удалить опрос «{{ $form->name }}»?'))document.getElementById('delete-mailing_list-{{ $form->slug }}').submit();">Удалить</a>
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
      {{ $forms->appends(request()->input())->links('pagination::tailwind') }}
    </div>
  </div>
</x-admin-layout>

