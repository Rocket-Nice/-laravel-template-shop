@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
      <a href="{{ route('admin.nps.comments') }}" class="button button-success">Комментарии</a>
    @endif
  </x-slot>

  <x-admin.search-form :route="url()->current()">
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="date_from" :value="__('Дата от')"/>
        <x-text-input type="text" name="date_from" id="date_from" value="{{ request()->get('date_from') }}"
                      data-minDate="false" placeholder="{{ now()->startOfMonth()->format('d.m.Y H:i') }}"
                      class="mt-1 block w-full datepicker"/>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="date_until" :value="__('Дата до')"/>
        <x-text-input type="text" name="date_until" id="date_until" value="{{ request()->get('date_until') }}"
                      placeholder="{{ now()->format('d.m.Y H:i') }}" data-minDate="false"
                      class="mt-1 block w-full datepicker"/>
      </div>
    </div>
  </x-admin.search-form>
  <div class="flex flex-wrap -m-1">
    @foreach($results as $key => $result)
      @php($score = round($result->average_score, 2))
      <div class="p-1 w-full lg:w-1/2">
        <div class="rounded-lg border border-gray-200 p-1 @if($score >= 9) bg-green-200 @elseif($score >= 7) bg-yellow-200 @else bg-red-200 @endif">
          <h3 class="font-bold mb-2 text-md">{{ $key }}</h3>
          <span class="font-medium">Средняя оценка:</span> {{ $score }}<br/>
          <span class="font-medium">Всего оценкок:</span> {{ $result->total_scores_count }}<br/>
          <span class="font-medium">Промоутеров:</span> {{ $result->high_scores_count }}<br/>
          <span class="font-medium">Нейтралов:</span> {{ $result->medium_scores_count }}<br/>
          <span class="font-medium">Критиков:</span> {{ $result->low_scores_count }}<br/><br/>
        </div>
      </div>

    @endforeach

  </div>

</x-admin-layout>

