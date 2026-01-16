<x-admin-layout>
  <x-slot name="header">
    @if(isset($seo['title']))
      <h1 class="text-3xl font-semibold m-2">
        {{ $seo['title'] }}
      </h1>
    @endif
  </x-slot>
  <form action="{{ route('admin.vouchers.batch_store') }}" method="post">
    @csrf
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
            <x-input-label :value="__('Сумма сертификатов')" />
            <div class="flex items-center">
              <input type="radio" id="vouchers-amount-6" name="amount" value="500" class="form-control">
              <label for="vouchers-amount-6" class="ml-2">500 рублей</label>
            </div>
            <div class="flex items-center">
              <input type="radio" id="vouchers-amount-5" name="amount" value="1000" class="form-control">
              <label for="vouchers-amount-5" class="ml-2">1000 рублей</label>
            </div>
            <div class="flex items-center">
              <input type="radio" id="vouchers-amount-7" name="amount" value="1000n" class="form-control">
              <label for="vouchers-amount-7" class="ml-2">1000 рублей (новый)</label>
            </div>
            <div class="flex items-center">
              <input type="radio" id="vouchers-amount-8" name="amount" value="1000nn" class="form-control">
              <label for="vouchers-amount-8" class="ml-2">1000 рублей (новый вертикальный)</label>
            </div>
            <div class="flex items-center">
              <input type="radio" id="vouchers-amount-1" name="amount" value="3000" class="form-control">
              <label for="vouchers-amount-1" class="ml-2">3000 рублей</label>
            </div>
            <div class="flex items-center">
              <input type="radio" id="vouchers-amount-2" name="amount" value="5000" class="form-control">
              <label for="vouchers-amount-2" class="ml-2">5000 рублей</label>
            </div>
            <div class="flex items-center">
              <input type="radio" id="vouchers-amount-3" name="amount" value="7000" class="form-control">
              <label for="vouchers-amount-3" class="ml-2">7000 рублей</label>
            </div>
            <div class="flex items-center">
              <input type="radio" id="vouchers-amount-4" name="amount" value="10000" class="form-control">
              <label for="vouchers-amount-4" class="ml-2">10000 рублей</label>
            </div>
          </div>
          <div class="form-group">
            <x-input-label for="count" :value="__('Количество сертификатов')" />
            <x-text-input type="text" name="count" id="count" value="{{ old('count') }}" class="mt-1 block w-full numeric-field" required />
          </div>
        </div>
      </div>
    </div>
    <div class="px-3 sm:px-4 lg:px-6 py-6 flex justify-end">
      <x-primary-button>Создать сертификаты</x-primary-button>
    </div>
  </form>
</x-admin-layout>
