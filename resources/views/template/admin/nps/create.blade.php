@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <form action="{{ route('admin.nps.store') }}" method="post">
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
            <x-text-input type="text" name="name" id="name" value="{{ old('name') }}" class="mt-1 block w-full" required />
          </div>
          <div class="form-group">
            <x-input-label for="slug" :value="__('Текст ссылки')" />
            <x-text-input type="text" name="slug" id="slug" value="{{ old('slug') }}" class="mt-1 block w-full" required />
            <span class="hint">Только латиница</span>
          </div>
        </div>
      </div>
      <div id="tab-2-content" role="tabpanel">
        <div class="carousel-contructor mb-5">
          <div class="flex justify-between items-end">
            <x-input-label for="surveyQuestionsAddSlide" class="mb-2" :value="__('Вопросы')" />
            <button type="button" id="surveyQuestionsAddSlide" class="addSlide button button-success button-sm mb-2" data-id="surveyQuestions">Добавить вопрос</button>
          </div>
          <div id="surveyQuestions_donor" style="display: none;">
            <x-admin.content.home-slider-1>
              <div class="form-group">
                <x-input-label :value="__('Текст вопроса')" />
                <x-text-input type="text" data-name="text" class="mt-1 block w-full" disabled/>
              </div>
              <div class="form-group">
                <x-input-label :value="__('Комментарий')" />
                <x-text-input type="text" data-name="comment_text" class="mt-1 block w-full" disabled/>
              </div>
              <div class="form-group flex items-center">
                <x-checkbox data-name="is_hidden" value="1" disabled/>
                <x-input-label class="ml-2" :value="__('Скрыть вопрос')"/>
              </div>
            </x-admin.content.home-slider-1>
          </div>
          <div class="carousel space-y-3 mb-1 p-2 border-gray-300 rounded-md border">
{{--            @if(isset($content->carousel_data['surveyQuestions']))--}}
{{--              @foreach($content->carousel_data['surveyQuestions'] as $index => $slide)--}}
{{--                <x-admin.content.home-slider-1>--}}
{{--                  <div class="slideImage">--}}
{{--                    <x-input-label class="image-label mb-2" :value="__('Изображение')" />--}}

{{--                    <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">--}}
{{--                      <div id="lfm-preview-surveyQuestions-{{ $index }}" class="lfm-preview flex flex-wrap" data-name="carousel_data[surveyQuestions][{{ $index }}][image]">--}}
{{--                        <input type="hidden" name="carousel_data[surveyQuestions][{{ $index }}][image][img]" id="carousel_data-surveyQuestions-{{ $index }}-[image]img" data-name="img" data-parent="image" value="{{ $slide['image']['img'] ?? '' }}">--}}
{{--                        <input type="hidden" name="carousel_data[surveyQuestions][{{ $index }}][image][thumb]" id="carousel_data-surveyQuestions-{{ $index }}-[image]thumb" data-name="thumb" data-parent="image" value="{{ $slide['image']['thumb'] ?? '' }}">--}}

{{--                        <a href="javascript:;" data-fancybox="true" data-src="{{ $slide['image']['img'] ?? '' }}" style="display: block;">--}}
{{--                          <img src="{{ $slide['image']['thumb'] ?? '' }}" class="overflow-hidden max-w-full object-cover object-center">--}}
{{--                        </a>--}}
{{--                      </div>--}}
{{--                      <div class="mb-2">--}}
{{--                        <div class="mb-2 text-gray-500 text-sm">546x658px</div>--}}
{{--                        <button--}}
{{--                          type="button"--}}
{{--                          id="surveyQuestions"--}}
{{--                          class="button button-secondary"--}}
{{--                          data-lfm="image"--}}
{{--                          data-preview="lfm-preview-surveyQuestions-{{ $index }}">Выбрать изображение</button>--}}
{{--                      </div>--}}
{{--                    </div>--}}
{{--                  </div>--}}
{{--                  <div class="form-group">--}}
{{--                    <x-input-label :value="__('ID слайда')" />--}}
{{--                    <x-text-input type="text" name="carousel_data[surveyQuestions][{{ $index }}][slide_id]" id="carousel_data-surveyQuestions-{{ $index }}-slide_id" data-name="slide_id" class="mt-1 block w-full" value="{{ $slide['slide_id'] ?? '' }}"/>--}}
{{--                    <div class="hint">Скрытое системное поле</div>--}}
{{--                  </div>--}}
{{--                  <div class="form-group">--}}
{{--                    <x-input-label :value="__('Ссылка')" />--}}
{{--                    <x-text-input type="text" name="carousel_data[surveyQuestions][{{ $index }}][link]" id="carousel_data-surveyQuestions-{{ $index }}-link" data-name="link" class="mt-1 block w-full" value="{{ $slide['link'] }}"/>--}}
{{--                  </div>--}}
{{--                </x-admin.content.home-slider-1>--}}
{{--              @endforeach--}}
{{--            @endif--}}
          </div>
        </div>
      </div>
    </div>
    <div class="px-3 sm:px-4 lg:px-6 py-6 flex justify-end">
      <x-primary-button>Сохранить</x-primary-button>
    </div>
  </form>
</x-admin-layout>


