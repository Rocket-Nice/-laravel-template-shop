<x-light-layout>
  <div class="px-3 lg:max-w-[1000px] mx-auto rounded-lg overflow-hidden border w-full mb-4 relative video item-square">
  <div id="30aa912e10d77ed88eca44eaf5846324" class="rutube"></div>
  </div>
  <form id="file-upload-form" class="uploader" method="POST">
    @csrf
    @include('_parts.drag_n_drop')
    <input id="file-upload" type="file" name="image" accept="image/*" multiple hidden/>
    <label id="file-drag" for="file-upload" style="display: block;width: 300px; height: 200px; border: 2px dashed #ccc">
      Нажмите здесь или перетащите изображения для загрузки
    </label>
    <button type="submit">Отправить</button>
  </form>
  <div class="ml-2 sm:whitespace-nowrap text-20 text-22 text-24
  lg:pb-10
lg:py-10
lg:pb-8
h-[2px]
sm:flex-row
lg:flex-row pl-[15px] pl-[10px]
xl:order-1
xl:order-2
xl:order-3
xl:order-4
xl:order-5
xl:order-6
xl:order-7
xl:order-8
xl:order-9
xl:order-10
xl:max-w-[350px] min-h-[174px] grid-flow-row grid-rows-[76px,174px,174px,174px,174px] xl:grid min-h-[160px] xl:block xl:hidden xl:grid-cols-3 xl:gap-x-5 sm:-mt-[1px] xl:max-w-[400px] xl:leading-[1.15] pb-2 pl-[6px] pr-2 pt-[7px] gap-[10px]
max-w-[457px]
xl:mb-[2px] xl:order-1 xl:order-2 xl:order-3 xl:order-4 xl:order-5 xl:items-center -mt-[1px] xl:gap-16
xl:grid xl:grid-cols-2 xl:mx-0 sm:px-0 xl:mb-9 xl:mb-[34px] xl:px-9 xl:px-44
lg:justify-between lg:pt-0 md:pt-0 md:px-0 sm:px-0 lg:mb-6 xl:mb-8 gap-5 xl:gap-8
lg:w-2/4 md:max-w-[500px] lg:max-w-[700px] xl:flex-row lg:gap-7 xl:gap-12 md:max-w-[300px] md:max-w-[500px] lg:max-w-[700px] md:max-w-[700px] lg:max-w-[900px]
lg:gap-14 mb-4 lg:mb-8 xl:mb-10 h-px bg-myGreenLimitter mx-auto w-full max-w-[186px] my-4 py-8 font-medium text-xl xl:text-[24px] leading-1.4 text-center px-[16px] text-myDark w-full sm:max-w-[400px] lg:max-w-[450px] xl:max-w-[524px] mx-auto"></div>
  <script>
    let fileInput = document.getElementById('file-upload');
    let fileDrag = document.getElementById('file-drag');

    fileDrag.addEventListener('dragover', (event) => {
      event.preventDefault();
      event.stopPropagation();
      fileDrag.style.color = 'green';
    }, false);

    fileDrag.addEventListener('dragleave', (event) => {
      event.preventDefault();
      event.stopPropagation();
      fileDrag.style.color = 'black';
    }, false);

    fileDrag.addEventListener('drop', (event) => {
      event.preventDefault();
      event.stopPropagation();
      fileInput.files = event.dataTransfer.files;
      uploadFiles(fileInput.files);
    }, false);

    fileInput.addEventListener('change', () => {
      uploadFiles(fileInput.files);
    });

    function uploadFiles(files) {
      Array.from(files).forEach(file => {
        let formData = new FormData();
        formData.append('image', file);

        fetch('/upload', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: formData
        })
          .then(response => response.json())
          .then(data => console.log(data))
          .catch(error => console.error(error));
      });
    }
  </script>
  <div class="xl:px-24 hidden lg:block lg:hidden lg:mb-2 lg:max-w-[355px] lg:my-8 lg:pb-2 lg:text-2xl lg:px-28 lg:gap-8 lg:pb-0 lg:gap-3 pb-5 lg:pb-6 lg:py-6 xl:py-8 lg:uppercase lg:gap-11 lg:mt-8 gap-6 md:px-0 sm:pb-4 lg:pb-5 xl:pb-6 2xl:pb-7"></div>
</x-light-layout>
