@section('title', $seo['title'] ?? config('app.name'))
<x-app-layout>
  <div class="text-center">
    <div class="h-[50vh] flex flex-col justify-center items-center">
      <div>
        <h2 class="d-headline-1 m-headline-1">Оплата заказа №<span class="cormorantInfant">{{ $params['inv_id'] }}</span></h2>
        <div id="message" class="d-text-body m-text-body" style="color: red;"></div>

        <form action='https://auth.robokassa.ru/Merchant/Index.aspx' method="POST" id="robokassa-pay" style="margin-top: 15px;">
          <input type="hidden" name="MerchantLogin" value="{{ $params['mrh_login'] }}">
          <input type="hidden" name="mrh_pass1" value="{{ $params['mrh_pass1'] }}">
          <input type="hidden" name="OutSum" value="{{ $params['out_summ'] }}">
          <input type="hidden" name="InvId" value="{{ $params['inv_id'] }}">
          <input type="hidden" name="Description" value="{{ $params['inv_desc'] }}">
          <input type="hidden" name="SignatureValue" value="{{ $params['crc'] }}">
          <input type="hidden" name="Receipt" value="{{ $params['Receipt'] }}">
          <input type="hidden" name="Email" value="{{ $params['Email'] }}">
          <input type="hidden" name="Culture" value="ru">
          <input type="hidden" name="IsTest" value="{{ $params['test'] ?? 0 }}">
          <x-public.primary-button type="submit" class="md:h-14 md:w-full md:max-w-[285px] mx-auto">К оплате</x-public.primary-button>
        </form>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', ()=>{
      localStorage.removeItem('deadTime');
      document.getElementById('robokassa-pay').submit();
    });
  </script>
</x-app-layout>
