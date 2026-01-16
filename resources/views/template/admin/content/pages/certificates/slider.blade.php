



<div class="carousel-contructor mb-5">
  <div class="flex justify-between items-end">
    <x-input-label for="certificatesAddSlideRed" class="mb-2" :value="__('Красные сертификаты')" />
    <button type="button" id="certificatesAddSlideRed" class="addSlide button button-success button-sm mb-2" data-id="certificatesRed">Добавить слайд</button>
  </div>
  <div id="certificatesRed_donor" style="display: none;">
    <x-admin.content.home-slider-1>
      <div class="slideFile">
        <x-input-label class="image-label mb-2" :value="__('Сертификат')" />

        <div class="form-group">
          <div class="mb-1 p-2 border-gray-300 rounded-md border">
            <div class="lfm-preview flex flex-wrap"></div>
            <div class="mb-2">
              <div class="mb-2 text-gray-500 text-sm">pdf, jpg, png</div>
              <button
                type="button"
                id="certificatesRed"
                class="button button-secondary"
                data-lfm="file">Выбрать сертификат</button>
            </div>

          </div>

        </div>

      </div>
      <div class="form-group">
        <x-input-label :value="__('Имя')" />
        <x-text-input type="text" data-name="name" class="mt-1 block w-full" disabled/>
      </div>
      <div class="form-group">
        <x-input-label :value="__('Подпись')" />
        <x-text-input type="text" data-name="description" class="mt-1 block w-full" disabled/>
      </div>
    </x-admin.content.home-slider-1>
  </div>
  <div class="carousel space-y-3 mb-1 p-2 border-gray-300 rounded-md border">
    @if(isset($content->carousel_data['certificatesRed']))
      @foreach($content->carousel_data['certificatesRed'] as $index => $slide)
        <x-admin.content.home-slider-1>
          <div class="slideFile">
            <x-input-label class="image-label mb-2" :value="__('Красный сертификат')" />
            <div class="form-group">
              <div class="mb-1 p-2 border-gray-300 rounded-md border">
                <div id="lfm-preview-certificatesRed-{{ $index }}" class="lfm-preview flex flex-wrap" data-name="carousel_data[certificatesRed][{{ $index }}]">
                  @if (isset($slide['file'])&&!empty($slide['file']))
                    <input type="hidden" name="carousel_data[certificatesRed][{{ $index }}][file]" value="{{ $slide['file'] }}">
                    <div>
                      <ul class="max-w-md space-y-1 text-gray-500 list-disc list-inside mt-2 mb-4">
                        <li>{{ filenameFromUrl($slide['file']) }}</li>
                      </ul>
                    </div>
                  @elseif(isset($slide['files'])&&is_array($slide['files']))
                    @foreach($slide['files'] as $key => $file)
                      <input type="hidden" name="carousel_data[certificatesRed][{{ $index }}][files][{{ $key }}][file]" value="{{ $file['file'] }}">
                      <a href="javascript:;" data-fancybox="true" data-src="{{ $file['file'] }}" class="block rounded-md overflow-hidden w-20 m-2">
                        <img src="{{ $file['thumb'] ?? $file['file'] }}" class="overflow-hidden max-w-full object-cover object-center">
                      </a>
                    @endforeach
                  @endif
                </div>
                <div class="mb-2">
                  <div class="mb-2 text-gray-500 text-sm">pdf, jpg, png</div>
                  <button
                    type="button"
                    id="certificatesRed"
                    class="button button-secondary"
                    data-lfm="file"
                    data-preview="lfm-preview-certificatesRed-{{ $index }}">Выбрать сертификат</button>
                </div>
              </div>
            </div>

          </div>
          <div class="form-group">
            <x-input-label :value="__('Заголовок')" />
            <x-text-input type="text" name="carousel_data[certificatesRed][{{ $index }}][name]" id="carousel_data-certificatesRed-{{ $index }}-name" data-name="name" class="mt-1 block w-full" value="{{ $slide['name'] }}"/>
          </div>
          <div class="form-group">
            <x-input-label :value="__('Подпись')" />
            <x-text-input type="text" name="carousel_data[certificatesRed][{{ $index }}][description]" id="carousel_data-certificatesRed-{{ $index }}-description" data-name="description" class="mt-1 block w-full" value="{{ $slide['description'] ?? '' }}"/>
          </div>
        </x-admin.content.home-slider-1>
      @endforeach
    @endif
  </div>
</div>

<div class="carousel-contructor mb-5">
  <div class="flex justify-between items-end">
    <x-input-label for="certificatesAddSlideGreen" class="mb-2" :value="__('Зеленые сертификаты')" />
    <button type="button" id="certificatesAddSlideGreen" class="addSlide button button-success button-sm mb-2" data-id="certificatesGreen">Добавить слайд</button>
  </div>
  <div id="certificatesGreen_donor" style="display: none;">
    <x-admin.content.home-slider-1>
      <div class="slideFile">
        <x-input-label class="image-label mb-2" :value="__('Сертификат')" />

        <div class="form-group">
          <div class="mb-1 p-2 border-gray-300 rounded-md border">
            <div class="lfm-preview flex flex-wrap"></div>
            <div class="mb-2">
              <div class="mb-2 text-gray-500 text-sm">pdf, jpg, png</div>
              <button
                type="button"
                id="certificatesGreen"
                class="button button-secondary"
                data-lfm="file">Выбрать сертификат</button>
            </div>

          </div>

        </div>

      </div>
      <div class="form-group">
        <x-input-label :value="__('Имя')" />
        <x-text-input type="text" data-name="name" class="mt-1 block w-full" disabled/>
      </div>
      <div class="form-group">
        <x-input-label :value="__('Подпись')" />
        <x-text-input type="text" data-name="description" class="mt-1 block w-full" disabled/>
      </div>
    </x-admin.content.home-slider-1>
  </div>
  <div class="carousel space-y-3 mb-1 p-2 border-gray-300 rounded-md border">
    @if(isset($content->carousel_data['certificatesGreen']))
      @foreach($content->carousel_data['certificatesGreen'] as $index => $slide)
        <x-admin.content.home-slider-1>
          <div class="slideFile">
            <x-input-label class="image-label mb-2" :value="__('Сертификат')" />
            <div class="form-group">
              <div class="mb-1 p-2 border-gray-300 rounded-md border">
                <div id="lfm-preview-certificatesGreen-{{ $index }}" class="lfm-preview flex flex-wrap" data-name="carousel_data[certificatesGreen][{{ $index }}]">
                  @if (isset($slide['file'])&&!empty($slide['file']))
                    <input type="hidden" name="carousel_data[certificatesGreen][{{ $index }}][file]" value="{{ $slide['file'] }}">
                    <div>
                      <ul class="max-w-md space-y-1 text-gray-500 list-disc list-inside mt-2 mb-4">
                        <li>{{ filenameFromUrl($slide['file']) }}</li>
                      </ul>
                    </div>
                  @elseif(isset($slide['files'])&&is_array($slide['files']))
                    @foreach($slide['files'] as $key => $file)
                      <input type="hidden" name="carousel_data[certificatesGreen][{{ $index }}][files][{{ $key }}][file]" value="{{ $file['file'] }}">
                      <a href="javascript:;" data-fancybox="true" data-src="{{ $file['file'] }}" class="block rounded-md overflow-hidden w-20 m-2">
                        <img src="{{ $file['thumb'] ?? $file['file'] }}" class="overflow-hidden max-w-full object-cover object-center">
                      </a>
                    @endforeach
                  @endif
                </div>
                <div class="mb-2">
                  <div class="mb-2 text-gray-500 text-sm">pdf, jpg, png</div>
                  <button
                    type="button"
                    id="certificatesGreen"
                    class="button button-secondary"
                    data-lfm="file"
                    data-preview="lfm-preview-certificatesGreen-{{ $index }}">Выбрать сертификат</button>
                </div>
              </div>
            </div>

          </div>
          <div class="form-group">
            <x-input-label :value="__('Заголовок')" />
            <x-text-input type="text" name="carousel_data[certificatesGreen][{{ $index }}][name]" id="carousel_data-certificatesGreen-{{ $index }}-name" data-name="name" class="mt-1 block w-full" value="{{ $slide['name'] }}"/>
          </div>
          <div class="form-group">
            <x-input-label :value="__('Подпись')" />
            <x-text-input type="text" name="carousel_data[certificatesGreen][{{ $index }}][description]" id="carousel_data-certificatesGreen-{{ $index }}-description" data-name="description" class="mt-1 block w-full" value="{{ $slide['description'] ?? '' }}"/>
          </div>
        </x-admin.content.home-slider-1>
      @endforeach
    @endif
  </div>
</div>
<div class="carousel-contructor mb-5">
  <div class="flex justify-between items-end">
    <x-input-label for="certificatesAddSlideKids" class="mb-2" :value="__('Детские сертификаты')" />
    <button type="button" id="certificatesAddSlideKids" class="addSlide button button-success button-sm mb-2" data-id="certificatesKids">Добавить слайд</button>
  </div>
  <div id="certificatesKids_donor" style="display: none;">
    <x-admin.content.home-slider-1>
      <div class="slideFile">
        <x-input-label class="image-label mb-2" :value="__('Сертификат')" />

        <div class="form-group">
          <div class="mb-1 p-2 border-gray-300 rounded-md border">
            <div class="lfm-preview flex flex-wrap"></div>
            <div class="mb-2">
              <div class="mb-2 text-gray-500 text-sm">pdf, jpg, png</div>
              <button
                type="button"
                id="certificatesKids"
                class="button button-secondary"
                data-lfm="file">Выбрать сертификат</button>
            </div>

          </div>

        </div>

      </div>
      <div class="form-group">
        <x-input-label :value="__('Имя')" />
        <x-text-input type="text" data-name="name" class="mt-1 block w-full" disabled/>
      </div>
      <div class="form-group">
        <x-input-label :value="__('Подпись')" />
        <x-text-input type="text" data-name="description" class="mt-1 block w-full" disabled/>
      </div>
    </x-admin.content.home-slider-1>
  </div>
  <div class="carousel space-y-3 mb-1 p-2 border-gray-300 rounded-md border">
    @if(isset($content->carousel_data['certificatesKids']))
      @foreach($content->carousel_data['certificatesKids'] as $index => $slide)
        <x-admin.content.home-slider-1>
          <div class="slideFile">
            <x-input-label class="image-label mb-2" :value="__('Сертификат')" />
            <div class="form-group">
              <div class="mb-1 p-2 border-gray-300 rounded-md border">
                <div id="lfm-preview-certificatesKids-{{ $index }}" class="lfm-preview flex flex-wrap" data-name="carousel_data[certificatesKids][{{ $index }}]">
                  @if (isset($slide['file'])&&!empty($slide['file']))
                    <input type="hidden" name="carousel_data[certificatesKids][{{ $index }}][file]" value="{{ $slide['file'] }}">
                    <div>
                      <ul class="max-w-md space-y-1 text-gray-500 list-disc list-inside mt-2 mb-4">
                        <li>{{ filenameFromUrl($slide['file']) }}</li>
                      </ul>
                    </div>
                  @elseif(isset($slide['files'])&&is_array($slide['files']))
                    @foreach($slide['files'] as $key => $file)
                      <input type="hidden" name="carousel_data[certificatesKids][{{ $index }}][files][{{ $key }}][file]" value="{{ $file['file'] }}">
                      <a href="javascript:;" data-fancybox="true" data-src="{{ $file['file'] }}" class="block rounded-md overflow-hidden w-20 m-2">
                        <img src="{{ $file['thumb'] ?? $file['file'] }}" class="overflow-hidden max-w-full object-cover object-center">
                      </a>
                    @endforeach
                  @endif
                </div>
                <div class="mb-2">
                  <div class="mb-2 text-gray-500 text-sm">pdf, jpg, png</div>
                  <button
                    type="button"
                    id="certificatesKids"
                    class="button button-secondary"
                    data-lfm="file"
                    data-preview="lfm-preview-certificatesKids-{{ $index }}">Выбрать сертификат</button>
                </div>
              </div>
            </div>

          </div>
          <div class="form-group">
            <x-input-label :value="__('Заголовок')" />
            <x-text-input type="text" name="carousel_data[certificatesKids][{{ $index }}][name]" id="carousel_data-certificatesKids-{{ $index }}-name" data-name="name" class="mt-1 block w-full" value="{{ $slide['name'] }}"/>
          </div>
          <div class="form-group">
            <x-input-label :value="__('Подпись')" />
            <x-text-input type="text" name="carousel_data[certificatesKids][{{ $index }}][description]" id="carousel_data-certificatesKids-{{ $index }}-description" data-name="description" class="mt-1 block w-full" value="{{ $slide['description'] ?? '' }}"/>
          </div>
        </x-admin.content.home-slider-1>
      @endforeach
    @endif
  </div>
</div>

<div class="carousel-contructor mb-5">
  <div class="flex justify-between items-end">
    <x-input-label for="certificatesAddSlide" class="mb-2" :value="__('Сертификаты')" />
    <button type="button" id="certificatesAddSlide" class="addSlide button button-success button-sm mb-2" data-id="certificates">Добавить слайд</button>
  </div>
  <div id="certificates_donor" style="display: none;">
    <x-admin.content.home-slider-1>
      <div class="slideFile">
        <x-input-label class="image-label mb-2" :value="__('Сертификат')" />

        <div class="form-group">
          <div class="mb-1 p-2 border-gray-300 rounded-md border">
            <div class="lfm-preview flex flex-wrap"></div>
            <div class="mb-2">
              <div class="mb-2 text-gray-500 text-sm">pdf, jpg, png</div>
              <button
                type="button"
                id="certificates"
                class="button button-secondary"
                data-lfm="file">Выбрать сертификат</button>
            </div>

          </div>

        </div>

      </div>
      <div class="form-group">
        <x-input-label :value="__('Имя')" />
        <x-text-input type="text" data-name="name" class="mt-1 block w-full" disabled/>
      </div>
      <div class="form-group">
        <x-input-label :value="__('Подпись')" />
        <x-text-input type="text" data-name="description" class="mt-1 block w-full" disabled/>
      </div>
    </x-admin.content.home-slider-1>
  </div>
  <div class="carousel space-y-3 mb-1 p-2 border-gray-300 rounded-md border">
    @if(isset($content->carousel_data['certificates']))
      @foreach($content->carousel_data['certificates'] as $index => $slide)
        <x-admin.content.home-slider-1>
          <div class="slideFile">
            <x-input-label class="image-label mb-2" :value="__('Сертификат')" />
            <div class="form-group">
              <div class="mb-1 p-2 border-gray-300 rounded-md border">
                <div id="lfm-preview-certificates-{{ $index }}" class="lfm-preview flex flex-wrap" data-name="carousel_data[certificates][{{ $index }}]">
                  @if (isset($slide['file'])&&!empty($slide['file']))
                    <input type="hidden" name="carousel_data[certificates][{{ $index }}][file]" value="{{ $slide['file'] }}">
                    <div>
                      <ul class="max-w-md space-y-1 text-gray-500 list-disc list-inside mt-2 mb-4">
                        <li>{{ filenameFromUrl($slide['file']) }}</li>
                      </ul>
                    </div>
                  @elseif(isset($slide['files'])&&is_array($slide['files']))
                    @foreach($slide['files'] as $key => $file)
                      <input type="hidden" name="carousel_data[certificates][{{ $index }}][files][{{ $key }}][file]" value="{{ $file['file'] }}">
                      <a href="javascript:;" data-fancybox="true" data-src="{{ $file['file'] }}" class="block rounded-md overflow-hidden w-20 m-2">
                        <img src="{{ $file['thumb'] ?? $file['file'] }}" class="overflow-hidden max-w-full object-cover object-center">
                      </a>
                    @endforeach
                  @endif
                </div>
                <div class="mb-2">
                  <div class="mb-2 text-gray-500 text-sm">pdf, jpg, png</div>
                  <button
                    type="button"
                    id="certificates"
                    class="button button-secondary"
                    data-lfm="file"
                    data-preview="lfm-preview-certificates-{{ $index }}">Выбрать сертификат</button>
                </div>
              </div>
            </div>

          </div>
          <div class="form-group">
            <x-input-label :value="__('Заголовок')" />
            <x-text-input type="text" name="carousel_data[certificates][{{ $index }}][name]" id="carousel_data-certificates-{{ $index }}-name" data-name="name" class="mt-1 block w-full" value="{{ $slide['name'] }}"/>
          </div>
          <div class="form-group">
            <x-input-label :value="__('Подпись')" />
            <x-text-input type="text" name="carousel_data[certificates][{{ $index }}][description]" id="carousel_data-certificates-{{ $index }}-description" data-name="description" class="mt-1 block w-full" value="{{ $slide['description'] ?? '' }}"/>
          </div>
        </x-admin.content.home-slider-1>
      @endforeach
    @endif
  </div>
</div>
