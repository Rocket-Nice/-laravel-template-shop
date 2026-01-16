@section('title', $seo['title'] ?? config('app.name'))
<x-app-layout>
  <main>
    @if(isset($category->data['image']['size']))
      <div class="w-full object-cover max-w-[520px] mx-auto">
        <input type="hidden" data-id="productionImage" class="json-image"
               value="{{ e(json_encode($category->data['image']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover" data-alt="{{ $category->name }}">
      </div>
    @endif
    <a
      href="{{ route('blog.index') }}"
      class="flex justify-center max-w-[520px] mx-auto my-6 flex items-center gap-2 border-2 border-myGreen px-[18px] py-2 text-2xl font-medium uppercase leading-1.6 text-center"
    >
      <svg width="25" height="25" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.07 18.57 4 12.5l6.07-6.07M21 12.5H4.17" stroke="#6C715C" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/></svg>
      <p>читать все новости</p>
    </a>
    <section class="py-6 sm:py-8 lg:mb-6">
      <div class="mx-auto h-[1px] max-w-[132px] bg-myBrown"></div>
      <h3
        class="py-8 text-center text-[18px] md:text-xl lg:text-2xl uppercase"
      >Раздел {{ $category->name }}</h3>
      <div class="mx-auto h-[1px] max-w-[186px] bg-myBrown"></div>
    </section>
      @foreach($articles as $article)
        <article class="mb-12">
          <div class="relative max-w-[520px] mx-auto mb-4 px-2">
            @if(isset($article->data_title['image']['size']))
              <div>
                <input type="hidden" data-id="productionImage" class="json-image"
                       value="{{ e(json_encode($article->data_title['image']['size'], JSON_UNESCAPED_UNICODE)) }}" data-picture-class="block object-cover" data-alt="{{ $article->title }}">
              </div>
            @endif
          </div>
          <div class="flex flex-col gap-5 px-2 max-w-[520px] mx-auto">
            <h3 class="font-light text-[32px] leading-none">
              {!! nl2br($article->title) !!}
            </h3>
            @if($article->data_title['short_description'] ?? false)
              <div class="italic font-semibold text-xl leading-tight text-myBrown">
                {!! $article->data_title['short_description'] !!}
              </div>
            @endif
            <a href="{{ route('blog.article', $article->slug) }}"
               class="block text-center w-full font-medium text-xl leading-none mx-auto py-3 border border-solid border-black"
            >
              Читать
            </a>
          </div>
        </article>
      @endforeach
  </main>
</x-app-layout>
