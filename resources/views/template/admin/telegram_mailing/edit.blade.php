@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <x-slot name="script">
    <script>
      window.filemanger.working_dir = @json($working_dir);
    </script>
  </x-slot>
  <div class="mx-auto py-4">
    <div class="mb-4">Выбрано пользователей: {{ $users }}</div>
    <form action="{{ route('admin.telegram_mailing.update', $tgMailing->id) }}" method="POST">
      @csrf
      @method('PUT')
      <div class="form-group">
        <div class="form-group">
          <x-input-label for="mainImage" class="mb-2" :value="__('Изображение')" />
          <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
            <div id="lfm-preview-mainImage" class="rounded-md overflow-hidden w-20" data-name="image">
              @if(old('image') || isset($tgMailing->data['image']))
                <input type="hidden" name="image[img]" id="input-lfm-image" value="{{ old('image')['img'] ?? $tgMailing->data['image']['img'] ?? '' }}">
                <input type="hidden" name="image[thumb]" id="input-lfm-thumb-image" value="{{ old('image')['thumb'] ?? $tgMailing->data['image']['thumb'] ?? '' }}">
                <a href="javascript:;" data-fancybox="true" data-src="{{ old('image')['img'] ?? $tgMailing->data['image']['img'] ?? '' }}" style="display: block;">
                  <img src="{{ old('image')['thumb'] ?? $tgMailing->data['image']['thumb'] ?? '' }}" class="overflow-hidden max-w-full object-cover object-center">
                </a>
              @endif
            </div>
            <div class="mb-2">
              <div class="mb-2 text-gray-500 text-sm"></div>
              <button
                type="button"
                id="mainImage"
                class="button button-secondary"
                data-lfm="image"
                data-preview="lfm-preview-mainImage">Выбрать изображение</button>
            </div>
          </div>
          <div class="hint">Изображение должно быть сжато через телеграм, иначе могут возникнуть проблемы с отправкой</div>
        </div>
      </div>
      <div class="form-group">
        <x-input-label for="video" class="mb-2" :value="__('Видео')"/>

        <div class="space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
          <div id="lfm-preview-video" class="" data-name="video">
            @if(isset($tgMailing->data['video']))

              <input type="hidden" name="video[file]" id="input-lfm-mainVideo"
                     value="{{ $tgMailing->data['video']['file'] ?? '' }}">
              <div>
                <ul class="max-w-md space-y-1 text-gray-500 list-disc list-inside mt-2 mb-4">
                  <li><a href="javascript:;" data-src="#mov-video"
                         data-fancybox>{{ filenameFromUrl($tgMailing->data['video']['file']) }}</a></li>
                  {{--                      <li><a href="{{ $design_data['mainVideo']['file'] }}" target="_blank">{{ filenameFromUrl($design_data['mainVideo']['file']) }}</a></li>--}}
                </ul>
              </div>
              <div class="hidden">
                <div style="display: none;" id="mov-video">
                  <video controls width="100%" style="max-height: 80vh">
                    <source src="{{ $tgMailing->data['video']['file'] }}" type="video/quicktime">
                    Ваш браузер не поддерживает воспроизведение видео.
                  </video>
                </div>
              </div>
            @endif
          </div>
          <div class="mb-2">
            <button
              type="button"
              id="video"
              class="button button-secondary"
              data-lfm="video"
              data-preview="lfm-preview-video">Выбрать видео
            </button>
          </div>
          <div class="hint">Видео должно быть в формате .mp4 или .mov, не более 50мб</div>
        </div>
      </div>
      <div class="form-group">
        <x-input-label for="message" :value="__('Сообщение')" />
        <x-textarea type="text" name="message" id="message" class="mt-1 w-full auto-height">{{ old('message') ?? $tgMailing->message }}</x-textarea>
        <div class="hint">
          Телеграм поддерживает форматирование HTML<br/>
          Поддерживается следующие теги:<br/><br/>
          {{ '<b>Жирный текст</b>' }}<br/>
          {{ '<i>Наклонный текст</i>' }}<br/>
          {{ '<u>Подчеркнутый текст</u>' }}<br/>
          {{ '<s>Перечеркнутый текст</s>' }}<br/>
          {{ '<span class="tg-spoiler">Спойлер</span>' }}<br/>
          {{ '<a href="#">Внешняя ссылка</a>' }}
        </div>
      </div>
      <div class="form-group">
        <x-input-label for="send_at" :value="__('Дата отправки')"/>
        <x-text-input type="text" name="send_at" id="send_at" data-minDate="false" placeholder="{{ now()->format('d.m.Y H:i') }}" value="{{ old('send_at') ?? $tgMailing->send_at->format('d.m.Y H:i') }}" class="mt-1 block w-full datepicker"/>
        <div class="hint">
          Если дата отправки меньше текущей, то отправка произойдет немедленно
        </div>
      </div>
      <div class="form-group">
        <x-primary-button>Сохранить</x-primary-button>
        <div class="hint">
          После сохранения рассылка запланируется на указанное время
        </div>
      </div>
    </form>
  </div>
</x-admin-layout>

