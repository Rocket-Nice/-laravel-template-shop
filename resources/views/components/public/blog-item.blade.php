@props(['article' => null])
@php($getId = getCode(3))
<div class="{{ $attributes['class'] ?? 'w-1/2 lg:w-1/3 px-2 md:px-3 py-2 md:py-3 flex flex-col justify-between' }}">
   <div>
    <div>
      <div class="product-card_item relative">
        <a href="{{ route('blog.article', $article->slug) }}" class="img product_card_preview item-square block">
          @if(isset($article->data_title['image']['size']))
            <div>
              <input type="hidden" data-id="productionImage" class="json-image"
                     value="{{ e(json_encode($article->data_title['image']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover" data-alt="{{ $article->title }}">
            </div>
          @endif
        </a>
      </div>
    </div>

    <div class="mb-4 mt-4 md:mt-6 flex-1">
        <h3 class="break-words font-light text-xl sm:text-2xl md:text-3xl lg:text-32 min-h-[52px] pr-2 lh-outline-none">
          <a href="{{ route('blog.article', $article->slug) }}">{!! nl2br($article->title) !!}</a></h3>

{{--      @if($article->data_title['short_description'] ?? false)--}}
{{--          <div class="text-myBrown text-base md:text-lg lg:text-xl italic mt-3">{!! $article->data_title['short_description'] !!}</div>--}}
{{--      @endif--}}
    </div>
   </div>
  <div>
    <x-public.primary-button href="{{ route('blog.article', $article->slug) }}" class="w-full !px-2 text-center"><span class="text-base sm:text-xl whitespace-nowrap">Читать</span></x-public.primary-button>
  </div>
</div>
