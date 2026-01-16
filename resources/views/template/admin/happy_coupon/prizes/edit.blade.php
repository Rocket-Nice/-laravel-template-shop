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
  <form action="{{ route('admin.prizes.update', $prize->id) }}" method="post">
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
        </nav>
      </div>
    </div>

    <div class="mx-auto px-2 sm:px-3 lg:px-4 py-6" id="tab-content">
      <div id="tab-1-content" role="tabpanel">
        <div class="sm:w-[50%]">
          <div class="form-group">
            <x-input-label for="prizeImg" class="mb-2" :value="__('Изображение в каталоге')" />
            <div class="flex items-end space-x-3 mb-1 p-2 border-gray-300 rounded-md border">
              <div id="lfm-preview-prizeImg" class="rounded-md overflow-hidden w-20" data-name="options">
                @if(isset($prize->options['img']))
                  <input type="hidden" name="options[img]" id="input-lfm-prizeImg" value="{{ old('options')['img'] ?? $prize->options['img'] ?? '' }}">
                  <input type="hidden" name="options[thumb]" id="input-lfm-thumb-prizeImg" value="{{ old('options')['thumb'] ?? $prize->options['thumb'] ?? '' }}">

                  <a href="javascript:;" data-fancybox="true" data-src="{{ $prize->options['img'] }}" style="display: block;">
                    <img src="{{ $prize->options['thumb'] }}" class="overflow-hidden max-w-full object-cover object-center">
                  </a>
                @endif
              </div>
              <div class="mb-2">
                <div class="mb-2 text-gray-500 text-sm"></div>
                <button
                  type="button"
                  id="prizeImg"
                  class="button button-secondary"
                  data-lfm="image"
                  data-preview="lfm-preview-prizeImg">Выбрать изображение</button>
              </div>
            </div>
          </div>
          <div class="form-group">
            <x-input-label for="name" :value="__('Наименование')" />
            <x-text-input type="text" name="name" id="name" value="{{ old('name') ?? $prize->name }}" class="mt-1 block w-full" required />
          </div>
          <div class="form-group">
            <x-input-label for="total" :value="__('Общее количество')" />
            <x-text-input type="text" name="total" id="total" value="{{ old('total') ?? $prize->total  }}" class="mt-1 block w-full" />
          </div>
          <div class="form-group">
            <x-input-label for="product" :value="__('Товар из магазина')"/>
            <select id="product" name="product_id" class="form-control w-full">
              <option value="">Выбрать</option>
              @foreach($products as $product)
                <option value="{{ $product->id }}" @if($prize->product_id == $product->id){!! 'selected' !!}@endif>{{ $product->name }} ({{ $product->sku }})</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <div class="flex items-center">
              <x-checkbox id="active" name="active" value="1"
                          :checked="$prize->active ? true : false"/>
              <x-input-label for="active" class="ml-2" :value="__('Включен')"/>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="px-3 sm:px-4 lg:px-6 py-6 flex justify-end">
      <x-primary-button>Сохранить</x-primary-button>
    </div>
  </form>
</x-admin-layout>
