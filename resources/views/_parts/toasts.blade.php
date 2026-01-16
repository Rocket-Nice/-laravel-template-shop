@if($errors->any()||session('success')||session('status')||session('warning'))
  <div class="fixed bottom-0 right-0 m-6 space-y-2 max-w-xl">
    @if($errors->any())
      @foreach($errors->all() as $error)
        <div class="toast-item bg-red-600 text-white rounded py-2 px-3 shadow-md mb-2">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="font-bold">Ошибка</h3>
              <p class="text-sm">{!! $error !!}</p>
            </div>
            <button class="text-white" tabindex="0" data-event="close">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>
      @endforeach
    @endif
    @if(session('success'))
        <div class="toast-item bg-green-600 text-white rounded py-2 px-3 shadow-md mb-2">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="font-bold">Успех</h3>
              <p class="text-sm">{{ session('success') }}</p>
            </div>
            <button class="text-white" tabindex="0" data-event="close">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>
    @endif
      @if(session('warning'))
        <div class="toast-item bg-yellow-500 text-white rounded py-2 px-3 shadow-md mb-2">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="font-bold">Внимание</h3>
              <p class="text-sm">{{ session('warning') }}</p>
            </div>
            <button class="text-black" tabindex="0" data-event="close">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>
      @endif
      @if(session('status'))
        <div class="toast-item bg-blue-600 text-white rounded py-2 px-3 shadow-md mb-2">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="font-bold">Статус</h3>
              <p class="text-sm">{{ session('status') }}</p>
            </div>
            <button class="text-black" tabindex="0" data-event="close">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>
      @endif

  </div>
@endif
