@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
      <form action="{{ route('admin.blog.articles.destroy', $article->slug) }}" id="delete-mailing_list-{{ $article->id }}" method="POST">
        @csrf
        @method('DELETE')
      </form>
      <a href="#" class="button button-light-secondary" onclick="if(confirm('Удалить публикацию навсегда?'))document.getElementById('delete-mailing_list-{{ $article->id }}').submit();">Удалить</a>
  </x-slot>
  <x-slot name="script">
    <script src="{{ asset('libraries/ace-builds-master/src-min/ace.js') }}"></script>
{{--    <script src="{{ asset('libraries/ace-master/mode/html.js') }}"></script>--}}
{{--    <script src="{{ asset('libraries/ace-master/src/theme/monokai.js') }}"></script>--}}
    <script>
      window.filemanger.working_dir = @json($working_dir);
    </script>
    <style>
      .ace-editor-area {
        width: 100%;
        height: calc(100vh - 200px);
      }
    </style>
{{--    <script>--}}
{{--      var editor = ace.edit("editor");--}}
{{--      editor.setTheme("ace/theme/monokai");--}}
{{--      editor.session.setMode("ace/mode/javascript");--}}
{{--    </script>--}}
  </x-slot>
  <form action="{{ route('admin.blog.articles.update', $article->slug) }}" method="post">
    @csrf
    @method('PUT')
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
                  id="tab-2" aria-selected="false" role="tab" aria-controls="tab-2-content">Контент
          </button>
        </nav>
      </div>
    </div>

    <div class="mx-auto px-2 sm:px-3 lg:px-4 py-6" id="tab-content">
      <div id="tab-1-content" role="tabpanel">
        <div class="sm:w-[50%]">
          <div class="form-group">
            <x-input-label for="article-title" :value="__('Наименование')" />
            <x-textarea name="title" id="article-title" class="mt-1 block w-full" required>{{ old('title') ?? $article->title }}</x-textarea>
{{--            <x-text-input type="text" name="title" id="article-title" value="{{ old('title') ?? $article->title }}" class="mt-1 block w-full" required />--}}
          </div>
          <div class="form-group">
            <x-input-label for="blog_category_id" :value="__('Раздел')"/>
            <select id="blog_category_id" name="blog_category_id" class="form-control w-full">
              <option>Выбрать</option>
              @foreach($categories as $category)
                <option value="{{ $category->id }}" @if($article->blog_category_id == $category->id) selected @endif>{{ $category->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <x-input-label for="status" :value="__('Статус')"/>
            <select id="status" name="status" class="form-control w-full">
              <option value="0" @if(old('status') == 0 || $article->status == 0) selected @endif>Скрыта</option>
              <option value="1" @if(old('status') == 1 || $article->status == 1) selected @endif>Активна</option>
            </select>
          </div>
          <div class="form-group">

            <x-input-label for="image" class="mb-2" :value="__('Изображение заголовка')" />

            <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
              <div id="lfm-preview-image" class="rounded-md overflow-hidden w-20" data-name="data_title[image]">
                <input type="hidden" name="data_title[image][maxWidth]" id="input-lfm-image" value="1280">
                @if(isset($article->data_title['image']['img']))
                  <input type="hidden" name="data_title[image][img]" id="input-lfm-image" value="{{ old('data')['image']['img'] ?? $article->data_title['image']['img'] ?? '' }}">
                  <input type="hidden" name="data_title[image][thumb]" id="input-lfm-thumb-image" value="{{ old('data')['image']['thumb'] ?? $article->data_title['image']['thumb'] ?? '' }}">

                  <a href="javascript:;" data-fancybox="true" data-src="{{ $article->data_title['image']['img'] }}" style="display: block;">
                    <img src="{{ $article->data_title['image']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
                  </a>
                @endif
              </div>
              <div class="mb-2">
                <div class="mb-2 text-gray-500 text-sm"></div>
                <button
                  type="button"
                  id="image"
                  class="button button-secondary"
                  data-lfm="image"
                  data-preview="lfm-preview-image">Выбрать изображение</button>
              </div>

            </div>
          </div>
          <div class="form-group">
            <x-input-label for="data_title-short_description" :value="__('Короткое описание')" />
            <x-textarea name="data_title[short_description]" id="data_title-short_description" class="mt-1 block w-full tinymce-textarea">{{ $article->data_title['short_description'] ?? '' }}</x-textarea>
          </div>
          <div class="form-group">
            <x-input-label for="article-products-title" :value="__('Заголовок для товаров')" />
            <x-text-input type="text" name="data_content[products-title]" id="article-products-title" value="{{ old('data_content')['products-title'] ?? (isset($article->data_content['products-title'])&&is_string($article->data_content['products-title']) ? $article->data_content['products-title'] : '') }}" class="mt-1 block w-full" />
          </div>
          <div class="form-group">
            <x-input-label for="data_content-products" :value="__('Связанные товары')"/>
            <select name="data_content[products][]" id="data_content-products" multiple class="multipleSelect form-control">
              @foreach($products as $product)
                <option value="{{ $product->id }}" data-keywords="{{ $product->category_title }}" @if(isset($article->data_content['products'])&&in_array($product->id, $article->data_content['products'])){!! 'selected' !!}@endif>{{ $product->id }}: {{ $product->name }} ({{ $product->sku }}, Категория "{{ $product->category_title }}")</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
      <div id="tab-2-content" role="tabpanel">
        @if(isset($article->data_content['_request']))
          <div class="my-4">
            <div class="badge badge-orange">Последние изменения в процессе сохранения</div>
          </div>
        @endif
        <div class="carousel-contructor mb-5">
          <div class="flex justify-between items-end">
            <x-input-label for="articleTextAddSlide" class="mb-2" :value="__('Текст')" />
            <button type="button" id="articleTextAddSlide" class="addSlide button button-success button-sm mb-2" data-id="articleContent" data-field="data_content" data-donor-id="articleText_donor">Добавить блок текста</button>
          </div>
          <div class="flex justify-between items-end">
            <x-input-label for="articleContentAddSlide" class="mb-2" :value="__('Свободный код')" />
            <button type="button" id="articleContentAddSlide" class="addSlide button button-success button-sm mb-2" data-id="articleContent" data-field="data_content">Добавить блок кода</button>
          </div>
          <div class="flex justify-between items-end">
            <x-input-label for="articleImageAddSlide" class="mb-2" :value="__('Изображения')" />
            <button type="button" id="articleImageAddSlide" class="addSlide button button-success button-sm mb-2" data-id="articleContent" data-field="data_content" data-donor-id="articleImage_donor">Добавить изображение</button>
          </div>
          <div class="flex justify-between items-end">
            <x-input-label for="articleVideoAddSlide" class="mb-2" :value="__('Видео')" />
            <button type="button" id="articleVideoAddSlide" class="addSlide button button-success button-sm mb-2" data-id="articleContent" data-field="data_content" data-donor-id="articleVideo_donor">Добавить видео</button>
          </div>
          <div id="articleContent_donor" style="display: none;">
            <x-admin.content.home-slider-1>
              <x-text-input type="hidden" data-name="content" disabled/>
              <div class="ace-editor-area"></div>
            </x-admin.content.home-slider-1>
          </div>
          <div id="articleText_donor" style="display: none;">
            <x-admin.content.home-slider-1>
              <div class="form-group">
                <x-input-label :value="__('Текст')" />
                <x-textarea data-name="text" class="mt-1 block w-full tinymce-textarea" data-field="data_content" disabled></x-textarea>
              </div>
            </x-admin.content.home-slider-1>
          </div>
          <div id="articleImage_donor" style="display: none;">
            <x-admin.content.home-slider-1>
              <div class="slideImage">
                <x-input-label class="image-label mb-2" :value="__('Изображение')" />
                <div class="form-group">
                  <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
                    <div class="lfm-preview flex flex-wrap"></div>
                    <div class="mb-2">
                      <div class="mb-2 text-gray-500 text-sm"></div>
                      <button
                        type="button"
                        id="articleImage"
                        class="button button-secondary"
                        data-lfm="image">Выбрать изображение</button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <x-input-label :value="__('Классы для контейнера')" />
                <x-text-input type="text" data-name="class" class="mt-1 block w-full" disabled/>
              </div>
            </x-admin.content.home-slider-1>
          </div>
          <div id="articleVideo_donor" style="display: none;">
            <x-admin.content.home-slider-1>
{{--              <div class="slideImage">--}}
{{--                <x-input-label class="image-label mb-2" :value="__('Обложка')" />--}}
{{--                <div class="form-group">--}}
{{--                  <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">--}}
{{--                    <div class="lfm-preview flex flex-wrap"></div>--}}
{{--                    <div class="mb-2">--}}
{{--                      <div class="mb-2 text-gray-500 text-sm"></div>--}}
{{--                      <button--}}
{{--                        type="button"--}}
{{--                        id="articleVideo"--}}
{{--                        class="button button-secondary"--}}
{{--                        data-lfm="image">Выбрать изображение</button>--}}
{{--                    </div>--}}
{{--                  </div>--}}
{{--                </div>--}}
{{--              </div>--}}
              <div class="form-group">
                <x-input-label :value="__('Идентификатор видео (youtube)')" />
                <x-text-input type="text" data-name="youtube" class="mt-1 block w-full" disabled/>
              </div>
              <div class="form-group">
                <x-input-label :value="__('Идентификатор видео (rutube)')" />
                <x-text-input type="text" data-name="rutube" class="mt-1 block w-full" disabled/>
              </div>
              <div class="form-group">
                <x-input-label :value="__('Классы для контейнера')" />
                <x-text-input type="text" data-name="class" class="mt-1 block w-full" disabled/>
              </div>
            </x-admin.content.home-slider-1>
          </div>
          <div class="carousel space-y-3 mb-1 p-2 border-gray-300 rounded-md border">
            @if(isset($article->data_content['articleContent']))
              @foreach($article->data_content['articleContent'] as $index => $slide)
                <x-admin.content.home-slider-1>
                  @if(isset($slide['youtube']))
{{--                    <div class="slideImage">--}}
{{--                      <x-input-label class="image-label mb-2" :value="__('Изображение')" />--}}
{{--                      <div class="form-group">--}}
{{--                        <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">--}}
{{--                          <div id="lfm-preview-articleContent-{{ $index }}" class="lfm-preview flex flex-wrap" data-name="data_content[articleContent][{{ $index }}][image]">--}}
{{--                            @if (isset($slide['image']['img'])&&!empty($slide['image']['img']))--}}
{{--                              <input type="hidden" name="data_content[articleContent][{{ $index }}][image][img]" data-name="img" value="{{ $slide['image']['img'] }}">--}}
{{--                              <input type="hidden" name="data_content[articleContent][{{ $index }}][image][thumb]" data-name="thumb" value="{{ $slide['image']['thumb'] }}">--}}
{{--                              <a href="javascript:;" data-fancybox="true" data-src="{{ $slide['image']['img'] }}" class="block rounded-md overflow-hidden w-20 m-2">--}}
{{--                                <img src="{{ $slide['image']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">--}}
{{--                              </a>--}}
{{--                            @elseif(isset($slide['image'])&&is_array($slide['image']))--}}
{{--                              @foreach($slide['image'] as $key => $image)--}}
{{--                                <input type="hidden" name="data_content[articleContent][{{ $index }}][image][{{ $key }}][img]" data-name="img" value="{{ $image['img'] }}">--}}
{{--                                <input type="hidden" name="data_content[articleContent][{{ $index }}][image][{{ $key }}][thumb]" data-name="thumb" value="{{ $image['thumb'] }}">--}}
{{--                                <a href="javascript:;" data-fancybox="true" data-src="{{ $image['img'] }}" class="block rounded-md overflow-hidden w-20 m-2">--}}
{{--                                  <img src="{{ $image['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">--}}
{{--                                </a>--}}
{{--                              @endforeach--}}
{{--                            @endif--}}
{{--                          </div>--}}
{{--                          <div class="mb-2">--}}
{{--                            <div class="mb-2 text-gray-500 text-sm"></div>--}}
{{--                            <button--}}
{{--                              type="button"--}}
{{--                              id="articleContent"--}}
{{--                              class="button button-secondary"--}}
{{--                              data-lfm="image"--}}
{{--                              data-preview="lfm-preview-articleContent-{{ $index }}">Выбрать изображение</button>--}}
{{--                          </div>--}}
{{--                        </div>--}}
{{--                      </div>--}}
{{--                    </div>--}}
                    <div class="form-group">
                      <x-input-label for="data_content-articleContent-{{ $index }}-youtube" :value="__('Идентификатор видео (youtube)')" />
                      <x-text-input type="text" data-name="youtube" class="mt-1 block w-full" name="data_content[articleContent][{{ $index }}][youtube]" id="data_content-articleContent-{{ $index }}-youtube" value="{{ $slide['youtube'] ?? '' }}"/>
                    </div>
                    <div class="form-group">
                      <x-input-label for="data_content-articleContent-{{ $index }}-rutube" :value="__('Идентификатор видео (rutube)')" />
                      <x-text-input type="text" data-name="rutube" class="mt-1 block w-full" name="data_content[articleContent][{{ $index }}][rutube]" id="data_content-articleContent-{{ $index }}-rutube" value="{{ $slide['rutube'] ?? '' }}"/>
                    </div>
                    <div class="form-group">
                      <x-input-label for="data_content-articleContent-{{ $index }}-class" :value="__('Классы для контейнера')" />
                      <x-text-input type="text" data-name="class" class="mt-1 block w-full" name="data_content[articleContent][{{ $index }}][class]" id="data_content-articleContent-{{ $index }}-class" value="{{ $slide['class'] ?? '' }}"/>
                    </div>
                  @elseif(isset($slide['image']))
                    <div class="slideImage">
                      <x-input-label class="image-label mb-2" :value="__('Изображение')" />
                      <div class="form-group">
                        <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
                          <div id="lfm-preview-articleContent-{{ $index }}" class="lfm-preview flex flex-wrap" data-name="data_content[articleContent][{{ $index }}][image]">
                            @if (isset($slide['image']['img'])&&!empty($slide['image']['img']))
                              <input type="hidden" name="data_content[articleContent][{{ $index }}][image][img]" data-name="img" value="{{ $slide['image']['img'] }}">
                              <input type="hidden" name="data_content[articleContent][{{ $index }}][image][thumb]" data-name="thumb" value="{{ $slide['image']['thumb'] }}">
                              <a href="javascript:;" data-fancybox="true" data-src="{{ $slide['image']['img'] }}" class="block rounded-md overflow-hidden w-20 m-2">
                                <img src="{{ $slide['image']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
                              </a>
                            @elseif(isset($slide['image'])&&is_array($slide['image']))
                              @foreach($slide['image'] as $key => $image)
                                <input type="hidden" name="data_content[articleContent][{{ $index }}][image][{{ $key }}][img]" data-name="img" value="{{ $image['img'] }}">
                                <input type="hidden" name="data_content[articleContent][{{ $index }}][image][{{ $key }}][thumb]" data-name="thumb" value="{{ $image['thumb'] }}">
                                <a href="javascript:;" data-fancybox="true" data-src="{{ $image['img'] }}" class="block rounded-md overflow-hidden w-20 m-2">
                                  <img src="{{ $image['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
                                </a>
                              @endforeach
                            @endif
                          </div>
                          <div class="mb-2">
                            <div class="mb-2 text-gray-500 text-sm"></div>
                            <button
                              type="button"
                              id="articleContent"
                              class="button button-secondary"
                              data-lfm="image"
                              data-preview="lfm-preview-articleContent-{{ $index }}">Выбрать изображение</button>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <x-input-label for="data_content-articleContent-{{ $index }}-class" :value="__('Классы для контейнера')" />
                      <x-text-input type="text" data-name="class" class="mt-1 block w-full" name="data_content[articleContent][{{ $index }}][class]" id="data_content-articleContent-{{ $index }}-class" value="{{ $slide['class'] ?? '' }}"/>
                    </div>
                  @elseif(isset($slide['content']))
                    <x-text-input type="hidden" name="data_content[articleContent][{{ $index }}][content]" data-name="content" value="{{ $slide['content'] }}"/>
                    <div class="ace-editor-area" id="data_content-articleContent-{{ $index }}-content">{{ $slide['content'] }}</div>
                  @elseif(isset($slide['text']))
                    <div class="form-group">
                      <x-input-label for="data_content-articleContent-{{ $index }}-text" :value="__('Текст')" />
                      <x-textarea name="data_content[articleContent][{{ $index }}][text]" id="data_content-articleContent-{{ $index }}-text" data-name="text" class="mt-1 block w-full tinymce-textarea">{{ $slide['text'] }}</x-textarea>
                    </div>
                  @endif
                </x-admin.content.home-slider-1>
              @endforeach
            @endif
          </div>
        </div>
      </div>
    </div>
    <div class="px-3 sm:px-4 lg:px-6 py-6 flex justify-end">
      <x-primary-button>Сохранить</x-primary-button>
    </div>
  </form>
</x-admin-layout>
