@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <form action="{{ route('admin.custom-forms.update', $form->slug) }}" method="post">
    @method('PUT')
    @csrf
    <div class="border-b">
      <div class="mx-auto px-2 sm:px-3 lg:px-4">
        <nav class="-mb-px flex flex-col sm:flex-row sm:justify-start space-y-2 sm:space-y-0 sm:space-x-4"
             aria-label="Tabs" role="tablist">
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-1" aria-selected="true" role="tab" aria-controls="tab-1-content">Общие данные
          </button>
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-2" aria-selected="true" role="tab" aria-controls="tab-2-content">Вопросы
          </button>
        </nav>
      </div>
    </div>

    <div class="mx-auto px-2 sm:px-3 lg:px-4 py-6" id="tab-content">
      <div id="tab-1-content" role="tabpanel">
        <div class="sm:w-[50%]">
          <div class="form-group">
            <x-input-label for="name" :value="__('Наименование')" />
            <x-text-input type="text" name="name" id="name" value="{{ old('name') ?? $form->name }}" class="mt-1 block w-full" required />
          </div>
          <div class="form-group">
            <x-input-label for="slug" :value="__('Текст ссылки')" />
            <x-text-input type="text" name="slug" id="slug" value="{{ old('slug') ?? $form->slug }}" class="mt-1 block w-full" required />
            <span class="hint">Только латиница</span>
          </div>
        </div>
      </div>
      <div id="tab-2-content" role="tabpanel">
        <div class="carousel-contructor mb-5">
          <div class="flex justify-between items-end">
            <x-input-label for="formQuestionsAddSlide" class="mb-2" :value="__('Вопросы')" />
            <button type="button" id="formQuestionsAddSlide" class="addSlide button button-success button-sm mb-2" data-id="formQuestions">Добавить вопрос</button>
          </div>
          <div id="formQuestions_donor" style="display: none;">
            <x-admin.content.home-slider-1>
              <div class="form-group">
                <x-input-label :value="__('Текст вопроса')" />
                <x-text-input type="text" data-name="key" class="mt-1 block w-full" disabled/>
              </div>
              <div class="form-group flex items-center">
                <x-checkbox data-name="is_hidden" value="1" disabled/>
                <x-input-label class="ml-2" :value="__('Скрыть вопрос')"/>
              </div>
            </x-admin.content.home-slider-1>
          </div>
          <div class="carousel space-y-3 mb-1 p-2 border-gray-300 rounded-md border">
            @foreach($form->fields as $field)
              <x-admin.content.home-slider-1>
                <x-text-input type="hidden" data-name="id" class="mt-1 block w-full" name="carousel_data[formQuestions][{{ $field->order }}][id]" value="{{ $field->id }}"/>
                <div class="form-group">
                  <x-input-label :value="__('Текст вопроса')" />
                  <x-text-input type="text" name="carousel_data[formQuestions][{{ $field->order }}][key]" id="carousel_data-formQuestions-{{ $field->order }}-key" data-name="key" class="mt-1 block w-full" value="{{ $field->key }}"/>
                  <div class="hint">Скрытое системное поле</div>
                </div>
                <div class="form-group flex items-center">
                  <x-checkbox name="carousel_data[formQuestions][{{ $field->order }}][is_hidden]" id="carousel_data-formQuestions-{{ $field->order }}-is_hidden" data-name="is_hidden" value="1"  :checked="($field->is_hidden ?? false) ? true : false" />
                  <x-input-label class="ml-2" :value="__('Скрыть вопрос')"/>
                </div>
              </x-admin.content.home-slider-1>
            @endforeach
          </div>
        </div>
      </div>
    </div>
    <div class="px-3 sm:px-4 lg:px-6 py-6 flex justify-end">
      <x-primary-button>Сохранить</x-primary-button>
    </div>
  </form>
</x-admin-layout>


