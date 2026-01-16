<form action="{{ $attributes['action'] }}" method="POST" enctype="multipart/form-data">
  @csrf
  <div class="mb-6">
    <div class="field_file mt-1 flex justify-between items-center">
      <input type="file" name="user_image" id="user_image" class="inputfile hidden" accept=".jpg, .jpeg, .png" data-multiple-caption="{count}" />
      <label for="user_image" class="flex-shrink-0 w-12 h-12 flex items-center justify-center bg-white rounded-full border border-myGray cursor-pointer overflow-hidden">
        @if(!empty($user->img))
          <span class="file_thumb"><img src="{{ storageToAsset($user->img) }}" alt="" class="w-12 h-12 rounded-full object-cover"></span>
        @else
          <span class="file_thumb">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user-circle" width="48" height="48" viewBox="0 0 24 24" stroke-width="1" stroke="#B2B2B2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M12 10m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                <path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855" />
              </svg>
            </span>
        @endif
      </label>
    </div>
  </div>
  <div class="mb-6">
    <x-public.order-input type="text" id="review_name" name="review_name" placeholder="Ваше имя" value="{{ auth()->user()->getShortName() }}" readonly/>
  </div>
  <div class="mb-6">
    <x-public.order-textarea id="review_text" :min="300" name="review_text" placeholder="Ваш отзыв" class="resize-none h-[200px]" value="{{ old('review_text') }}" required/>
  </div>
  <div class="mb-6">
    @include('_parts.drag_n_drop')
  </div>
  <div class="text-center">
    <x-public.primary-button type="submit" class="md:w-full max-w-[193px] md:max-w-[223px] lg:max-w-[253px] xl:max-w-[283px] md:text-2xl md:h-14">
      Оставить отзыв
    </x-public.primary-button>
  </div>
</form>
