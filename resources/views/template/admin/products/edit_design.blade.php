<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <x-slot name="style">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
      href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Cormorant+Infant:wght@400;600&family=Montserrat:wght@400;500&family=Playfair+Display:ital@1&display=swap"
      rel="stylesheet">
  </x-slot>
  <x-slot name="script">
    <script>
      window.filemanger.working_dir = @json($working_dir);
    </script>
  </x-slot>

  @php($design_data = $product->style_page)
  @if($design)
    @php($design_data = $design->style_page)
  @endif
  <form action="{{ route('admin.products.updateDesign', $product->slug) }}" method="post">
    @csrf
    @method('PUT')
    <div class="border-b">
      <div class="mx-auto px-2 sm:px-3 lg:px-4">
        <nav class="-mb-px flex flex-col sm:flex-row sm:justify-start space-y-2 sm:space-y-0 sm:space-x-4"
             aria-label="Tabs" role="tablist">
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-1" aria-selected="true" role="tab" aria-controls="tab-1-content">Карточки товара
          </button>
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-2" aria-selected="true" role="tab" aria-controls="tab-2-content">Текст на странице
          </button>
          <a href="{{ route('admin.products.edit', $product->slug) }}"
             class="whitespace-nowrap no-underline py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500">Редактировать
            товар <i class="fas fa-external-link-alt"></i></a>
        </nav>
      </div>
    </div>

    <div class="mx-auto px-2 sm:px-3 lg:px-4 py-6" id="tab-content">
      <div id="tab-1-content" role="tabpanel">
        <div class="sm:w-[75%]">
          @if(isset($product->style_cards['_request']))
            <div class="my-4">
              <div class="badge badge-orange">Последние изменения в процессе сохранения</div>
            </div>
          @endif
          <div class="mb-6">
            <a href="javascript:;" data-fancybox data-src="#copyProductCards" class="button button-primary">Загрузить
              существующие карточки</a>
          </div>
{{--            <div class="form-group">--}}
{{--              <x-input-label for="text_data-subtitle" :value="__('Подзаголовок')" />--}}
{{--              <x-text-input type="text" name="style_page[subtitle]" id="text_data-subtitle" value="{{ old('style_page')['subtitle'] ?? $design_data['subtitle'] ?? '' }}" class="mt-1 block w-full"/>--}}
{{--            </div>--}}
            <div class="form-group">
              <x-input-label for="text_data-subtitle" :value="__('Подзаголовок')" />
              <x-textarea type="text" name="style_page[subtitle]" id="text_data-subtitle" class="mt-1 block w-full  tinymce-textarea">{{ old('style_page')['subtitle'] ?? $design_data['subtitle'] ?? '' }}</x-textarea>
            </div>
            <div class="form-group">
              <x-input-label for="text_data-subtitle-page" :value="__('Подзаголовок на странице')" />
              <x-textarea type="text" name="style_page[subtitle-page]" id="text_data-subtitle-page" class="mt-1 block w-full  tinymce-textarea">{{ old('style_page')['subtitle-page'] ?? $design_data['subtitle-page'] ?? '' }}</x-textarea>
            </div>
          <div class="form-group">
            <x-input-label for="cardImage" class="mb-2" :value="__('Изображение в каталоге')" />
            <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
              <div id="lfm-preview-cardImage" class="rounded-md overflow-hidden w-20" data-name="style_page[cardImage]">
                <input type="hidden" name="style_page[cardImage][maxWidth]" id="input-lfm-cardImage" value="500">
                @if(isset($design_data['cardImage']['img']))
                  <input type="hidden" name="style_page[cardImage][img]" id="input-lfm-cardImage" value="{{ old('style_page')['cardImage']['img'] ?? $design_data['cardImage']['img'] ?? '' }}">
                  <input type="hidden" name="style_page[cardImage][thumb]" id="input-lfm-thumb-cardImage" value="{{ old('style_page')['cardImage']['thumb'] ?? $design_data['cardImage']['thumb'] ?? '' }}">

                  <a href="javascript:;" data-fancybox="true" data-src="{{ $design_data['cardImage']['img'] }}" style="display: block;">
                    <img src="{{ $design_data['cardImage']['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
                  </a>
                @endif
              </div>
              <div class="mb-2">
                <div class="mb-2 text-gray-500 text-sm"></div>
                <button
                  type="button"
                  id="cardImage"
                  class="button button-secondary"
                  data-lfm="image"
                  data-preview="lfm-preview-cardImage">Выбрать изображение</button>
              </div>
            </div>
          </div>

            <div class="form-group">
              <x-input-label for="text_data-puzzle_color" :value="__('Цвет пазлов')" />
              <x-text-input type="text" name="style_page[puzzle_color]" id="text_data-puzzle_color" value="{{ old('style_page')['puzzle_color'] ?? $design_data['puzzle_color'] ?? '' }}" class="mt-1 block w-full"/>
            </div>
          <div class="form-group">
            <x-input-label for="style_page-cardsDescription" :value="__('Описание в карточке')"/>
            <x-textarea name="style_page[cardsDescription]" id="style_page-cardsDescription"
                        class="mt-1 block w-full tinymce-textarea">{{ old('style_page')['cardsDescription'] ?? $cards->style_page['cardsDescription'] ?? $product->style_page['cardsDescription'] ?? '' }}</x-textarea>
          </div>
          <div class="carousel-contructor mb-5">
            <div class="flex justify-between items-end">
              <x-input-label for="cardsDescriptionIcons" class="mb-2" :value="__('Иконки в описании')"/>
              <button type="button" id="cardsDescriptionIcons" class="addSlide button button-success button-sm mb-2"
                      data-id="cardsDescriptionIcons">Добавить элемент
              </button>
            </div>
            <div id="cardsDescriptionIcons_donor" style="display: none;">
              <x-admin.content.home-slider-1>
                <div class="form-group">
                  <x-input-label :value="__('Иконка')"/>
                  <select data-name="icon" data-field="style_page" class="choisesImgSelect form-control" disabled>
                    @include('_parts.admin.s-icons')
                  </select>
                </div>
                <div class="form-group">
                  <x-input-label :value="__('Текст')"/>
                  <x-text-input type="text" data-name="text" data-field="style_page" class="mt-1 block w-full"
                                disabled/>
                </div>
              </x-admin.content.home-slider-1>
            </div>
            <div class="carousel space-y-3 mb-1 p-2 border-gray-300 rounded-md border">
              @if(isset($cards->style_page['cardsDescriptionIcons']))
                @php($product->style_page['cardsDescriptionIcons'] = $cards->style_page['cardsDescriptionIcons'])
              @endif
              @if(isset($product->style_page['cardsDescriptionIcons']))
                @foreach($product->style_page['cardsDescriptionIcons'] as $index => $elem)
                  <x-admin.content.home-slider-1>
                    <div class="form-group">
                      <x-input-label for="style_page-cardsDescriptionIcons-{{ $index }}-icon" :value="__('Иконка')"/>
                      <select name="style_page[cardsDescriptionIcons][{{ $index }}][icon]"
                              id="style_page-cardsDescriptionIcons-{{ $index }}-icon" data-name="icon"
                              data-field="style_page" class="choisesImgSelect form-control">
                        @include('_parts.admin.s-icons', ['selected' => $elem['icon']])
                      </select>
                    </div>
                    <div class="form-group">
                      <x-input-label for="style_page-cardsDescriptionIcons-{{ $index }}-text" :value="__('Текст')"/>
                      <x-text-input name="style_page[cardsDescriptionIcons][{{ $index }}][text]"
                                    id="style_page-cardsDescriptionIcons-{{ $index }}-text" type="text" data-name="text"
                                    data-field="style_page" class="mt-1 block w-full" value="{{ $elem['text'] }}"/>
                    </div>
                  </x-admin.content.home-slider-1>
                @endforeach
              @endif
            </div>
          </div>
          <div class="flex justify-between items-end mb-2">
            <div class="form-group flex-1 mr-2">
              <x-input-label for="card_style" :value="__('Тип карточки')"/>
              <select id="card_style" name="card_style" class="form-control w-full">
                <option value="card-style-1">Тип 1 (белый градиент)</option>
                <option value="card-style-2">Тип 2 (черный градиент)</option>
                <option value="card-style-3">Тип 3 (текст без фона)</option>
                <option value="card-style-4">Тип 4 (заголовок на полосе)</option>
                <option value="card-style-5">Тип 5 (заголовок на размытом фоне)</option>
              </select>
            </div>
            <button type="button" id="addPCItem" class="button button-success mb-4">Добавить</button>
          </div>
          <div id="card-style-1_field_donor" style="display: none;">
            <x-admin.product-card-1_field></x-admin.product-card-1_field>
          </div>
          <div id="card-style-2_field_donor" style="display: none;">
            <x-admin.product-card-1_field></x-admin.product-card-1_field>
          </div>
          <div id="card-style-1_donor" data-field="card-style-1_field_donor" style="display: none;">
            <x-admin.product-card-1></x-admin.product-card-1>
          </div>
          <div id="card-style-2_donor" data-field="card-style-1_field_donor" style="display: none;">
            <x-admin.product-card-2></x-admin.product-card-2>
          </div>
          <div id="card-style-3_donor" data-field="" style="display: none;">
            <x-admin.product-card-3></x-admin.product-card-3>
          </div>
          <div id="card-style-4_donor" data-field="" style="display: none;">
            <x-admin.product-card-4></x-admin.product-card-4>
          </div>
          <div id="card-style-5_donor" data-field="" style="display: none;">
            <x-admin.product-card-5></x-admin.product-card-5>
          </div>
          <div id="product-cards_container">
            @if($cards)
              @php($product->style_cards = $cards->style_cards)
            @endif
            @if(old('product-cards')&&is_array(old('product-cards')))
              @foreach(old('product-cards') as $key => $product_card)
                <x-admin.product-card :index="$key"/>
              @endforeach
            @elseif(isset($product->style_cards)&&!empty($product->style_cards))
              @php($i=1)
              @foreach($product->style_cards as $key => $product_card)
                @php($card_style = $product_card['card_style'] ?? null)
                @if($card_style == 'card-style-1')
                  <x-admin.product-card-1 :card="$product_card" :card_index="$i"></x-admin.product-card-1>
                @elseif($card_style == 'card-style-2')
                  <x-admin.product-card-2 :card="$product_card" :card_index="$i"></x-admin.product-card-2>
                @elseif($card_style == 'card-style-3')
                  <x-admin.product-card-3 :card="$product_card" :card_index="$i"></x-admin.product-card-3>
                @elseif($card_style == 'card-style-4')
                  <x-admin.product-card-4 :card="$product_card" :card_index="$i"></x-admin.product-card-4>
                @elseif($card_style == 'card-style-5')
                  <x-admin.product-card-5 :card="$product_card" :card_index="$i"></x-admin.product-card-5>
                @endif
                @php($i++)
              @endforeach
            @endif
          </div>
        </div>
      </div>
      <div id="tab-2-content" role="tabpanel">
        <div class="sm:w-[75%]">
          @if(isset($design_data['_request']))
            <div class="my-4">
              <div class="badge badge-orange">Последние изменения в процессе сохранения</div>
            </div>
          @endif
          <div class="mb-6">
            <a href="javascript:;" data-fancybox data-src="#copyProductCards" class="button button-primary">Загрузить
              существующий дизайн</a>
          </div>

          <div class="form-group">
            <div class="flex items-center">
              <x-checkbox id="style_page-alert" name="style_page[alert]" value="1" :checked="$design_data['alert'] ?? false"/>
              <x-input-label for="style_page-alert" class="ml-2" :value="__('Всплывающее предупреждение')"/>
            </div>
            <div class="form-group">
              <x-input-label for="style_page-alertText" :value="__('Текст предупреждения')"/>
              <x-textarea name="style_page[alertText]" id="style_page-alertText"
                          class="mt-1 block w-full tinymce-textarea">{{ old('style_page')['alertText'] ?? $design_data['alertText'] ?? '' }}</x-textarea>
            </div>
            <div class="form-group">
              <x-input-label for="text_data-alertButtonText" :value="__('Текст кнопки во всплывающем окне')" />
              <x-text-input type="text" name="style_page[alertButtonText]" id="text_data-alertButtonText" value="{{ old('style_page')['alertButtonText'] ?? $design_data['alertButtonText'] ?? '' }}" class="mt-1 block w-full"/>
            </div>
            <div class="form-group">
              <x-input-label for="text_data-alertButtonUrl" :value="__('Ссылка кнопки во всплывающем окне')" />
              <x-text-input type="text" name="style_page[alertButtonUrl]" id="text_data-alertButtonUrl" value="{{ old('style_page')['alertButtonUrl'] ?? $design_data['alertButtonUrl'] ?? '' }}" class="mt-1 block w-full"/>
            </div>
            <div class="form-group">
              <x-input-label for="text_data-alertCloseButton" :value="__('Текст закрытия всплывающего окна')" />
              <x-text-input type="text" name="style_page[alertCloseButton]" id="text_data-alertCloseButton" value="{{ old('style_page')['alertCloseButton'] ?? $design_data['alertCloseButton'] ?? '' }}" class="mt-1 block w-full"/>
            </div>


            <div class="flex items-center">
              <x-checkbox id="style_page-xenon" name="style_page[xenon]" value="1" :checked="$design_data['xenon'] ?? false"/>
              <x-input-label for="style_page-xenon" class="ml-2" :value="__('Иконка «Содержит ксенон»')"/>
            </div>
            <div class="flex items-center">
              <x-checkbox id="style_page-k_info" name="style_page[k_info]" value="1" :checked="($design_data['k_info'] ?? false) == '1'"/>
              <x-input-label for="style_page-k_info" class="ml-2" :value="__('Показать блок «подробнее о комплексном уходе»')"/>
            </div>
            </div>
            <div class="form-group">
              <x-input-label for="text_data-age" :value="__('Возрастная категория')" />
              <x-text-input type="text" name="style_page[age]" id="text_data-age" value="{{ old('style_page')['age'] ?? $design_data['age'] ?? '' }}" class="mt-1 block w-full"/>
            </div>

          <div class="form-group">
            <x-input-label for="mainVideo" class="mb-2" :value="__('Видео')"/>

            <div class="space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
              <div id="lfm-preview-mainVideo" class="" data-name="style_page[mainVideo]">

                @if(isset($design_data['mainVideo']))

                  <input type="hidden" name="style_page[mainVideo][file]" id="input-lfm-mainVideo"
                         value="{{ old('style_page')['mainVideo']['file'] ?? $design_data['mainVideo']['file'] ?? '' }}">
                  <div>
                    <ul class="max-w-md space-y-1 text-gray-500 list-disc list-inside mt-2 mb-4">
                      <li><a href="javascript:;" data-src="{{ $design_data['mainVideo']['file'] }}"
                             data-fancybox>{{ filenameFromUrl($design_data['mainVideo']['file']) }}</a></li>
                      @if(isset($design_data['mainVideo']['mp4']))
                        <li><a href="javascript:;" data-src="{{ $design_data['mainVideo']['mp4'] }}"
                               data-fancybox>{{ filenameFromUrl($design_data['mainVideo']['mp4']) }}</a></li>
                      @endif
                      {{--                      <li><a href="{{ $design_data['mainVideo']['file'] }}" target="_blank">{{ filenameFromUrl($design_data['mainVideo']['file']) }}</a></li>--}}
                    </ul>
                  </div>
                @endif
              </div>
              <div class="mb-2">
                <button
                  type="button"
                  id="mainVideo"
                  class="button button-secondary"
                  data-lfm="video"
                  data-preview="lfm-preview-mainVideo">Выбрать видео
                </button>
              </div>

            </div>
          </div>
            <div class="flex items-center">
              <x-checkbox id="style_page-hide_video" name="style_page[hide_video]" value="1" :checked="$design_data['hide_video'] ?? false"/>
              <x-input-label for="style_page-hide_video" class="ml-2" :value="__('Не показывать видео')"/>
            </div>
          <div class="form-group">

            <x-input-label for="mainImage" class="mb-2" :value="__('Изображение')"/>

            <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
              <div id="lfm-preview-mainImage" class="rounded-md overflow-hidden w-20" data-name="style_page[mainImage]">
                @if(isset($design_data['mainImage']['img']))
                  <input type="hidden" name="style_page[mainImage][img]" id="input-lfm-mainImage"
                         value="{{ old('style_page')['mainImage']['img'] ?? $design_data['mainImage']['img'] ?? '' }}">
                  <input type="hidden" name="style_page[mainImage][thumb]" id="input-lfm-thumb-mainImage"
                         value="{{ old('style_page')['mainImage']['thumb'] ?? $design_data['mainImage']['thumb'] ?? '' }}">

                  <a href="javascript:;" data-fancybox="true" data-src="{{ $design_data['mainImage']['img'] }}"
                     style="display: block;">
                    <img src="{{ $design_data['mainImage']['thumb'] }}"
                         class="overflow-hidden max-w-full object-cover object-center">
                  </a>
                @endif
              </div>
              <div class="mb-2">
                <div class="mb-2 text-gray-500 text-sm">622x702px</div>
                <button
                  type="button"
                  id="mainImage"
                  class="button button-secondary"
                  data-lfm="image"
                  data-preview="lfm-preview-mainImage">Выбрать изображение
                </button>
              </div>

            </div>
          </div>
            @if($product->category_id==29)
          <div class="form-group">
            <x-input-label for="style_page-sostav-nabora" :value="__('Состав набора')"/>
            <x-textarea name="style_page[sostav-nabora]" id="style_page-sostav-nabora"
                        class="mt-1 block w-full tinymce-textarea">{{ old('style_page')['sostav-nabora'] ?? $design_data['sostav-nabora'] ?? '' }}</x-textarea>
          </div>
          <div class="form-group">
            <x-input-label for="style_page-podarok" :value="__('Подарок')"/>
            <x-textarea name="style_page[podarok]" id="style_page-podarok"
                        class="mt-1 block w-full tinymce-textarea">{{ old('style_page')['podarok'] ?? $design_data['podarok'] ?? '' }}</x-textarea>
          </div>
            @endif
          <div class="form-group">
            <x-input-label for="style_page-description" :value="__('Описание под заголовком')"/>
            <x-textarea name="style_page[description]" id="style_page-description"
                        class="mt-1 block w-full tinymce-textarea">{{ old('style_page')['description'] ?? $design_data['description'] ?? '' }}</x-textarea>
          </div>
          <div class="form-group">
            <x-input-label for="style_page-features" :value="__('Особенности продукта')"/>
            <x-textarea name="style_page[features]" id="style_page-features"
                        class="mt-1 block w-full tinymce-textarea">{{ old('style_page')['features'] ?? $design_data['features'] ?? '' }}</x-textarea>
          </div>
          <div class="form-group">
            <x-input-label for="style_page-activeComponentsPercentage" :value="__('Процент активных компонентов')"/>
            <x-text-input type="text" name="style_page[activeComponentsPercentage]"
                          id="style_page-activeComponentsPercentage"
                          value="{{ old('style_page')['activeComponentsPercentage'] ?? $design_data['activeComponentsPercentage'] ?? '' }}"
                          class="mt-1 block w-full numeric-field"/>
          </div>
          <div class="form-group">
            <x-input-label for="style_page-activeComponentsText" :value="__('Активные компоненты')"/>
            <x-textarea name="style_page[activeComponentsText]" id="style_page-activeComponentsText"
                        class="mt-1 block w-full tinymce-textarea">{{ old('style_page')['activeComponentsText'] ?? $design_data['activeComponentsText'] ?? '' }}</x-textarea>
          </div>
          <div class="carousel-contructor mb-5">
            <div class="flex justify-between items-end">
              <x-input-label for="productEffectAddSlide" class="mb-2" :value="__('Действие')"/>
              <button type="button" id="productEffectAddSlide" class="addSlide button button-success button-sm mb-2"
                      data-id="productEffect">Добавить элемент
              </button>
            </div>
            <div id="productEffect_donor" style="display: none;">
              <x-admin.content.home-slider-1>
                <div class="form-group">
                  <x-input-label :value="__('Иконка')"/>
                  <select data-name="icon" data-field="style_page" class="choisesImgSelect form-control" disabled>
                    @include('_parts.admin.icons')
                  </select>
                </div>
                <div class="form-group">
                  <x-input-label :value="__('Текст')"/>
                  <x-text-input type="text" data-name="text" data-field="style_page" class="mt-1 block w-full"
                                disabled/>
                </div>
              </x-admin.content.home-slider-1>
            </div>
            <div class="carousel space-y-3 mb-1 p-2 border-gray-300 rounded-md border">
              @if(isset($design_data['productEffect']))
                @foreach($design_data['productEffect'] as $index => $elem)
                  <x-admin.content.home-slider-1>
                    <div class="form-group">
                      <x-input-label for="style_page-productEffect-{{ $index }}-icon" :value="__('Иконка')"/>
                      <select name="style_page[productEffect][{{ $index }}][icon]"
                              id="style_page-productEffect-{{ $index }}-icon" data-name="icon" data-field="style_page"
                              class="choisesImgSelect form-control">
                        @include('_parts.admin.icons', ['selected' => $elem['icon']])
                      </select>
                    </div>
                    <div class="form-group">
                      <x-input-label for="style_page-productEffect-{{ $index }}-text" :value="__('Текст')"/>
                      <x-text-input name="style_page[productEffect][{{ $index }}][text]"
                                    id="style_page-productEffect-{{ $index }}-text" type="text" data-name="text"
                                    data-field="style_page" class="mt-1 block w-full" value="{{ $elem['text'] }}"/>
                    </div>
                  </x-admin.content.home-slider-1>
                @endforeach
              @endif
            </div>
          </div>
            <div class="carousel-contructor mb-5">
              <div class="flex justify-between items-end">
                <x-input-label for="celebritiesAddSlide" class="mb-2" :value="__('Бренд, который выбирают звезды')"/>
                <button type="button" id="celebritiesAddSlide" class="addSlide button button-success button-sm mb-2"
                        data-id="celebrities">Добавить элемент
                </button>
              </div>
              <div id="celebrities_donor" style="display: none;">
                <x-admin.content.home-slider-1>
                  <div class="slideImage">
                    <x-input-label class="image-label mb-2" :value="__('Изображение')" />

                    <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
                      <div class="lfm-preview flex flex-wrap"></div>
                      <div class="mb-2">
                        <div class="mb-2 text-gray-500 text-sm">546x658px</div>
                        <button
                          type="button"
                          id="celebrities"
                          class="button button-secondary"
                          data-lfm="image">Выбрать изображение</button>
                      </div>

                    </div>
                  </div>
                  <div class="form-group">
                    <x-input-label :value="__('Имя')"/>
                    <x-text-input type="text" data-name="name" data-field="style_page" class="mt-1 block w-full"
                                  disabled/>
                  </div>
                </x-admin.content.home-slider-1>
              </div>
              <div class="carousel space-y-3 mb-1 p-2 border-gray-300 rounded-md border">
                @if(isset($design_data['celebrities']))
                  @foreach($design_data['celebrities'] as $index => $elem)
                    <x-admin.content.home-slider-1>
                      <div class="slideImage">
                        <x-input-label class="image-label mb-2" :value="__('Изображение')" />

                        <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
                          <div id="lfm-preview-celebrities-{{ $index }}" class="lfm-preview flex flex-wrap" data-name="style_page[celebrities][{{ $index }}][image]">
                            <input type="hidden" name="style_page[celebrities][{{ $index }}][image][img]" id="style_page-celebrities-{{ $index }}-[image]img" data-name="img" data-parent="image" value="{{ $elem['image']['img'] ?? '' }}">
                            <input type="hidden" name="style_page[celebrities][{{ $index }}][image][thumb]" id="style_page-celebrities-{{ $index }}-[image]thumb" data-name="thumb" data-parent="image" value="{{ $elem['image']['thumb'] ?? '' }}">

                            <a href="javascript:;" data-fancybox="true" data-src="{{ $elem['image']['img'] ?? '' }}" style="display: block;">
                              <img src="{{ $elem['image']['thumb'] ?? '' }}" class="overflow-hidden max-w-full object-cover object-center">
                            </a>
                          </div>
                          <div class="mb-2">
                            <div class="mb-2 text-gray-500 text-sm"></div>
                            <button
                              type="button"
                              id="celebrities"
                              class="button button-secondary"
                              data-lfm="image"
                              data-preview="lfm-preview-celebrities-{{ $index }}">Выбрать изображение</button>
                          </div>
                        </div>
                      </div>
                      <div class="form-group">
                        <x-input-label for="style_page-celebrities-{{ $index }}-name" :value="__('Имя')"/>
                        <x-text-input name="style_page[celebrities][{{ $index }}][name]"
                                      id="style_page-celebrities-{{ $index }}-name" type="text" data-name="name"
                                      data-field="style_page" class="mt-1 block w-full" value="{{ $elem['name'] }}"/>
                      </div>
                    </x-admin.content.home-slider-1>
                  @endforeach
                @endif
              </div>
            </div>
            <div class="space-y-3 mb-1">
              <div class="mb-1 p-2 border-gray-300 rounded-md border">
                <div class="form-group">
                  <x-input-label for="style_page-typeOfSkinTitle" :value="__('Тип кожи. Заголовок')"/>
                  <x-text-input name="style_page[typeOfSkinTitle]" id="style_page-typeOfSkinTitle"
                                class="mt-1 block w-full"
                                value="{{ old('style_page')['typeOfSkinTitle'] ?? $design_data['typeOfSkinTitle'] ?? '' }}"/>
                </div>
                <div class="carousel-contructor mb-5">
                  <div class="flex justify-between items-end">
                    <x-input-label for="typeOfSkinAddSlide" class="mb-2" :value="__('Тип кожи')"/>
                    <button type="button" id="typeOfSkinAddSlide" class="addSlide button button-success button-sm mb-2"
                            data-id="typeOfSkin">Добавить элемент
                    </button>
                  </div>
                  <div id="typeOfSkin_donor" style="display: none;">
                    <x-admin.content.home-slider-1>
                      <div class="form-group">
                        <x-input-label :value="__('Текст')"/>
                        <x-text-input type="text" data-name="text" data-field="style_page" class="mt-1 block w-full"
                                      disabled/>
                      </div>
                    </x-admin.content.home-slider-1>
                  </div>
                  <div class="carousel space-y-3 mb-1 p-2 border-gray-300 rounded-md border">
                    @if(isset($design_data['typeOfSkin']))
                      @foreach($design_data['typeOfSkin'] as $index => $elem)
                        <x-admin.content.home-slider-1>
                          <div class="form-group">
                            <x-input-label for="style_page-typeOfSkin-{{ $index }}-text" :value="__('Текст')"/>
                            <x-text-input name="style_page[typeOfSkin][{{ $index }}][text]"
                                          id="style_page-typeOfSkin-{{ $index }}-text" type="text" data-name="text"
                                          data-field="style_page" class="mt-1 block w-full" value="{{ $elem['text'] }}"/>
                          </div>
                        </x-admin.content.home-slider-1>
                      @endforeach
                    @endif
                  </div>
                </div>
                <div class="form-group">
                  <x-input-label for="style_page-typeOfSkinItalic" :value="__('Тип кожи. Подпись курсивом')"/>
                  <x-textarea name="style_page[typeOfSkinItalic]" id="style_page-typeOfSkinItalic"
                              class="mt-1 block w-full">{{ old('style_page')['typeOfSkinItalic'] ?? $design_data['typeOfSkinItalic'] ?? '' }}</x-textarea>
                </div>
              </div>
              @if($product->category_id==29)
                <div class="mb-1 p-2 border-gray-300 rounded-md border">
                  <div class="form-group">
                    <x-input-label for="style_page-sostavNaboraBlockTitle" :value="__('Состав набора. Заголовок')"/>
                    <x-text-input name="style_page[sostavNaboraBlockTitle]" id="style_page-sostavNaboraBlockTitle"
                                  class="mt-1 block w-full"
                                  value="{{ old('style_page')['sostavNaboraBlockTitle'] ?? $design_data['sostavNaboraBlockTitle'] ?? '' }}"/>
                  </div>
                  <div class="carousel-contructor mb-5">
                    <div class="flex justify-between items-end">
                      <x-input-label for="sostavNaboraBlockAddSlide" class="mb-2" :value="__('Состав набора')"/>
                      <button type="button" id="sostavNaboraBlockAddSlide" class="addSlide button button-success button-sm mb-2"
                              data-id="sostavNaboraBlock">Добавить элемент
                      </button>
                    </div>
                    <div id="sostavNaboraBlock_donor" style="display: none;">
                      <x-admin.content.home-slider-1>
                        <div class="form-group">
                          <x-input-label :value="__('Текст')"/>
                          <x-textarea type="text" data-name="text" data-field="style_page" class="mt-1 block w-full"
                                      disabled></x-textarea>
                        </div>
                      </x-admin.content.home-slider-1>
                    </div>
                    <div class="carousel space-y-3 mb-1 p-2 border-gray-300 rounded-md border">
                      @if(isset($design_data['sostavNaboraBlock']))
                        @foreach($design_data['sostavNaboraBlock'] as $index => $elem)
                          <x-admin.content.home-slider-1>
                            <div class="form-group">
                              <x-input-label for="style_page-sostavNaboraBlock-{{ $index }}-text" :value="__('Текст')"/>
                              <x-textarea name="style_page[sostavNaboraBlock][{{ $index }}][text]"
                                            id="style_page-sostavNaboraBlock-{{ $index }}-text" type="text" data-name="text"
                                          data-field="style_page" class="mt-1 tinymce-textarea block w-full">{{ $elem['text'] }}</x-textarea>
                            </div>
                          </x-admin.content.home-slider-1>
                        @endforeach
                      @endif
                    </div>
                  </div>
                  <div class="form-group">
                    <x-input-label for="style_page-sostavNaboraBlockDescription" :value="__('Дополнительный текст')"/>
                    <x-textarea name="style_page[sostavNaboraBlockDescription]" id="style_page-sostavNaboraBlockDescription"
                                class="mt-1 block w-full tinymce-textarea">{{ old('style_page')['sostavNaboraBlockDescription'] ?? $design_data['sostavNaboraBlockDescription'] ?? '' }}</x-textarea>
                  </div>
                </div>
{{--                <div class="mb-1 p-2 border-gray-300 rounded-md border">--}}
{{--                  <div class="form-group">--}}
{{--                    <x-input-label for="style_page-podarokBlockTitle" :value="__('Подарок. Заголовок')"/>--}}
{{--                    <x-text-input name="style_page[podarokBlockTitle]" id="style_page-podarokBlockTitle"--}}
{{--                                  class="mt-1 block w-full"--}}
{{--                                  value="{{ old('style_page')['podarokBlockTitle'] ?? $design_data['podarokBlockTitle'] ?? '' }}"/>--}}
{{--                  </div>--}}
{{--                  <div class="carousel-contructor mb-5">--}}
{{--                    <div class="flex justify-between items-end">--}}
{{--                      <x-input-label for="podarokBlockAddSlide" class="mb-2" :value="__('Подарок')"/>--}}
{{--                      <button type="button" id="podarokBlockAddSlide" class="addSlide button button-success button-sm mb-2"--}}
{{--                              data-id="podarokBlock">Добавить элемент--}}
{{--                      </button>--}}
{{--                    </div>--}}
{{--                    <div id="podarokBlock_donor" style="display: none;">--}}
{{--                      <x-admin.content.home-slider-1>--}}
{{--                        <div class="form-group">--}}
{{--                          <x-input-label :value="__('Текст')"/>--}}
{{--                          <x-text-input type="text" data-name="text" data-field="style_page" class="mt-1 block w-full"--}}
{{--                                        disabled/>--}}
{{--                        </div>--}}
{{--                      </x-admin.content.home-slider-1>--}}
{{--                    </div>--}}
{{--                    <div class="carousel space-y-3 mb-1 p-2 border-gray-300 rounded-md border">--}}
{{--                      @if(isset($design_data['podarokBlock']))--}}
{{--                        @foreach($design_data['podarokBlock'] as $index => $elem)--}}
{{--                          <x-admin.content.home-slider-1>--}}
{{--                            <div class="form-group">--}}
{{--                              <x-input-label for="style_page-podarokBlock-{{ $index }}-text" :value="__('Текст')"/>--}}
{{--                              <x-text-input name="style_page[podarokBlock][{{ $index }}][text]"--}}
{{--                                            id="style_page-podarokBlock-{{ $index }}-text" type="text" data-name="text"--}}
{{--                                            data-field="style_page" class="mt-1 block w-full" value="{{ $elem['text'] }}"/>--}}
{{--                            </div>--}}
{{--                          </x-admin.content.home-slider-1>--}}
{{--                        @endforeach--}}
{{--                      @endif--}}
{{--                    </div>--}}
{{--                  </div>--}}
{{--                  <div class="form-group">--}}
{{--                    <x-input-label for="style_page-podarokBlockItalic" :value="__('Подарок. Подпись курсивом')"/>--}}
{{--                    <x-textarea name="style_page[podarokBlockItalic]" id="style_page-podarokBlockItalic"--}}
{{--                                class="mt-1 block w-full">{{ old('style_page')['podarokBlockItalic'] ?? $design_data['podarokBlockItalic'] ?? '' }}</x-textarea>--}}
{{--                  </div>--}}
{{--                </div>--}}
              @endif
            </div>


          <div class="form-group">
            <x-input-label for="care_compatibility" :value="__('Комплексный уход и совместимость продуктов')"/>
            <x-textarea name="style_page[care_compatibility]" id="style_page-care_compatibility"
                        class="mt-1 block w-full tinymce-textarea" data-background="#6C715C" data-color="#fff" data-style_formats="1" data-toolbar="clearstyles | styles fontfamily bold italic letter-case headlines | align bullist forecolor | code | arrowBottom addSpace addHalfSpace">{{ old('style_page')['care_compatibility'] ?? $design_data['care_compatibility'] ?? '' }}</x-textarea>
          </div>

          <div class="carousel-contructor mb-5">
            <div class="flex justify-between items-end">
              <x-input-label for="accordionAddSlide" class="mb-2" :value="__('Аккордеон')"/>
              <button type="button" id="accordionAddSlide" class="addSlide button button-success button-sm mb-2"
                      data-id="accordion">Добавить элемент
              </button>
            </div>
            <div id="accordion_donor" style="display: none;">
              <x-admin.content.home-slider-1>
                <div class="form-group">
                  <x-input-label :value="__('Заголовок')"/>
                  <x-text-input type="text" data-name="title" data-field="style_page" class="mt-1 block w-full"
                                disabled/>
                </div>

                <div class="form-group">
                  <x-input-label :value="__('Текст')"/>
                  <x-textarea data-name="text" data-field="style_page" class="mt-1 block w-full tinymce-textarea"
                              disabled/>
                </div>
              </x-admin.content.home-slider-1>
            </div>
            <div class="carousel space-y-3 mb-1 p-2 border-gray-300 rounded-md border">
              @if(isset($design_data['accordion']))
                @foreach($design_data['accordion'] as $index => $elem)
                  <x-admin.content.home-slider-1>
                    <div class="form-group">
                      <x-input-label for="style_page-accordion-{{ $index }}-title" :value="__('Заголовок')"/>
                      <x-text-input type="text" name="style_page[accordion][{{ $index }}][title]"
                                    id="style_page-accordion-{{ $index }}-title" data-name="title"
                                    data-field="style_page" class="mt-1 block w-full" value="{{ $elem['title'] }}"/>
                    </div>
                    <div class="form-group">
                      <x-input-label for="style_page-accordion-{{ $index }}-text" :value="__('Текст')"/>
                      <x-textarea name="style_page[accordion][{{ $index }}][text]"
                                  id="style_page-accordion-{{ $index }}-text" data-name="text" data-field="style_page"
                                  class="mt-1 block w-full tinymce-textarea">{!! $elem['text'] !!}</x-textarea>
                    </div>
                  </x-admin.content.home-slider-1>
                @endforeach
              @endif
            </div>
          </div>

          <div class="carousel-contructor mb-5">
            <div class="form-group">
              <x-input-label for="style_page-weRecommend" :value="__('Слайдер с товарами')"/>
              <select name="style_page[weRecommend][]" id="style_page-weRecommend" multiple
                      class="multipleSelect form-control">
                @foreach($products as $p)
                  <option value="{{ $p->id }}"
                          data-keywords="{{ $p->category_title }}" @if(isset($design_data['weRecommend'])&&in_array($p->id, $design_data['weRecommend']))
                    {!! 'selected' !!}
                    @endif>{{ $p->id }}: {{ $p->name }} ({{ $p->sku }}, Категория "{{ $p->category_title }}")
                  </option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="px-3 sm:px-4 lg:px-6 py-6 flex justify-between">
      <div class="form-group">
        <div class="flex items-center">
          <x-checkbox id="clearCache" name="clearCache" value="1"/>
          <x-input-label for="clearCache" class="ml-2" :value="__('Очистить кэш изображений')"/>
        </div>
      </div><x-primary-button>Сохранить</x-primary-button>
    </div>
  </form>
  <div class="w-full max-w-3xl" id="copyProductCards" style="display: none;">
    <div class="p-4">
      @if($products->count())
        <form action="{{ route('admin.products.editDesign', $product->slug)  }}#tab-1" method="get">
          <div class="form-group">
            <x-input-label for="cards" :value="__('Взять карточки из товара')"/>
            <select id="cards" name="cards" class="form-control w-full">
              <option value="">Выбрать</option>
              @foreach($products as $product)
                @if(!$product->style_cards)
                  @continue
                @endif
                <option value="{{ $product->slug }}">{{ $product->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <x-input-label for="design" :value="__('Взять дизайн из товара')"/>
            <select id="design" name="design" class="form-control w-full">
              <option value="">Выбрать</option>
              @foreach($products as $product)
                @if(!$product->style_page)
                  @continue
                @endif
                <option value="{{ $product->slug }}">{{ $product->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="flex justify-center">
            <x-primary-button>Выбрать</x-primary-button>
          </div>
        </form>
      @else
        <div class="text-center text-2xl text-gray-400">Нет продуктов</div>
      @endif
    </div>
  </div>
</x-admin-layout>
