<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <form action="{{ route('admin.vouchers.update', $voucher->id) }}" method="post">
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
            <x-input-label for="code" :value="__('Код')" />
            <x-text-input type="text" name="code" id="code" value="{{ old('code') ?? $voucher->code }}" class="mt-1 block w-full" required />
          </div>
          <div class="form-group">
            <x-input-label for="amount" :value="__('Сумма сертификата')" />
            <x-text-input type="text" name="amount" id="amount" value="{{ old('amount') ?? $voucher->amount }}" class="mt-1 block w-full numeric-field" required />
          </div>
          <div class="form-group">
            <x-input-label for="available_from" :value="__('Доступен с:')"/>
            <x-text-input type="text" name="available_from" id="available_from" value="{{ old('available_from') ?? $voucher->available_from?->format('d.m.Y') }}" data-minDate="false" data-timepicker="0" class="mt-1 block w-full datepicker"/>
          </div>
          <div class="form-group">
            <x-input-label for="available_until" :value="__('Доступен до:')"/>
            <x-text-input type="text" name="available_until" id="available_until" value="{{ old('available_until') ?? $voucher->available_until?->format('d.m.Y') }}" data-minDate="false" data-timepicker="0" class="mt-1 block w-full datepicker"/>
          </div>
          <div class="form-group">
            <x-input-label for="comment" :value="__('Комментарий')" />
            <x-textarea name="comment" id="comment" class="mt-1 w-full"></x-textarea>
          </div>
          <div class="form-group flex items-center">
            <x-checkbox name="save_amount" id="save_amount" data-name="button" value="1" :checked="$voucher->save_amount ? true : false"/>
            <x-input-label class="ml-2" for="save_amount" :value="__('Сохранять баланс')"/>
          </div>
        </div>
      </div>
    </div>
    <div class="px-3 sm:px-4 lg:px-6 py-6 flex justify-end">
      <x-primary-button>Обновить сертификат</x-primary-button>
    </div>
  </form>
</x-admin-layout>
