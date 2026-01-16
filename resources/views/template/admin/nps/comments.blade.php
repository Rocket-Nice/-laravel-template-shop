@section('title', $seo['title'] ?? config('app.name'))
<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
      <a href="{{ route('admin.nps.statistic') }}" class="button button-success">Оценки</a>
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
    <div class="p-1 w-full">
      <div class="form-group">
        <x-input-label for="filter-question" :value="__('Вопрос')"/>
        <select id="filter-question" name="question" class="form-control w-full">
          <option>Средняя по всем</option>
          @foreach($questions as $question)
            <option value="{{ $question->id }}" @if(request()->question == $question->id){{ 'selected' }} @endif>{{ $question->text }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="score_from" :value="__('Средняя оценка от')"/>
        <x-text-input type="text" name="score_from" id="score_from" value="{{ request()->get('score_from') }}"
                      class="mt-1 block w-full numeric-field"/>
      </div>
    </div>
    <div class="p-1 w-full lg:w-1/2">
      <div class="form-group">
        <x-input-label for="score_to" :value="__('Средняя оценка до')"/>
        <x-text-input type="text" name="score_to" id="score_to" value="{{ request()->get('score_to') }}"
                      class="mt-1 block w-full numeric-field"/>
      </div>
    </div>
    <div class="p-1 w-full">
      <div class="form-group">
        <x-input-label for="filter-status" :value="__('Статус')"/>
        <select id="filter-status" name="status" class="form-control w-full">
          <option>Выбрать</option>
          @foreach(\App\Models\NpsSurvey::STATUS as $key => $status)
            <option value="{{ $key }}" @if(request()->get('status') !== null && request()->get('status') == $key){{ 'selected' }}@endif>{{ $status }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="p-1 w-full">
      <div class="form-group">
        <x-input-label for="filter-orderBy" :value="__('Сортировка')"/>
        <select id="filter-orderBy" name="orderBy" class="form-control w-full">
          <option>Выбрать</option>
          <option value="nps_score|asc" @if(request()->get('orderBy')&&request()->get('orderBy')=='nps_score|asc')
            {!! 'selected' !!}
            @endif>Сначала низкие оценки
          </option>
          <option value="nps_score|desc" @if(request()->get('orderBy')&&request()->get('orderBy')=='nps_score|desc')
            {!! 'selected' !!}
            @endif>Сначала высокие оценки
          </option>
          <option value="created_at|asc" @if(request()->get('orderBy')&&request()->get('orderBy')=='created_at|asc')
            {!! 'selected' !!}
            @endif>Сначала старые
          </option>
          <option value="created_at|desc" @if(request()->get('orderBy')&&request()->get('orderBy')=='created_at|desc')
            {!! 'selected' !!}
            @endif>Сначала новые
          </option>
        </select>
      </div>
    </div>
  </x-admin.search-form>
  <div class="flex flex-wrap -m-1">
    @foreach($comments as $comment)
      @if(request()->question && is_numeric(request()->question))
        @php($score = $comment->score)
      @else
        @php($score = $comment->nps_score)
      @endif
      <div class="p-1 w-full">
        <div class="rounded-lg border border-gray-200 p-2">
          <div x-data="surveyLoader()" class="border-b border-gray-200 mb-3">
            <div class="flex justify-between flex-col md:flex-row space-y-2 md:space-y-0">
              <div>
                @if($score >= 9)
                  <span class="badge-green whitespace-nowrap mb-2">{{ $score }}</span>
                @elseif($score >= 7)
                  <span class="badge-yellow whitespace-nowrap mb-2">{{ $score }}</span>
                @else
                  <span class="badge-red whitespace-nowrap mb-2">{{ $score }}</span>
                @endif
                <span @click="show =! show; loadSurvey('{{ route('admin.nps.survey', $comment->user_id) }}')" class="cursor-pointer underline decoration-dashed hover:no-underline">{{ $comment->name }}</span>
              </div>
              <div class="flex justify-center md:justify-end space-x-4 items-center">
                <div class="text-gray-400">
                  {{ date('d.m.Y H:i', strtotime($comment->created_at)) }}
                </div>
                <select x-data="surveyStatus" x-init="init()" @change.prevent="setStatus('{{ route('admin.nps.status', $comment->survey_user_id) }}')" name="status" :class="color" class="block rounded-md border-0 py-1 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 sm:text-sm sm:leading-6">
                  @foreach(\App\Models\NpsSurvey::STATUS as $key => $status)
                    <option value="{{ $key }}" @if($key == $comment->status){{ 'selected' }}@endif>{{ $status }}</option>
                  @endforeach
                </select>
              </div>

            </div>
            <div x-show="show" class="border-t border-gray-200">
              {{ $comment->email }}<br/>
              {{ $comment->phone }}
              <div x-ref="survey" class="my-2"></div>
            </div>
          </div>
          {!! nl2br($comment->comment) !!}

        </div>
      </div>
    @endforeach
  </div>
  <div class="p-2">
    {{ $comments->appends(request()->input())->links('pagination::tailwind') }}
  </div>
</x-admin-layout>

