<x-app-layout>
  <div class="py-6 lg:py-12 px-2 sm:px-4 md:px-8 lg:px-14 xl:px-16">
    @if(isset($page))
      <div class="space-y-2 d-text-body m-text-body">
        {!! $page->content !!}
      </div>

    @endif
  </div>
</x-app-layout>
