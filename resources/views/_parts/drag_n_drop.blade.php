<div class="bg-white w-full mx-auto" id="files-area">
  <div x-data="dataFileDnD()" class="relative flex flex-col p-4 text-gray-400 border border-myGray">
    <div x-ref="dnd"
         class="relative flex flex-col text-gray-400 border border-gray-200 border-dashed cursor-pointer">
      <input accept="image/*" type="file" multiple
             class="absolute inset-0 z-50 w-full h-full p-0 m-0 outline-none opacity-0 cursor-pointer"
             @change="addFiles($event)"
             @dragover="$refs.dnd.classList.add('border-blue-400'); $refs.dnd.classList.add('ring-4'); $refs.dnd.classList.add('ring-inset');"
             @dragleave="$refs.dnd.classList.remove('border-blue-400'); $refs.dnd.classList.remove('ring-4'); $refs.dnd.classList.remove('ring-inset');"
             @drop="$refs.dnd.classList.remove('border-blue-400'); $refs.dnd.classList.remove('ring-4'); $refs.dnd.classList.remove('ring-inset');"
             title="" />

      <div class="flex flex-col items-center justify-center py-5 text-center">
        <svg id="iconImg" class="icon-img w-6 h-6 mr-1 text-current-50" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <svg id="iconLoader" xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-loader hidden mx-auto w-6 h-6 mr-1 text-current-50" width="24" height="24" viewBox="0 0 24 24" stroke-width="1" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
          <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
          <path d="M12 6l0 -3" />
          <path d="M16.25 7.75l2.15 -2.15" />
          <path d="M18 12l3 0" />
          <path d="M16.25 16.25l2.15 2.15" />
          <path d="M12 18l0 3" />
          <path d="M7.75 16.25l-2.15 2.15" />
          <path d="M6 12l-3 0" />
          <path d="M7.75 7.75l-2.15 -2.15" />
        </svg>
        <p class="m-0 d-text-body m-text-body">Добавить изображения</p>
      </div>
    </div>

    <!-- Фрагмент для отображения загруженных изображений -->
    <template x-if="files.length > 0">
      <div class="grid grid-cols-2 gap-4 mt-4 md:grid-cols-6" @drop.prevent="drop($event)"
           @dragover.prevent="$event.dataTransfer.dropEffect = 'move'">
        <template x-for="(file, index) in files">
          <div class="relative flex flex-col items-center overflow-hidden text-center bg-gray-100 border rounded cursor-move select-none"
               style="padding-top: 100%;" @dragstart="dragstart($event)" @dragend="fileDragging = null"
               :class="{'border-blue-600': fileDragging == index}" draggable="true" :data-index="index">
            <button class="absolute top-0 right-0 z-50 p-1 bg-white rounded-bl focus:outline-none" type="button" @click="remove(index)">
              <svg class="w-4 h-4 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none"
                   viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </button>
            <template x-if="file.type.includes('image/')">
              <img class="absolute inset-0 z-0 object-cover w-full h-full border-4 border-white preview"
                   x-bind:src="loadFile(file)" />
            </template>

            <div class="absolute bottom-0 left-0 right-0 flex flex-col p-2 text-xs bg-white bg-opacity-50">
              <span class="w-full font-bold text-gray-900 truncate" x-text="file.name">Loading</span>
              <span class="text-xs text-gray-900" x-text="humanFileSize(file.size)">...</span>
            </div>

            <div class="absolute inset-0 z-40 transition-colors duration-300" @dragenter="dragenter($event)"
                 @dragleave="fileDropping = null"
                 :class="{'bg-blue-200 bg-opacity-80': fileDropping == index && fileDragging != index}">
            </div>
          </div>
        </template>
      </div>
    </template>
  </div>
</div>
<script src="https://unpkg.com/create-file-list"></script>
<script>
  function dataFileDnD() {
    return {
      files: [],
      fileDragging: null,
      fileDropping: null,
      form: { formData: new FormData() },
      humanFileSize(size) {
        const i = Math.floor(Math.log(size) / Math.log(1024));
        return (
          (size / Math.pow(1024, i)).toFixed(2) * 1 +
          " " +
          ["B", "kB", "MB", "GB", "TB"][i]
        );
      },
      remove(index) {
        let files = [...this.files];
        const [removedFile] = files.splice(index, 1);
        const inputFile = document.querySelector(`input[data-original="${removedFile.name}"]`)
        inputFile.remove();
        this.files = createFileList(files);
      },
      drop(e) {
        let removed, add;
        let files = [...this.files];

        removed = files.splice(this.fileDragging, 1);
        files.splice(this.fileDropping, 0, ...removed);

        this.files = createFileList(files);

        this.fileDropping = null;
        this.fileDragging = null;
      },
      dragenter(e) {
        let targetElem = e.target.closest("[draggable]");

        this.fileDropping = targetElem.getAttribute("data-index");
      },
      dragstart(e) {
        this.fileDragging = e.target
          .closest("[draggable]")
          .getAttribute("data-index");
        e.dataTransfer.effectAllowed = "move";
      },
      loadFile(file) {
        const preview = document.querySelectorAll(".preview");
        const blobUrl = URL.createObjectURL(file);

        preview.forEach(elem => {
          elem.onload = () => {
            URL.revokeObjectURL(elem.src); // free memory
          };
        });

        return blobUrl;
      },
      addFiles(e) {
        const addedFiles = [...e.target.files].filter(file => file.type.includes('image/'));
        this.files = addedFiles; // Предположим, что вы хотите заменить существующие файлы

        // Здесь же вызываем функцию для загрузки файлов на сервер
        this.uploadFiles(addedFiles);
      },
      uploadFiles(addedFiles) {
        const formData = new FormData();

        // Добавить только что добавленные файлы в formData
        addedFiles.forEach(file => formData.append('files[]', file));

        // Использовать fetch API для отправки файлов на сервер
        const iconImg = document.getElementById('iconImg');
        const iconLoader = document.getElementById('iconLoader');

        iconImg.classList.add('hidden');
        iconLoader.classList.remove('hidden');

        fetch('/upload', {
          method: 'POST',
          body: formData,
          headers: {
            // Здесь должен быть ваш CSRF токен и другие заголовки, если это необходимо
          },
        })
          .then(response => {
            iconImg.classList.remove('hidden');
            iconLoader.classList.add('hidden');
            if (!response.ok) {
              throw response;
            }
            return response.json();
          })
          .then(data => {
            const form = document.getElementById('files-area').closest('form')
            const files = data.files;
            if(form){
              files.forEach((filenames) => {
                const input = document.createElement('input')
                input.type = 'hidden'
                input.name = 'files[]'
                input.value = filenames[0]
                input.dataset.original = filenames[1]
                form.appendChild(input)
              })
            }
            console.log(this.files)
          })
          .catch(error => {
            console.error('Error:', error);
            this.files = []; // Очистить список файлов после ошибки
            if (error.json) {
              error.json().then(errorData => {
                console.error('Error data:', errorData);
              });
            }
          });
      }
    };
  }
</script>
