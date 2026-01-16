@section('title', $seo['title'] ?? config('app.name'))
<x-app-layout>
  <div class="text-center">
    <div class="h-[50vh] flex flex-col justify-center items-center">
      <div>
        <h2 class="d-headline-1 m-headline-1">Оплата заказа №<span class="cormorantInfant">{{ $params['order_id'] }}</span></h2>
        <div id="message" class="d-text-body m-text-body" style="color: red;"></div>

        <div class="mt-4"><x-public.primary-button type="button" id="pay-cloudpayments" class="md:h-14 md:w-full md:max-w-[285px] mx-auto">К оплате</x-public.primary-button></div>
      </div>
    </div>
  </div>
  <script src="https://widget.cloudpayments.ru/bundles/cloudpayments"></script>
  <script>
    this.pay = function () {
      var widget = new cp.CloudPayments();
      widget.pay('charge', // или 'auth'
        { //options
          publicId: 'pk_fca55781eeded7e4ca85d6ab8c1bd',
          description: '{{ $params['title'] }}', //назначение
          amount: {{ $params['amount'] }},
          currency: 'RUB',
          invoiceId: '{{ $params['order_id'] }}', //номер заказа  (необязательно)
          accountId: '{{ $params['email'] }}', //идентификатор плательщика (необязательно)
          email: '{{ $params['email'] }}', //идентификатор плательщика (необязательно)
          skin: "mini",
          retryPayment: true,
          data: {!! json_encode($params['widget_data'], JSON_UNESCAPED_UNICODE) !!}
        },
        {
          onSuccess: function (options) { // success
            document.location = '{{ route('order.success', ['InvId' => $params['order_id']]) }}';
          },
          onFail: function (reason, options) { // fail
            // let link = document.getElementById('reserv_pay');
            // document.location = link.getAttribute('href');
            document.getElementById('message').innerText = 'Мы не получили ваш платеж, попробуйте снова'
          },
          onComplete: function (paymentResult, options) { //Вызывается как только виджет получает от api.cloudpayments ответ с результатом транзакции.
            //например вызов вашей аналитики Facebook Pixel
          }
        }
      )
    };

    document.addEventListener('DOMContentLoaded', function(){
      document.getElementById('pay-cloudpayments').onclick = pay
      var eventClick = new Event('click', {
        'bubbles': true,
        'cancelable': true
      });
      document.getElementById('pay-cloudpayments').dispatchEvent(eventClick);
    });
  </script>
</x-app-layout>
