@section('title', $seo['title'] ?? config('app.name'))
<x-app-layout>
  <div class="text-center">
    <div class="h-[50vh] flex flex-col justify-center items-center">
      <div>
        <h2 class="uppercase text-sm sm:text-base md:text-lg lg:text-2xl xl:text-4xl my-5 md:my-9 lg:my-12 text-customBrown text-center">{{ $message['title'] }}</h2>
        <div class="text-customBrown text-sm sm:text-base md:text-lg lg:text-xl xl:text-2xl !leading-1.8">{!! $message['text'] !!}</div>
      </div>
    </div>
  </div>

  <script>
    document.getElementById('robokassa-pay').submit();
  </script>
</x-app-layout>
