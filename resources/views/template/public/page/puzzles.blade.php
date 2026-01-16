@section('title', $seo['title'] ?? config('app.name'))
<x-app-layout>

  @include('_parts.public.pageTopBlock')
  <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-6 sm:py-10 md:py-14 lg:py-16 xl:py-[86px]">
    <div>
      <div class="flex justify-center items-center mb-8 md:mb-10 lg:mb-12">
        <div class="border-t border-b border-myBrown w-[124px]"></div>
        <h2 class="text-center product-headline text-myBrown mx-6 lh-base">Призы</h2>
        <div class="border-t border-b border-myBrown w-[124px]"></div>
      </div>
      <div class="text-center mt-5 sm:mt-6 md:mt-8 lg:mt-12 p-2 sm:p-4 md:py-6 md:px-6 m-text-body d-text-body">
        <div class="relative overflow-hidden transition-all duration-500 ease-in-out collapsibleBlock"
             data-button-id="toggleButton" data-lines="6" data-ellipsis=" " id="collapsibleBlock">
          @if(isset($content->text_data['prizes']))
            {!! $content->text_data['prizes'] ?? '' !!}
          @endif
        </div>
        <div class="text-center mt-4">
          <button class="text-base md:text-lg lg:text-xl font-semibold flex items-center mx-auto" id="toggleButton"
                  data-open-text="Развернуть" data-close-text="Свернуть">
            <span class="text">Развернуть</span>
            <svg width="16" height="16" class="ml-2" viewBox="0 0 16 16" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
              <g filter="url(#filter0_b_1918_1487)">
                <path d="M3 5.96202L8 10.916L13 5.96202L12.1125 5.08268L8 9.15735L3.8875 5.08268L3 5.96202Z"
                      fill="#000"/>
              </g>
              <defs>
                <filter id="filter0_b_1918_1487" x="-32" y="-32" width="80" height="80" filterUnits="userSpaceOnUse"
                        color-interpolation-filters="sRGB">
                  <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                  <feGaussianBlur in="BackgroundImageFix" stdDeviation="16"/>
                  <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_1918_1487"/>
                  <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_1918_1487" result="shape"/>
                </filter>
              </defs>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>

{{--  @if(!auth()->check())--}}
{{--    <div class="p-0">--}}
{{--      <div class="px-2 md:px-4 py-3 bg-myGreen flex-1 flex items-center justify-center">--}}
{{--        <div class="flex items-center relative">--}}
{{--          <a href="javascript:;" data-fancybox-no-close-btn data-src="#authForm"--}}
{{--             class="outline-none absolute block z-10 left-0 top-0 right-0 bottom-0 w-full h-full"></a>--}}
{{--          <div class="bg-myCream rounded-2xl py-6 px-3 mr-5">--}}
{{--            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">--}}
{{--              <path--}}
{{--                d="M12.2363 4C11.4783 4 10.7863 4.42747 10.4473 5.10547L10.0527 5.89453C9.71373 6.57153 9.02067 7 8.26367 7H4C2.895 7 2 7.895 2 9V22C2 23.105 2.895 24 4 24H26C27.105 24 28 23.105 28 22V9C28 7.895 27.105 7 26 7H21.7363C20.9783 7 20.2863 6.57253 19.9473 5.89453L19.5527 5.10547C19.2137 4.42847 18.5207 4 17.7637 4H12.2363ZM6 5C5.448 5 5 5.448 5 6H8C8 5.448 7.552 5 7 5H6ZM15 8C18.86 8 22 11.14 22 15C22 18.86 18.86 22 15 22C11.14 22 8 18.86 8 15C8 11.14 11.14 8 15 8ZM24 9C24.552 9 25 9.448 25 10C25 10.552 24.552 11 24 11C23.448 11 23 10.552 23 10C23 9.448 23.448 9 24 9ZM15 10C13.6739 10 12.4021 10.5268 11.4645 11.4645C10.5268 12.4021 10 13.6739 10 15C10 16.3261 10.5268 17.5979 11.4645 18.5355C12.4021 19.4732 13.6739 20 15 20C16.3261 20 17.5979 19.4732 18.5355 18.5355C19.4732 17.5979 20 16.3261 20 15C20 13.6739 19.4732 12.4021 18.5355 11.4645C17.5979 10.5268 16.3261 10 15 10Z"--}}
{{--                fill="#2C2E35"/>--}}
{{--            </svg>--}}
{{--          </div>--}}
{{--          <div class="text-xl uppercase font-medium">ЗАГРУЗИТЬ ФОТО КАРТИНЫ</div>--}}
{{--        </div>--}}
{{--      </div>--}}
{{--    </div>--}}
{{--  @else--}}
{{--    @if(!$puzzleImage)--}}
{{--      <div class="d-text-body m-text-body text-center">--}}
{{--        <div>Фотографируйте пазл ровно по центру</div>--}}
{{--        <div>Фотографируйте при хорошем освещении</div>--}}
{{--        <div>Избегайте бликов, теней на фотографии</div>--}}
{{--        --}}{{--        <div>На фотографии не должно быть лишних предметов</div>--}}
{{--      </div>--}}
{{--      <div class="my-6 max-w-2xl mx-auto flex flex-wrap -m-2">--}}
{{--        <div class="p-1 w-1/2 sm:w-1/4">--}}
{{--          <img src="{{ asset('img/puzzles/1.png') }}" alt="" class="border border-myGray">--}}
{{--        </div>--}}
{{--        <div class="p-1 w-1/2 sm:w-1/4">--}}
{{--          <img src="{{ asset('img/puzzles/11.png') }}" alt="" class="border border-myGray">--}}
{{--        </div>--}}
{{--        <div class="p-1 w-1/2 sm:w-1/4">--}}
{{--          <img src="{{ asset('img/puzzles/12.png') }}" alt="" class="border border-myGray">--}}
{{--        </div>--}}
{{--        <div class="p-1 w-1/2 sm:w-1/4">--}}
{{--          <img src="{{ asset('img/puzzles/13.png') }}" alt="" class="border border-myGray">--}}
{{--        </div>--}}
{{--      </div>--}}
{{--      <form action="{{ route('cabinet.page.puzzle_upload') }}" method="POST" id="puzzleForm">--}}
{{--        @csrf--}}
{{--        <div class="files-area">--}}
{{--          <div x-data="dataFileDnD()" class="p-0">--}}
{{--            <div class="px-2 md:px-4 py-3 bg-myGreen flex-1 flex items-center justify-center">--}}
{{--              <div class="flex items-center relative">--}}
{{--                <input accept="image/*, .heic" type="file"--}}
{{--                       class="absolute inset-0 z-50 w-full h-full p-0 m-0 outline-none opacity-0 cursor-pointer"--}}
{{--                       @change="addFiles($event)"--}}
{{--                       @dragover="$refs.dnd.classList.add('border-blue-400'); $refs.dnd.classList.add('ring-4'); $refs.dnd.classList.add('ring-inset');"--}}
{{--                       @dragleave="$refs.dnd.classList.remove('border-blue-400'); $refs.dnd.classList.remove('ring-4'); $refs.dnd.classList.remove('ring-inset');"--}}
{{--                       @drop="$refs.dnd.classList.remove('border-blue-400'); $refs.dnd.classList.remove('ring-4'); $refs.dnd.classList.remove('ring-inset');"--}}
{{--                       title="" required/>--}}
{{--                --}}{{--          <a href="javascript:;" data-src="#upload-image" data-fancybox-no-close-btn class="outline-none absolute block z-10 left-0 top-0 right-0 bottom-0 w-full h-full"></a>--}}
{{--                <div class="bg-myCream rounded-2xl py-6 px-3 mr-5">--}}
{{--                  <svg class="iconImg" width="30" height="30" viewBox="0 0 30 30" fill="none"--}}
{{--                       xmlns="http://www.w3.org/2000/svg">--}}
{{--                    <path--}}
{{--                      d="M12.2363 4C11.4783 4 10.7863 4.42747 10.4473 5.10547L10.0527 5.89453C9.71373 6.57153 9.02067 7 8.26367 7H4C2.895 7 2 7.895 2 9V22C2 23.105 2.895 24 4 24H26C27.105 24 28 23.105 28 22V9C28 7.895 27.105 7 26 7H21.7363C20.9783 7 20.2863 6.57253 19.9473 5.89453L19.5527 5.10547C19.2137 4.42847 18.5207 4 17.7637 4H12.2363ZM6 5C5.448 5 5 5.448 5 6H8C8 5.448 7.552 5 7 5H6ZM15 8C18.86 8 22 11.14 22 15C22 18.86 18.86 22 15 22C11.14 22 8 18.86 8 15C8 11.14 11.14 8 15 8ZM24 9C24.552 9 25 9.448 25 10C25 10.552 24.552 11 24 11C23.448 11 23 10.552 23 10C23 9.448 23.448 9 24 9ZM15 10C13.6739 10 12.4021 10.5268 11.4645 11.4645C10.5268 12.4021 10 13.6739 10 15C10 16.3261 10.5268 17.5979 11.4645 18.5355C12.4021 19.4732 13.6739 20 15 20C16.3261 20 17.5979 19.4732 18.5355 18.5355C19.4732 17.5979 20 16.3261 20 15C20 13.6739 19.4732 12.4021 18.5355 11.4645C17.5979 10.5268 16.3261 10 15 10Z"--}}
{{--                      fill="#2C2E35"/>--}}
{{--                  </svg>--}}
{{--                  <svg xmlns="http://www.w3.org/2000/svg"--}}
{{--                       class="iconLoader icon icon-tabler icon-tabler-loader hidden w-6 h-6 mr-1 text-current-50"--}}
{{--                       width="24" height="24" viewBox="0 0 24 24" stroke-width="1" stroke="#2c3e50" fill="none"--}}
{{--                       stroke-linecap="round" stroke-linejoin="round">--}}
{{--                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>--}}
{{--                    <path d="M12 6l0 -3"/>--}}
{{--                    <path d="M16.25 7.75l2.15 -2.15"/>--}}
{{--                    <path d="M18 12l3 0"/>--}}
{{--                    <path d="M16.25 16.25l2.15 2.15"/>--}}
{{--                    <path d="M12 18l0 3"/>--}}
{{--                    <path d="M7.75 16.25l-2.15 2.15"/>--}}
{{--                    <path d="M6 12l-3 0"/>--}}
{{--                    <path d="M7.75 7.75l-2.15 -2.15"/>--}}
{{--                  </svg>--}}
{{--                </div>--}}
{{--                <div class="text-xl uppercase font-medium">ЗАГРУЗИТЬ ФОТО КАРТИНЫ</div>--}}
{{--              </div>--}}
{{--            </div>--}}

{{--            <!-- Фрагмент для отображения загруженных изображений -->--}}
{{--            <div class="hidden">--}}
{{--              <template x-if="files.length > 0">--}}
{{--                <div class="grid grid-cols-2 gap-4 mt-4 md:grid-cols-6" @drop.prevent="drop($event)"--}}
{{--                     @dragover.prevent="$event.dataTransfer.dropEffect = 'move'">--}}
{{--                  <template x-for="(file, index) in files">--}}
{{--                    <div--}}
{{--                      class="relative flex flex-col items-center overflow-hidden text-center bg-gray-100 border rounded cursor-move select-none"--}}
{{--                      style="padding-top: 100%;" @dragstart="dragstart($event)" @dragend="fileDragging = null"--}}
{{--                      :class="{'border-blue-600': fileDragging == index}" draggable="true" :data-index="index">--}}
{{--                      <button class="absolute top-0 right-0 z-50 p-1 bg-white rounded-bl focus:outline-none"--}}
{{--                              type="button" @click="remove(index)">--}}
{{--                        <svg class="w-4 h-4 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none"--}}
{{--                             viewBox="0 0 24 24" stroke="currentColor">--}}
{{--                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"--}}
{{--                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>--}}
{{--                        </svg>--}}
{{--                      </button>--}}
{{--                      <template x-if="file.type.includes('image/')">--}}
{{--                        <img class="absolute inset-0 z-0 object-cover w-full h-full border-4 border-white preview"--}}
{{--                             x-bind:src="loadFile(file)"/>--}}
{{--                      </template>--}}

{{--                      <div class="absolute bottom-0 left-0 right-0 flex flex-col p-2 text-xs bg-white bg-opacity-50">--}}
{{--                        <span class="w-full font-bold text-gray-900 truncate" x-text="file.name">Loading</span>--}}
{{--                        <span class="text-xs text-gray-900" x-text="humanFileSize(file.size)">...</span>--}}
{{--                      </div>--}}

{{--                      <div class="absolute inset-0 z-40 transition-colors duration-300" @dragenter="dragenter($event)"--}}
{{--                           @dragleave="fileDropping = null"--}}
{{--                           :class="{'bg-blue-200 bg-opacity-80': fileDropping == index && fileDragging != index}">--}}
{{--                      </div>--}}
{{--                    </div>--}}
{{--                  </template>--}}
{{--                </div>--}}
{{--              </template>--}}
{{--            </div>--}}
{{--          </div>--}}
{{--        </div>--}}
{{--      </form>--}}
{{--      <div id="loadingMessage" style="background: #F6F6F6;display: none;"--}}
{{--           class="z-50 fixed left-0 top-0 right-0 bottom-0 w-full h-full bg-white/90 flex justify-center items-center d-text-body m-text-body">--}}
{{--        <div>Ждите...</div>--}}
{{--      </div>--}}
{{--      <div class="hidden">--}}
{{--        <div id="popupMessage" style="background: #F6F6F6;display: none;"--}}
{{--             class="!px-4 !py-6 sm:!py-[60px] sm:!px-6 w-full max-w-[547px] bg-white/90">--}}
{{--          <div class="flex justify-center items-center d-text-body m-text-body textContent"></div>--}}
{{--        </div>--}}
{{--      </div>--}}
{{--      <script src="https://unpkg.com/create-file-list"></script>--}}
{{--      <script>--}}
{{--        function dataFileDnD() {--}}
{{--          return {--}}
{{--            files: [],--}}
{{--            fileDragging: null,--}}
{{--            fileDropping: null,--}}
{{--            fileArea: null,--}}
{{--            form: {formData: new FormData()},--}}
{{--            humanFileSize(size) {--}}
{{--              const i = Math.floor(Math.log(size) / Math.log(1024));--}}
{{--              return (--}}
{{--                (size / Math.pow(1024, i)).toFixed(2) * 1 +--}}
{{--                " " +--}}
{{--                ["B", "kB", "MB", "GB", "TB"][i]--}}
{{--              );--}}
{{--            },--}}
{{--            remove(index) {--}}
{{--              let files = [...this.files];--}}
{{--              const [removedFile] = files.splice(index, 1);--}}
{{--              const inputFile = document.querySelector(`input[data-original="${removedFile.name}"]`);--}}
{{--              if (inputFile) {--}}
{{--                inputFile.remove();--}}
{{--              }--}}
{{--              this.files = createFileList(files);--}}

{{--              // Сбросить input элемент, чтобы позволить добавление новых файлов--}}
{{--              document.querySelector('input[type="file"]').value = '';--}}
{{--            },--}}
{{--            drop(e) {--}}
{{--              let removed, add;--}}
{{--              let files = [...this.files];--}}

{{--              removed = files.splice(this.fileDragging, 1);--}}
{{--              files.splice(this.fileDropping, 0, ...removed);--}}

{{--              this.files = createFileList(files);--}}

{{--              this.fileDropping = null;--}}
{{--              this.fileDragging = null;--}}
{{--            },--}}
{{--            dragenter(e) {--}}
{{--              let targetElem = e.target.closest("[draggable]");--}}

{{--              this.fileDropping = targetElem.getAttribute("data-index");--}}
{{--            },--}}
{{--            dragstart(e) {--}}
{{--              this.fileDragging = e.target--}}
{{--                .closest("[draggable]")--}}
{{--                .getAttribute("data-index");--}}
{{--              e.dataTransfer.effectAllowed = "move";--}}
{{--            },--}}
{{--            loadFile(file) {--}}
{{--              const preview = document.querySelectorAll(".preview");--}}
{{--              const blobUrl = URL.createObjectURL(file);--}}

{{--              preview.forEach(elem => {--}}
{{--                elem.onload = () => {--}}
{{--                  URL.revokeObjectURL(elem.src); // free memory--}}
{{--                };--}}
{{--              });--}}

{{--              return blobUrl;--}}
{{--            },--}}
{{--            addFiles(e) {--}}
{{--              const addedFiles = [...e.target.files].filter(file => file.type.includes('image/') || file.name.endsWith('.heic'));--}}
{{--              if (addedFiles.length > 0) {--}}
{{--                this.files = createFileList(addedFiles); // Заменить существующие файлы на новые--}}
{{--                this.fileArea = e.target.closest('.files-area');--}}
{{--                this.uploadFiles(addedFiles); // Загрузить новый файл на сервер--}}
{{--              }--}}
{{--            },--}}
{{--            uploadFiles(addedFiles) {--}}
{{--              const formData = new FormData();--}}

{{--              // Добавить только что добавленные файлы в formData--}}
{{--              addedFiles.forEach(file => formData.append('files[]', file));--}}

{{--              // Использовать fetch API для отправки файлов на сервер--}}
{{--              const iconImg = this.fileArea.querySelector('.iconImg');--}}
{{--              const iconLoader = this.fileArea.querySelector('.iconLoader');--}}

{{--              iconImg.classList.add('hidden');--}}
{{--              iconLoader.classList.remove('hidden');--}}
{{--              this.fileArea.style.pointerEvents = 'none'--}}
{{--              window.setFormDisabled(this.fileArea.closest('form'), true)--}}
{{--              fetch('/upload', {--}}
{{--                method: 'POST',--}}
{{--                body: formData,--}}
{{--                headers: {--}}
{{--                  // Здесь должен быть ваш CSRF токен и другие заголовки, если это необходимо--}}
{{--                },--}}
{{--              })--}}
{{--                .then(response => {--}}
{{--                  iconImg.classList.remove('hidden');--}}
{{--                  iconLoader.classList.add('hidden');--}}
{{--                  this.fileArea.style.pointerEvents = ''--}}
{{--                  // this.fileArea.closest('form').querySelector('button[type="submit"]').classList.remove('hidden')--}}
{{--                  window.setFormDisabled(this.fileArea.closest('form'), false)--}}
{{--                  if (!response.ok) {--}}
{{--                    throw response;--}}
{{--                  }--}}
{{--                  return response.json();--}}
{{--                })--}}
{{--                .then(data => {--}}
{{--                  const form = this.fileArea.closest('form')--}}
{{--                  const files = data.files;--}}
{{--                  if (form) {--}}
{{--                    let inputs = form.querySelectorAll('input[name="files[]"]')--}}
{{--                    inputs.forEach(element => {--}}
{{--                      element.remove();--}}
{{--                    });--}}
{{--                    files.forEach((filenames) => {--}}
{{--                      const input = document.createElement('input')--}}
{{--                      input.type = 'hidden'--}}
{{--                      input.name = 'files[]'--}}
{{--                      input.value = filenames[0]--}}
{{--                      input.dataset.original = filenames[1]--}}
{{--                      form.appendChild(input)--}}
{{--                    })--}}

{{--                    const event = new Event('submit', {bubbles: true, cancelable: true});--}}
{{--                    if (!form.dispatchEvent(event)) {--}}
{{--                      // Если событие было отменено, вывести сообщение--}}
{{--                      console.log('Submit event was canceled.');--}}
{{--                    }--}}
{{--                  }--}}
{{--                  console.log(this.files)--}}
{{--                })--}}
{{--                .catch(error => {--}}
{{--                  console.error('Error:', error);--}}
{{--                  this.files = []; // Очистить список файлов после ошибки--}}
{{--                  if (error.json) {--}}
{{--                    error.json().then(errorData => {--}}
{{--                      console.error('Error data:', errorData);--}}
{{--                    });--}}
{{--                  }--}}
{{--                });--}}
{{--            }--}}
{{--          };--}}
{{--        }--}}
{{--      </script>--}}
{{--    @else--}}
{{--      <div class="d-text-body m-text-body text-center">--}}
{{--        <div>Ваш пазл принят, спасибо!</div>--}}
{{--        --}}{{--        <div>Ваше место в очереди: <span class="cormorantInfant">{{ $puzzleImage->member_id }}</span></div>--}}
{{--        @if($prize)--}}
{{--          <div>Ваш номер: {{ $prize['order'] }}.</div>--}}
{{--          <div>Ваш подарок: {{ $prize['name'] }}</div>--}}
{{--          <div>‼️Изучите условия получение призов‼️</div>--}}
{{--        @endif--}}
{{--        --}}{{--        <x-public.primary-button href="{{ route('cabinet.order.index') }}" target="_blank" class="md:w-full my-6 max-w-[357px]">Перейти в личный кабинет</x-public.primary-button>--}}
{{--      </div>--}}
{{--    @endif--}}
{{--  @endif--}}

  @if(isset($content->text_data['bottomText'])&&$content->text_data['bottomText'])
    <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-6 sm:py-10">
      <div>
        <div class="text-center mt-5 p-2 sm:p-4 md:py-6 md:px-6 m-text-body d-text-body">
          {!! $content->text_data['bottomText'] !!}
        </div>
      </div>
    </div>
  @endif
  @if(isset($prizes)&&is_array($prizes))
    <div class="px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16 py-6 sm:py-10">
      <div>
        <div class="text-center mt-5 p-2 sm:p-4 md:py-6 md:px-6 m-text-body d-text-body">
          @foreach($prizes as $prize)
            @if(!isset($prize['member']['fio'])||!$prize['member']['fio']||in_array($prize['order'], [40, 62, 67, 68,69,72,73,75,76,77,78,79,80,81,82,83,84,85,86,87,88,90,91,92,93,94,95,96,97,98,99,100]))
              @if($prize['order']==40)
                {{ $prize['order'] }}. Растрыгина Елизавета - {{ $prize['name'] }}<br/>
              @endif
              @if($prize['order']==62)
                {{ $prize['order'] }}. Лопина Анна - {{ $prize['name'] }}<br/>
              @endif
              @if($prize['order']==67)
                {{ $prize['order'] }}. Косырина Арина - {{ $prize['name'] }}<br/>
              @endif
              @if($prize['order']==68)
                {{ $prize['order'] }}. Яшма Дарья - {{ $prize['name'] }}<br/>
              @endif
              @if($prize['order']==69)
                {{ $prize['order'] }}. Лебедева Марина - {{ $prize['name'] }}<br/>
              @endif
              @if($prize['order']==72)
                {{ $prize['order'] }}. Сазанова Людмила - {{ $prize['name'] }}<br/>
              @endif
              @if($prize['order']==73)
                {{ $prize['order'] }}. Глазова Анна - {{ $prize['name'] }}<br/>
              @endif
              @if($prize['order']==75)
                {{ $prize['order'] }}. Ковалева Александра - {{ $prize['name'] }}<br/>
              @endif
              @if($prize['order']==76)
                {{ $prize['order'] }}. Воронцова Ольга - {{ $prize['name'] }}<br/>
              @endif
              @if($prize['order']==77)
                {{ $prize['order'] }}. Громова Наталья - {{ $prize['name'] }}<br/>
              @endif
              @if($prize['order']==78)
                {{ $prize['order'] }}. Журавлева Светлана - {{ $prize['name'] }}<br/>
              @endif
              @if($prize['order']==79)
                {{ $prize['order'] }}. Киселева Ирина - {{ $prize['name'] }}<br/>
              @endif
              @if($prize['order']==80)
                {{ $prize['order'] }}. Сазонова Вероника - {{ $prize['name'] }}<br/>
              @endif
              @if($prize['order']==81)
                {{ $prize['order'] }}. Чернова Мария - {{ $prize['name'] }}<br/>
              @endif
              @if($prize['order']==82)
                {{ $prize['order'] }}. Соколова Елена - {{ $prize['name'] }}<br/>
              @endif
              @if($prize['order']==83)
                {{ $prize['order'] }}. Румянцева Виктория - {{ $prize['name'] }}<br/>
              @endif
              @if($prize['order']==84)
                {{ $prize['order'] }}. Мельникова Анна - {{ $prize['name'] }}<br/>
              @endif
              @if($prize['order']==85)
                {{ $prize['order'] }}. Кадацкая Дарья - {{ $prize['name'] }}<br/>
              @endif
              @if($prize['order']==86)
                {{ $prize['order'] }}. Разумова Ирина - {{ $prize['name'] }}<br/>
              @endif
              @if($prize['order']==87)
                {{ $prize['order'] }}. Юдина Елизавета - {{ $prize['name'] }}<br/>
              @endif
              @if($prize['order']==88)
                {{ $prize['order'] }}. Баранова Наталья - {{ $prize['name'] }}<br/>
              @endif
              @if($prize['order']==90)
                {{ $prize['order'] }}. Кузнецова Алина - {{ $prize['name'] }}<br/>
              @endif
              @if($prize['order']==91)
                {{ $prize['order'] }}. Белова Ксения - {{ $prize['name'] }}<br/>
              @endif
              @if($prize['order']==92)
                {{ $prize['order'] }}. Федорова Полина - {{ $prize['name'] }}<br/>
              @endif
              @if($prize['order']==93)
                {{ $prize['order'] }}. Никифорова Анастасия - {{ $prize['name'] }}<br/>
              @endif
              @if($prize['order']==94)
                {{ $prize['order'] }}. Васильева Диана - {{ $prize['name'] }}<br/>
              @endif
              @if($prize['order']==95)
                {{ $prize['order'] }}. Тихонова Светлана - {{ $prize['name'] }}<br/>
              @endif
              @if($prize['order']==96)
                {{ $prize['order'] }}. Григорьева Дарья - {{ $prize['name'] }}<br/>
              @endif
              @if($prize['order']==97)
                {{ $prize['order'] }}. Ефимова Екатерина - {{ $prize['name'] }}<br/>
              @endif
              @if($prize['order']==98)
                {{ $prize['order'] }}. Мартынова Валерия - {{ $prize['name'] }}<br/>
              @endif
              @if($prize['order']==99)
                {{ $prize['order'] }}. Лазарева Александра - {{ $prize['name'] }}<br/>
              @endif
              @if($prize['order']==100)
                {{ $prize['order'] }}. Семенова Ольга - {{ $prize['name'] }}<br/>
              @endif
              @continue
            @endif
              {{ $prize['order'] }}. {{ $prize['member']['fio'] }} - {{ $prize['name'] }}<br/>
          @endforeach
        </div>
      </div>
    </div>

  @endif
  <div class="hidden">
    <div id="upload-image" class="!px-4 !py-6 sm:!py-[60px] sm:!px-6 w-full max-w-[547px]" style="display: none">
      <div class="flex items-start justify-between">
        <h4 class="d-headline-4 m-headline-3 leading-none lh-outline-none">Загрузка изображений будет доступна
          позже</h4>
        <button class="outline-none shrink-0" onclick="Fancybox.close()" tabindex="-1"><img
            src="{{ asset('img/icons/close-circle.svg') }}" alt="" class="w-6 h-6"></button>
      </div>
      {{--    <div class="p-6 d-text-body m-text-body text-center">--}}
      {{--      <a href="javascript:;" data-fancybox-no-close-btn data-src="#authForm" class="w-full md:text-2xl md:h-14 h-11 inline-flex items-center justify-center px-3 md:px-4 px-7 border border-black text-xl leading-none font-medium">--}}
      {{--        Войти в личный кабинет--}}
      {{--      </a>--}}
      {{--    </div>--}}
    </div>
  </div>
  <div class="border-t border-t-myGreen w-[240px] md:w-full mx-auto max-w-3xl"></div>
</x-app-layout>
