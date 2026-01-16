<x-light-layout>
  <div class="p-6">
    <div id="loading"><div class="spinner"></div></div>
    <div class="p-2">
      <p><span class="font-weight-bold">Всего заказов:</span> <span id="orders_total" class="numeric">0</span></p>
      <p><span class="font-weight-bold">Количество товаров:</span> <span id="total_count" class="numeric">0</span></p>
      <p><span class="font-weight-bold">Сумма товаров:</span> <span id="total" class="numeric">0</span></p>
      <p><span class="font-weight-bold">Сумма доставки (СДЭК):</span> <span id="total_shipping_cdek" class="numeric">0</span></p>
      <p><span class="font-weight-bold">Сумма доставки (СДЭК курьер):</span> <span id="total_shipping_cdek_courier" class="numeric">0</span></p>
      <p><span class="font-weight-bold">Сумма доставки (Boxberry):</span> <span id="total_shipping_boxberry" class="numeric">0</span></p>
      <p><span class="font-weight-bold">Сумма доставки (Почта):</span> <span id="total_shipping_pochta" class="numeric">0</span></p>
      <p><span class="font-weight-bold">Сумма доставки:</span> <span id="total_shipping" class="numeric">0</span></p>
      <p><span class="font-weight-bold">Сумма скидки:</span> <span id="total_discount" class="numeric">0</span></p>
    </div>
    <div class="relative overflow-x-auto min-h-[500px]">
      <table class="table-auto w-full text-center border-collapse border border-gray-200 rounded-md text-sm">
        <thead>
        <tr>
          <th class="bg-gray-100 border p-2">#</th>
          <th class="bg-gray-100 border p-2">Наименоание</th>
          <th class="bg-gray-100 border p-2">Артикул</th>
          <th class="bg-gray-100 border p-2">Продано шт.</th>
          <th class="bg-gray-100 border p-2">Продано на сумму</th>
        </tr>
        </thead>
        <tbody id="cart">
        </tbody>
      </table>
    </div>
  </div>
  <script>
    function getData(url, page = 1) {
      window.ajax.get(url, { page: page }, function(response) {
        // Если все нормально
        if (response.total > 0) {
          var totalElem = document.getElementById('total');
          var total = Number(totalElem.textContent) + Number(response.total);
          totalElem.textContent = total;
        }

        if (Number.isInteger(response.total_count) && response.total_count > 0) {
          var totalCountElem = document.getElementById('total_count');
          var total_count = Number(totalCountElem.textContent) + response.total_count;
          totalCountElem.textContent = total_count;
        }

        if (response.total_discount > 0) {
          var totalDiscountElem = document.getElementById('total_discount');
          var total_discount = Number(totalDiscountElem.textContent) + response.total_discount;
          totalDiscountElem.textContent = total_discount;
          console.log(total_discount);
        }

        if (response.total_shipping.total > 0) {
          var totalShippingElem = document.getElementById('total_shipping');
          var total_shipping = Number(totalShippingElem.textContent) + response.total_shipping.total;
          totalShippingElem.textContent = total_shipping;
        }

        if (Number.isInteger(response.orders_total) && response.orders_total > 0) {
          var ordersTotalElem = document.getElementById('orders_total');
          ordersTotalElem.textContent = response.orders_total;
        }


        if (Number.isInteger(response.total_shipping.boxberry) && response.total_shipping.boxberry > 0) {
          var totalShippingBoxberryElem = document.getElementById('total_shipping_boxberry');
          var total_shipping_boxberry = Number(totalShippingBoxberryElem.textContent) + response.total_shipping.boxberry;
          totalShippingBoxberryElem.textContent = total_shipping_boxberry;
        }

        if (Number.isInteger(response.total_shipping.cdek) && response.total_shipping.cdek > 0) {
          var totalShippingCdekElem = document.getElementById('total_shipping_cdek');
          var total_shipping_cdek = Number(totalShippingCdekElem.textContent) + response.total_shipping.cdek;
          totalShippingCdekElem.textContent = total_shipping_cdek;
        }

        if (Number.isInteger(response.total_shipping.cdek_courier) && response.total_shipping.cdek_courier > 0) {
          var totalShippingCdekCourierElem = document.getElementById('total_shipping_cdek_courier');
          var total_shipping_cdek_courier = Number(totalShippingCdekCourierElem.textContent) + response.total_shipping.cdek_courier;
          totalShippingCdekCourierElem.textContent = total_shipping_cdek_courier;
        }

        if (response.total_shipping.pochta > 0) {
          var totalShippingPochtaElem = document.getElementById('total_shipping_pochta');
          var total_shipping_pochta = Number(totalShippingPochtaElem.textContent) + response.total_shipping.pochta;
          totalShippingPochtaElem.textContent = total_shipping_pochta;
        }

        // ... и так далее для всех других полей

        var statistic = response.statistic;
        for (var key in statistic) {
          var elem = statistic[key];
          var rowElem = document.getElementById(key);
          if (rowElem) {
            var countElem = document.getElementById(key + '_count');
            var count = Number(countElem.textContent) + elem.count;
            countElem.textContent = count;
            countElem.closest('td').setAttribute('data-sort', count);

            var totalElem = document.getElementById(key + '_total');
            var total = Number(totalElem.textContent) + elem.total;
            totalElem.textContent = total;
            totalElem.closest('td').setAttribute('data-sort', total);
          } else {
            var html = `
                    <tr id="${key}">
                        <td class="border p-2">${elem.id}</td>
                        <td class="border p-2">${elem.name}</td>
                        <td class="border p-2">${key}</td>
                        <td class="border p-2" data-sort="${elem.count}">
                            <span id="${key}_count" class="numeric">${elem.count}</span>
                        </td>
                        <td class="border p-2" data-sort="${elem.total}">
                            <span id="${key}_total" class="numeric">${elem.total}</span>
                        </td>
                    </tr>
                `;
            document.getElementById('cart').insertAdjacentHTML('beforeend', html);
          }
        }

        if (response.this_page < response.last_page) {
          // Для URL-адресов, использующих blade-синтаксис, как в вашем коде, вы должны передать готовый URL в эту функцию.
          // Мы не можем интерпретировать blade-синтаксис в чистом JavaScript.
          // Например, используйте: getData('someURL', response.this_page + 1);
          getData(url, response.this_page + 1);
        } else {
          document.getElementById('loading').style.display = 'none';
          var numerics = document.querySelectorAll('.numeric');
          numerics.forEach(function(elem) {
            var val = elem.textContent;
            // elem.textContent = val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
          });

          // Для DataTable без jQuery, вы должны найти альтернативное решение.
          // Например, воспользуйтесь другой библиотекой или реализуйте похожий функционал вручную.
        }
      });
    }

    // Как и в предыдущем коде, вы должны передать правильный URL вместо blade-синтаксиса.
    // Например, используйте: getData('someURL', 1);
    document.addEventListener('DOMContentLoaded', function() {
      getData('{!! route('admin.orders.statistic', $_GET) !!}', 1);
    });
  </script>
</x-light-layout>
{{--@extends('layouts.admin_layout_light')--}}

{{--@section('content')--}}
{{--  <div class="card" style="min-height:100vh;">--}}
{{--    <div id="loading"><div class="spinner"></div></div>--}}
{{--    <div class="card-body p-0">--}}
{{--      <div class="table-responsive">--}}
{{--        <div class="p-2">--}}
{{--          @if(isset(request()->date_from))--}}
{{--            <p><span class="font-weight-bold">Фильтр по дате от:</span> {{ date('d.m.Y H:i:s', strtotime(request()->date_from)) }}</p>--}}
{{--          @endif--}}
{{--          @if(isset(request()->date_to))--}}
{{--            <p><span class="font-weight-bold">Фильтр по дате до:</span> {{ date('d.m.Y H:i:s', strtotime(request()->date_to)) }}</p>--}}
{{--          @endif--}}
{{--          <p><span class="font-weight-bold">Всего заказов:</span> <span id="orders_total" class="numeric">0</span></p>--}}
{{--          <p><span class="font-weight-bold">Сумма товаров:</span> <span id="total" class="numeric">0</span></p>--}}
{{--          <p><span class="font-weight-bold">Количество товаров:</span> <span id="total_count" class="numeric">0</span></p>--}}
{{--          <p><span class="font-weight-bold">Сумма доставки (OZON):</span> <span id="total_shipping_ozon" class="numeric">0</span></p>--}}
{{--          <p><span class="font-weight-bold">Сумма доставки (СДЭК):</span> <span id="total_shipping_cdek" class="numeric">0</span></p>--}}
{{--          <p><span class="font-weight-bold">Сумма доставки (СДЭК курьер):</span> <span id="total_shipping_cdek_courier" class="numeric">0</span></p>--}}
{{--          <p><span class="font-weight-bold">Сумма доставки (Boxberry):</span> <span id="total_shipping_boxberry" class="numeric">0</span></p>--}}
{{--          <p><span class="font-weight-bold">Сумма доставки (Почта):</span> <span id="total_shipping_pochta" class="numeric">0</span></p>--}}
{{--          <p><span class="font-weight-bold">Сумма доставки:</span> <span id="total_shipping" class="numeric">0</span></p>--}}
{{--          <p><span class="font-weight-bold">Сумма скидки:</span> <span id="total_discount" class="numeric">0</span></p>--}}
{{--        </div>--}}
{{--        <table id="sort_table" class="table table-striped projects">--}}
{{--          <thead>--}}
{{--          <tr>--}}
{{--            <th style="width: 5%">--}}
{{--              #--}}
{{--            </th>--}}
{{--            <th style="width: 30%">--}}
{{--              Наименоание--}}
{{--            </th>--}}
{{--            <th>--}}
{{--              Артикул--}}
{{--            </th>--}}
{{--            <th>--}}
{{--              Продано шт.--}}
{{--            </th>--}}
{{--            <th>--}}
{{--              Продано на сумму--}}
{{--            </th>--}}
{{--          </tr>--}}
{{--          </thead>--}}
{{--          <tbody id="cart">--}}
{{--          @foreach($products as $product)--}}
{{--            @if(!isset($stat[$product->article]['count'])||!$stat[$product->article]['count'])--}}
{{--              @continue--}}
{{--            @endif--}}
{{--            <tr>--}}
{{--              <td>{{ $product->id }}</td>--}}
{{--              <td>--}}
{{--                {{ $product->name }}--}}
{{--              </td>--}}
{{--              <td>--}}
{{--                {{ $product->article }}--}}
{{--              </td>--}}
{{--              <td>--}}
{{--                {{ isset($stat[$product->article]['count']) ? number_format($stat[$product->article]['count'], 0, ',', ' ') : 0 }}--}}
{{--              </td>--}}
{{--              <td>--}}
{{--                {{ isset($stat[$product->article]['total']) ? number_format($stat[$product->article]['total'], 0, ',', ' ') : 0 }}р--}}
{{--              </td>--}}

{{--            </tr>--}}
{{--          @endforeach--}}
{{--          </tbody>--}}
{{--        </table>--}}
{{--      </div>--}}
{{--    </div>--}}
{{--    <!-- /.card-body -->--}}
{{--  </div>--}}
{{--@endsection--}}
{{--@section('style')--}}
{{--  <link rel="stylesheet" href="{{ asset('admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">--}}
{{--  <link rel="stylesheet" href="{{ asset('admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">--}}
{{--  <link rel="stylesheet" href="{{ asset('admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">--}}
{{--  <style>--}}
{{--    #loading{--}}
{{--      position: fixed;--}}
{{--      background: rgba(255,255,255,.9);--}}
{{--      width: 100vw;--}}
{{--      height: 100vh;--}}
{{--      left: 0;--}}
{{--      top: 0;--}}
{{--      right: 0;--}}
{{--      bottom: 0;--}}
{{--      z-index: 1000;--}}
{{--      display: flex;--}}
{{--      text-align: center;--}}
{{--      align-items: center;--}}
{{--      justify-content: center;--}}
{{--    }--}}
{{--    .spinner {--}}
{{--      color: black;--}}
{{--      display: flex;--}}
{{--      align-items: center;--}}
{{--      justify-content: center;--}}
{{--      min-height: 100vh;--}}
{{--    }--}}

{{--    .spinner:after {--}}
{{--      animation: changeContent .8s linear infinite;--}}
{{--      display: block;--}}
{{--      content: "⠋";--}}
{{--      font-size: 80px;--}}
{{--    }--}}

{{--    @keyframes changeContent {--}}
{{--      10% { content: "⠙"; }--}}
{{--      20% { content: "⠹"; }--}}
{{--      30% { content: "⠸"; }--}}
{{--      40% { content: "⠼"; }--}}
{{--      50% { content: "⠴"; }--}}
{{--      60% { content: "⠦"; }--}}
{{--      70% { content: "⠧"; }--}}
{{--      80% { content: "⠇"; }--}}
{{--      90% { content: "⠏"; }--}}
{{--    }--}}
{{--  </style>--}}
{{--@endsection--}}
{{--@section('script')--}}
{{--  <script src="{{ asset('admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>--}}
{{--  <script src="{{ asset('admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>--}}
{{--  <script src="{{ asset('admin/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>--}}
{{--  <script src="{{ asset('admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>--}}
{{--  <script src="{{ asset('admin/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>--}}
{{--  <script src="{{ asset('admin/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>--}}
{{--  <script src="{{ asset('admin/plugins/jszip/jszip.min.js') }}"></script>--}}
{{--  <script src="{{ asset('admin/plugins/pdfmake/pdfmake.min.js') }}"></script>--}}
{{--  <script src="{{ asset('admin/plugins/pdfmake/vfs_fonts.js') }}"></script>--}}
{{--  <script src="{{ asset('admin/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>--}}
{{--  <script src="{{ asset('admin/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>--}}
{{--  <script src="{{ asset('admin/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>--}}
{{--  <script>--}}


{{--    function getData(url, page = 1){--}}
{{--      $.ajax({--}}
{{--        type: 'GET',--}}
{{--        url: url,--}}
{{--        data: {page: page},--}}
{{--        success: function(response) { //Если все нормально--}}
{{--          if (response.total > 0) {--}}
{{--            var total = Number($('#total').text())+Number(response.total);--}}
{{--            $('#total').text(total);--}}
{{--          }--}}
{{--          if (Number.isInteger(response.total_count) && response.total_count > 0) {--}}
{{--            var total_count = Number($('#total_count').text())+response.total_count;--}}
{{--            $('#total_count').text(total_count);--}}
{{--          }--}}
{{--          if (response.total_discount > 0) {--}}
{{--            var total_discount = Number($('#total_discount').text())+response.total_discount;--}}
{{--            $('#total_discount').text(total_discount);--}}
{{--            console.log(total_discount);--}}
{{--          }--}}
{{--          if (response.total_shipping.total > 0) {--}}
{{--            var total_shipping = Number($('#total_shipping').text())+response.total_shipping.total;--}}
{{--            $('#total_shipping').text(total_shipping);--}}
{{--          }--}}
{{--          if (Number.isInteger(response.orders_total) && response.orders_total > 0) {--}}
{{--            var orders_total = response.orders_total;--}}
{{--            $('#orders_total').text(orders_total);--}}
{{--          }--}}
{{--          if (Number.isInteger(response.total_shipping.ozon) && response.total_shipping.ozon > 0) {--}}
{{--            var total_shipping_ozon = Number($('#total_shipping_ozon').text())+response.total_shipping.ozon;--}}
{{--            $('#total_shipping_ozon').text(total_shipping_ozon);--}}
{{--          }--}}
{{--          if (Number.isInteger(response.total_shipping.boxberry) && response.total_shipping.boxberry > 0) {--}}
{{--            var total_shipping_boxberry = Number($('#total_shipping_boxberry').text())+response.total_shipping.boxberry;--}}
{{--            $('#total_shipping_boxberry').text(total_shipping_boxberry);--}}
{{--          }--}}
{{--          if (Number.isInteger(response.total_shipping.cdek) && response.total_shipping.cdek > 0) {--}}
{{--            var total_shipping_cdek = Number($('#total_shipping_cdek').text())+response.total_shipping.cdek;--}}
{{--            $('#total_shipping_cdek').text(total_shipping_cdek);--}}
{{--          }--}}
{{--          if (Number.isInteger(response.total_shipping.cdek_courier) && response.total_shipping.cdek_courier > 0) {--}}
{{--            var total_shipping_cdek_courier = Number($('#total_shipping_cdek_courier').text())+response.total_shipping.cdek_courier;--}}
{{--            $('#total_shipping_cdek_courier').text(total_shipping_cdek_courier);--}}
{{--          }--}}
{{--          if (response.total_shipping.pochta > 0) {--}}
{{--            var total_shipping_pochta = Number($('#total_shipping_pochta').text())+response.total_shipping.pochta;--}}
{{--            $('#total_shipping_pochta').text(total_shipping_pochta);--}}
{{--          }--}}
{{--          for (key in response.statistic) {--}}
{{--            var elem = response.statistic[key];--}}
{{--            if($('tr').is('#'+key)){--}}
{{--              var count = Number($('#'+key+'_count').text())+elem.count;--}}
{{--              $('#'+key+'_count').text(count);--}}
{{--              $('#'+key+'_count').closest('td').attr('data-sort', count);--}}
{{--              var total = Number($('#'+key+'_total').text())+elem.total;--}}
{{--              $('#'+key+'_total').text(total);--}}
{{--              $('#'+key+'_total').closest('td').attr('data-sort', total);--}}
{{--            }else{--}}
{{--              var html = '<tr id="'+key+'">';--}}
{{--              html += '<td class="border p-2">'+elem.id+'</td>';--}}
{{--              html += '<td class="border p-2">'+elem.name+'</td>';--}}
{{--              html += '<td class="border p-2">'+key+'</td>';--}}
{{--              html += '<td class="border p-2" data-sort="'+elem.count+'"><span id="'+key+'_count" class="numeric">'+elem.count+'</span></td>';--}}
{{--              html += '<td class="border p-2" data-sort="'+elem.total+'"><span id="'+key+'_total" class="numeric">'+elem.total+'</span></td>';--}}
{{--              html += '</tr>';--}}
{{--              $('#cart').append(html);--}}
{{--            }--}}
{{--          }--}}
{{--          if (response.this_page<response.last_page){--}}
{{--            getData('{!! route('admin.orders.statistic', $_GET) !!}', response.this_page+1);--}}
{{--          }else{--}}
{{--            $('#loading').hide();--}}
{{--            $('.numeric').each(function(){--}}
{{--              var this_val = $(this).text();--}}
{{--              $(this).text(this_val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " "));--}}
{{--            })--}}
{{--            $("#sort_table").DataTable({--}}
{{--              "paging": false, "responsive": false, "lengthChange": false, "autoWidth": false,"searching": false, "info": false,--}}
{{--            });--}}
{{--          }--}}
{{--        }--}}
{{--      });--}}
{{--    }--}}
{{--    getData('{!! route('admin.orders.statistic', request()->toArray()) !!}', 1);--}}
{{--  </script>--}}
{{--@endsection--}}
