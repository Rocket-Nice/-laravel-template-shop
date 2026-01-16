<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <x-search-form :route="route('admin.products.reviews')">
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="date_from" :value="__('Дата от')"/>
        <x-text-input type="text" name="date_from" id="date_from" value="{{ request()->get('date_from') }}" data-minDate="false" placeholder="{{ now()->startOfMonth()->format('d.m.Y H:i') }}" class="mt-1 block w-full datepicker"/>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="date_until" :value="__('Дата до')"/>
        <x-text-input type="text" name="date_until" id="date_until" value="{{ request()->get('date_until') }}" placeholder="{{ now()->format('d.m.Y H:i') }}" data-minDate="false" class="mt-1 block w-full datepicker"/>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/3">
      <div class="form-group">
        <x-input-label for="name" :value="__('Имя')"/>
        <x-text-input type="text" name="name" id="name" value="{{ request()->get('name') }}"
                      class="mt-1 block w-full"/>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/3">
      <div class="form-group">
        <x-input-label for="email" :value="__('Email')"/>
        <x-text-input type="text" name="email" id="email" value="{{ request()->get('email') }}"
                      class="mt-1 block w-full"/>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/3">
      <div class="form-group">
        <x-input-label for="phone" :value="__('Телефон')"/>
        <x-text-input type="text" name="phone" id="phone" value="{{ request()->get('phone') }}"
                      class="mt-1 block w-full"/>
      </div>
    </div>
    <div class="p-1 w-full">
      <div class="form-group">
        <x-input-label for="filter-product" :value="__('Товар')"/>
        <select id="filter-product" name="product[]" multiple class="multipleSelect form-control">
          @foreach($products as $product)
            @if(is_array(request()->get('not_product'))&&in_array($product->sku, request()->get('not_product')))
              @continue
            @endif
            <option value="{{ $product->sku }}" @if(is_array(request()->get('product'))&&in_array($product->sku, request()->get('product'))){!! 'selected' !!}@endif>{{ $product->name }} ({{ $product->sku }})</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="p-1 w-full">
      <div class="form-group">
        <x-input-label for="reviewStatus" :value="__('Статус')"/>
        <select id="reviewStatus" name="reviewStatus" class="form-control w-full">
          <option value="">Выбрать</option>
          <option value="n" @if(request()->get('reviewStatus')&&request()->get('reviewStatus')=='n'){!! 'selected' !!}@endif>Опубликованные</option>
          <option value="y" @if(request()->get('reviewStatus')&&request()->get('reviewStatus')=='y'){!! 'selected' !!}@endif>Неопубликованные</option>
        </select>
      </div>
    </div>
  </x-search-form>
  <div class="mx-auto py-4">
    <div id="action-box" class="flex items-center flex-wrap -mx-1 hidden">
      <div class="p-1 w-full lg:w-1/3">
        <div class="form-group">
          <select name="action" form="action" class="form-control" id="do_action">
            <option>Действие с выбранными</option>
            <option value="set_status|0">Показать</option>
            <option value="set_status|1">Скрыть</option>
            <option value="remove|1">Удалить</option>
          </select>
        </div>

      </div>
      <div class="p-1 w-full lg:w-2/3 flex justify-end">
        <button class="button button-success" id="actioncell_submit" form="action">Применить</button>
      </div>
    </div>
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
        <thead>
        <tr>
          <th class="bg-gray-100 border p-2" style="width: 2%">
            <input type="checkbox" class="action" id="check_all">
          </th>
          <th class="bg-gray-100 border p-2" style="width: 5%">#</th>
          <th class="bg-gray-100 border p-2">Продукт</th>
          <th class="bg-gray-100 border p-2">Автор</th>
          <th class="bg-gray-100 border p-2">Комментарий</th>
{{--          <th class="bg-gray-100 border p-2" style="width:120px"></th>--}}
        </tr>
        </thead>
        <tbody>
        @foreach($comments as $comment)
          <tr>
            <td class="border p-2">
              <input type="checkbox" name="comments_ids[]" form="action" value="{{ $comment->id }}" class="action" id="checkbox_{{ $comment->id }}">
            </td>
            <td class="border p-2 project-actions">
              @if($comment->hidden)
                <i class="fas fa-eye-slash"></i>
              @else
                <i class="fas fa-eye text-green-600"></i>
              @endif
                {{ $comment->id }}
            </td>
            <td class="border p-2">
              <span title="{{ $comment->commentable->name ?? '' }}">{{ ($comment->commentable->id ?? '').': '.($comment->commentable->name ?? '') }}</span>
            </td>
            <td class="border p-2">
              <a href="{{ route('admin.users.edit', $comment->user_id) }}" class="whitespace-nowrap">{{ getShortName($comment->first_name, $comment->last_name) }}</a>
            </td>
            <td class="border p-2 text-left">
              <div class="flex justify-between">

{{--                <div class="flex space-x-1 mb-6" style="transform: scale(.8); transform-origin: left top;" data-toggle="tooltip" title="Качество продукта: {{ $comment->rQuality."\n" }}Аромат: {{ $comment->rAroma."\n" }}Текстура: {{ $comment->rStructure."\n" }}Эффект: {{ $comment->rEffect."\n" }}Доставка: {{ $comment->rShipping."\n" }}">--}}
{{--                  {!! getRatingStars($comment->rating) !!}--}}
{{--                </div>--}}
                <div class="text-gray-400">{{ date('d.m.Y H:i', strtotime($comment->created_at)) }}</div>
              </div>
              {{ $comment->text }}
              @if($comment->files)
                <div class="mt-6">
                  <div class="flex flex-wrap -m-2">
                    @foreach($comment->files as $index => $file)
                      <div class="w-[50px] m-2 text-xs text-center box">
                        <form action="{{ route('admin.products.reviews.image', [$comment->id, $index]) }}" method="POST" id="image-{{ $comment->id }}-{{ $index }}">
                          @csrf
                          @method('PUT')
                        </form>
                        <a href="{{ storageToAsset($file['image']) }}" data-fancybox="comment-{{ $comment->id }}" class="image block rounded border border-myGray @if($file['hidden'] ?? false) opacity-50 @endif">
                          <img src="{{ storageToAsset($file['thumb']) }}" alt="" class="block w-[50px] h-[50px] rounded">
                        </a>

                         <a href="#" class="updateReviewImage" data-form="image-{{ $comment->id }}-{{ $index }}">@if($file['hidden'] ?? false) Показать @else Скрыть @endif</a>

                      </div>
                    @endforeach
                  </div>
                </div>

              @endif
            </td>
{{--            <td class="border p-2">--}}
{{--              <a href="#answer" data-fancybox class="btn btn-default"  onclick="setComment({{ $comment->id }}, '{{ $comment->data['text'] ?? '' }}');">--}}
{{--                @if(isset($comment->data['text'])&&!empty($comment->data['text']))--}}
{{--                  <i class="fas fa-reply text-success"></i>--}}
{{--                @else--}}
{{--                  <i class="fas fa-reply"></i>--}}
{{--                @endif--}}
{{--              </a>--}}
{{--            </td>--}}
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    <div class="p-2">
      {{ $comments->appends(request()->input())->links('pagination::tailwind') }}
    </div>
  </div>
  <form action="{{ route('admin.products.reviews.update') }}" id="action" method="POST">
    @csrf
    @method('PUT')
  </form>
  <script>
    const imageLinks = document.querySelectorAll('a.updateReviewImage')
    imageLinks.forEach((link) => {
      link.addEventListener('click', function(e){
        e.preventDefault()
        const form = document.getElementById(link.dataset.form)
        const action = form.action
        window.ajax.post(action, {
          '_method': 'PUT',
          '_token': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        }, (response) => {
          if(response.success){
            let isShowing = link.textContent.trim() === 'Показать'
            link.textContent = isShowing ? 'Скрыть' : 'Показать'
            if(isShowing){
              link.closest('.box').querySelector('.image').classList.remove('opacity-50')
            }else{
              link.closest('.box').querySelector('.image').classList.add('opacity-50')
            }
          }
        })
      })
    })
  </script>
</x-admin-layout>
