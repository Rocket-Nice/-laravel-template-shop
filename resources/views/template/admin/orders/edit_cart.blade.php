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

      function findKey(i, selector) {
        if (document.querySelector(`div${selector}${i}`) !== null) {
          return findKey(i + 1, selector);
        }
        return i;
      }

      const updateDynamicField = (elem, number) => {
        let inputs = elem.querySelectorAll('input'),
          labels = elem.querySelectorAll('label[for]');

        inputs.forEach((input) => {
          input.id = input.id.replace('{number}', number)
          input.name = input.name.replace('{number}', number)
          if (input.disabled) {
            input.removeAttribute('disabled')
          }
        })
        labels.forEach((label) => {
          let thisId = label.getAttribute('for');
          label.setAttribute('for', thisId.replace('{number}', number));
        })
      }

      function addSlide() {
        var option = document.querySelector('select[name="product"] option:checked');
        var item_id = option.dataset.id;
        var name = option.dataset.name;
        var model = option.dataset.model;
        var price = option.dataset.price;
        var i = findKey(document.querySelectorAll('.toggle-wrapper').length, '#toggle-content-');

        var new_slide = document.getElementById('item-donor').querySelector('.toggle-wrapper').cloneNode(true);
        updateDynamicField(new_slide, i);
        new_slide.querySelector('.product-name').innerText = name
        new_slide.querySelector('.toggle-content').id = 'toggle-content-'+i
        new_slide.querySelector('#cart-'+i+'-id').value = item_id
        new_slide.querySelector('#cart-'+i+'-name').value = name
        new_slide.querySelector('#cart-'+i+'-model').value = model
        new_slide.querySelector('#cart-'+i+'-qty').value = 1
        new_slide.querySelector('#cart-'+i+'-price').value = price
        document.querySelector('.tab-content').append(new_slide);


        const content = new_slide.querySelector('.toggle-content');
        const toggleButton = new_slide.querySelector('.toggle-button');
        const arrow = new_slide.querySelector('svg');

        // Сохраняем высоту контента для каждого блока
        let contentHeight = content.scrollHeight + "px";

        // Устанавливаем начальную высоту и overflow
        content.style.height = contentHeight;
        content.style.overflow = 'hidden';
        arrow.style.transform = 'rotate(180deg)';

        toggleButton.addEventListener('click', (event) => {
          if(content.style.height === '0px') {
            content.style.height = contentHeight;
            arrow.style.transform = 'rotate(180deg)'; // Переворачиваем стрелку вниз
          } else {
            content.style.height = '0px';
            arrow.style.transform = 'rotate(0deg)'; // Возвращаем стрелку в начальное положение
          }
        });
      }

      function remvoeSlide(elem) {
        var box = elem.closest('.toggle-wrapper');
        box.parentNode.removeChild(box);
      }


      // end ozon
    </script>
  </x-slot>
  <form action="{{ route('admin.orders.updateCart', ['order' => $order->slug]) }}" method="post">
    @csrf
    @method('PUT')

    <div class="border-b">
      <div class="mx-auto px-2 sm:px-3 lg:px-4">
        <nav class="-mb-px flex flex-nowrap flex-col sm:flex-row sm:justify-start space-y-2 sm:space-y-0 sm:space-x-4"
             aria-label="Tabs" role="tablist">
          <button type="button"
                  class="whitespace-nowrap py-2 sm:py-4 px-1 border-b-2 font-medium text-sm focus:outline-none active:border-gray-500"
                  id="tab-1" aria-selected="true" role="tab" aria-controls="tab-1-content">Данные корзины
          </button>
        </nav>
      </div>
    </div>
    <div id="item-donor" class="hidden">
      <div class="toggle-wrapper mb-2 md:mb-4 border border-gray-200 rounded-md">
        <div class="toggle-button p-4 cursor-pointer flex justify-between items-center">
          <span class="product-name"></span>
          <svg width="17" height="11" class="ml-2" viewBox="0 0 17 11" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g filter="url(#filter0_b_1943_23515)">
              <path d="M16.5 2.23962L8.5 10.166L0.5 2.23962L1.92 0.832683L8.5 7.35214L15.08 0.832682L16.5 2.23962Z" fill="black"/>
            </g>
            <defs>
              <filter id="filter0_b_1943_23515" x="-31.5" y="-31.168" width="80" height="73.334" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                <feGaussianBlur in="BackgroundImageFix" stdDeviation="16"/>
                <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_1943_23515"/>
                <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_1943_23515" result="shape"/>
              </filter>
            </defs>
          </svg>
        </div>
        <div class="toggle-content overflow-hidden transition-max-height duration-500 h-0 mx-auto max-w-[700px] txt-body px-2 md:px-0">
          <div class="p-4">
            <div class="mb-2">
              <div class="form-group">
                <label class="block font-medium text-sm text-gray-700" for="cart-{number}-id">
                  Идентификатор
                </label>
                <input readonly="" class="block w-full border-gray-300 focus:border-lemousseColor focus:ring-lemousseColor rounded-md shadow-sm" type="text" name="cart[{number}][id]" id="cart-{number}-id" placeholder="..." value="" disabled>
              </div>
              <div class="form-group">
                <label class="block font-medium text-sm text-gray-700" for="cart-{number}-qty">
                  Количество
                </label>
                <input class="block w-full border-gray-300 focus:border-lemousseColor focus:ring-lemousseColor rounded-md shadow-sm" type="text" name="cart[{number}][qty]" id="cart-{number}-qty" placeholder="..." value="" disabled>
              </div>
              <div class="form-group">
                <label class="block font-medium text-sm text-gray-700" for="cart-{number}-name">
                  Нааименование
                </label>
                <input class="block w-full border-gray-300 focus:border-lemousseColor focus:ring-lemousseColor rounded-md shadow-sm" type="text" name="cart[{number}][name]" id="cart-{number}-name" placeholder="..." value="" disabled>
              </div>
              <div class="form-group">
                <label class="block font-medium text-sm text-gray-700" for="cart-{number}-model">
                  Артикул
                </label>
                <input readonly="" class="block w-full border-gray-300 focus:border-lemousseColor focus:ring-lemousseColor rounded-md shadow-sm" type="text" name="cart[{number}][model]" id="cart-{number}-model" placeholder="..." value="" disabled>
              </div>
              <div class="form-group">
                <label class="block font-medium text-sm text-gray-700" for="cart-{number}-price">
                  Цена
                </label>
                <input class="block w-full border-gray-300 focus:border-lemousseColor focus:ring-lemousseColor rounded-md shadow-sm" type="text" name="cart[{number}][price]" id="cart-{number}-price" placeholder="..." value="" disabled>
              </div>
            </div>
            <div class="text-right">
              <button type="button" class="underline hover:no-underline" onclick="remvoeSlide(this);">Удалить</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="mx-auto px-2 sm:px-3 lg:px-4 py-6" id="tab-content">
      <div id="tab-1-content" role="tabpanel">
        <div class="sm:w-[50%]">
          @if(isset($order->data_cart))
            <div class="tab-content">
              @foreach($order->data_cart as $key => $item)
              <div class="toggle-wrapper mb-2 md:mb-4 border border-gray-200 rounded-md">
                <div class="toggle-button p-4 cursor-pointer flex justify-between items-center">
                  <span>{{ $item['name'] }}</span>
                  <svg width="17" height="11" class="ml-2" viewBox="0 0 17 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g filter="url(#filter0_b_1943_23515)">
                      <path d="M16.5 2.23962L8.5 10.166L0.5 2.23962L1.92 0.832683L8.5 7.35214L15.08 0.832682L16.5 2.23962Z" fill="black"/>
                    </g>
                    <defs>
                      <filter id="filter0_b_1943_23515" x="-31.5" y="-31.168" width="80" height="73.334" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                        <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                        <feGaussianBlur in="BackgroundImageFix" stdDeviation="16"/>
                        <feComposite in2="SourceAlpha" operator="in" result="effect1_backgroundBlur_1943_23515"/>
                        <feBlend mode="normal" in="SourceGraphic" in2="effect1_backgroundBlur_1943_23515" result="shape"/>
                      </filter>
                    </defs>
                  </svg>
                </div>
                <div id="toggle-content-{{ $key }}" class="toggle-content overflow-hidden transition-max-height duration-500 h-0 mx-auto max-w-[700px] txt-body px-2 md:px-0">
                  <div class="p-4">
                    <div class="mb-2">
                      @php
                        $item_fields = [
                            'id' => 'Идентификатор',
                            'name' => 'Нааименование',
                            'model' => 'Артикул',
                            'qty' => 'Количество',
                            'price' => 'Цена',
                        ];
                        $readonly = ['model','id'];
                      @endphp
                      @foreach($item as $field => $value)
                        @if(isset($item_fields[$field]))
                          <div class="form-group">
                            <x-input-label for="cart-{{ $key }}-{{ $field }}">{{ $item_fields[$field] }}</x-input-label>
                            <x-text-input type="text" name="cart[{{ $key }}][{{ $field }}]" id="cart-{{ $key }}-{{ $field }}" placeholder="..." value="{{ $value }}" :readonly="in_array($field, $readonly) ? true : false"/>
                          </div>
                        @else
                          <input type="hidden" name="cart[{{ $key }}][{{ $field }}]" value="{{ is_array($value) ? json_encode($value) : $value }}">
                        @endif
                      @endforeach
                      @if(isset($item['builder']))
                        <div>
                          @foreach($item['builder'] as $builder_item)
                            - {{ $builder_item['name'] }}<br/>
                          @endforeach
                        </div>
                      @endif
                    </div>
                    <div class="text-right">
                      <button type="button" class="underline hover:no-underline" onclick="remvoeSlide(this);">Удалить</button>
                    </div>
                  </div>
                </div>
              </div>
              @endforeach
            </div>
          @endif

            <div class="flex flex-wrap items-center">
              <div class="form-group !mb-0 mr-3 flex-1">
                <select name="product" class="form-control w-full">
                  @foreach($products as $product)
                    <option value="" data-id="{{ $product->id }}" data-name="{{ str_replace('"', '\'', $product->name) }}" data-model="{{ $product->sku }}" data-price="{{ $product->price }}">{{ $product->name }} ({{ $product->sku }})</option>
                  @endforeach
                </select>
              </div>
              <button type="button" class="button button-secondary text-white" id="actioncell_submit" onclick="addSlide(); return false;">Добавить</button>
            </div>
        </div>

      </div>
    </div>

    <div class="px-3 sm:px-4 lg:px-6 py-6 flex justify-end">
      <x-primary-button>Сохранить</x-primary-button>
    </div>
  </form>

</x-admin-layout>
