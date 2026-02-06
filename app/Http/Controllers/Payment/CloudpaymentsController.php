<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\OrderController;
use App\Models\Order;
use App\Models\User;
use Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CloudpaymentsController extends Controller
{
    // pk_fca55781eeded7e4ca85d6ab8c1bd
    // api f075f1ffe5eda5c57c15f60861adbe60

    public function index(Order $order)
    {
        if ($order->confirm == 1) {
            return redirect('/order/success?InvId=' . $order->id);
        }
        if ($order->status == 'cancelled') {
            abort(403, 'Данный заказ аннулирован');
        }
        $order_data = $order->data;
        $order_data['cart'] = $order->data_cart;
        $order_shipping = $order->data_shipping;
        $user = $order->user;
        $cart = Cart::instance('cart');

        $order_info = [
            'total' => $order->amount,
            'full_name' => $order_data['form']['full_name'],
            'email' => $order_data['form']['email'],
            'telephone' => $order_data['form']['phone'],
            'shipping' => $order_shipping,
        ];
        $recepient = $this->getReceiptData($order_info, $order_data);
        if (isset($recepient['error'])) {
            return redirect()->route('order.index')->withErrors([
                $recepient['error']
            ]);
        }
        $params = [
            'amount' => $order->amount,
            'email' => $user->email,
            'order_id' => $order->id,
            'order_slug' => $order->slug,
            'title' => 'Косметическая продукция «le mousse»',
            'widget_data' => [
                'name' => trim($order_data['form']['full_name']),
                'phone' => $order_data['form']['phone'],
                'cloudPayments' => [
                    'customerReceipt' => $recepient
                ]
            ]
        ];
        $recepient = $this->getReceiptData($order_info, $order_data);
        if (isset($recepient['error'])) {
            return redirect()->route('order.index')->withErrors([
                $recepient['error']
            ]);
        }
        $params['widget_data']['cloudPayments']['customerReceipt'] = $recepient;
        $order->update([
            'data_kkt' => $params['widget_data']['cloudPayments']
        ]);

        $cart->destroy();
        $seo = array(
            'title' => 'Оплата картой'
        );

        return view('template.public.order.cloudpayments', compact('params', 'seo'));
    }

    private function getReceiptData($order_info, $order_data)
    {
        $amount_total = $order_info['total'];
        $receiptData = array(
            'Items' => array(),
            'taxationSystem' => 0,
            'calculationPlace' => 'lemousse.shop',
            'email' => $order_info['email'],
            'phone' => $order_info['telephone'],
            'customerInfo' => $order_info['full_name'],
            'amounts' => [
                'electronic' => $amount_total
            ]
        );
        $discount = 0;
        if ((!isset($order_data['promocode']['discount_cart']) || empty($order_data['promocode']['discount_cart'])) && isset($order_data['discount'])) {
            $discount = $order_data['total'] + ($order_info['shipping']['price'] ?? 0) - $amount_total;
        } else {
            $discount = $order_data['total'] + ($order_info['shipping']['price'] ?? 0) - $amount_total;
            if ($discount <= 0) {
                $discount = 0;
            }
        }
        if ($discount) {
            $receiptData['amounts'] = [
                'electronic' => $amount_total,
                'provision' => $discount
            ];
        }
        $order_amount = 0;
        $cart = $order_data['cart'];

        foreach ($cart as $item) {
            if ($item['price'] == 0) {
                continue;
            }
            $amount = $item['price'] * $item['qty'];
            $order_amount = $order_amount + $amount;

            $item = array(
                'label' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['qty'],
                'amount' => $amount,
                'vat' => 22,
                'method' => 4,
                'object' => 1,
                //          'AgentSign'     => 6,
                //          'PurveyorData' => [
                //              'Name' => 'ИП Овчинникова Ю.И.',
                //              'Inn' => '344103083881',
                //          ]
            );
            $receiptData['Items'][] = $item;
        }

        // Order Totals
        if ($order_info['shipping']['price'] > 0) {
            $receiptData['Items'][] = array(
                'label' => 'доставка',
                'price' => $order_info['shipping']['price'],
                'quantity' => 1,
                'amount' => $order_info['shipping']['price'],
                'vat' => 22,
                'method' => 4,
                'object' => 4,
                //          'AgentSign'     => 6,
                //          'PurveyorData' => [
                //              'Name' => 'ИП Овчинникова Ю.И.',
                //              'Inn' => '344103083881',
                //          ]
            );
        }

        return $receiptData;
    }

    public function check(Request $request)
    {
        //    Log::debug(print_r($request->toArray(), true));
        $order = Order::find($request->InvoiceId);
        $params = array(
            'TransactionId' => $request->TransactionId,
            'Amount' => $request->Amount,
            'Currency' => $request->Currency,
            'PaymentAmount' => $request->PaymentAmount,
            'PaymentCurrency' => $request->PaymentCurrency,
            'OperationType' => $request->OperationType,
            'InvoiceId' => $request->InvoiceId,
            'AccountId' => $request->AccountId,
            'SubscriptionId' => $request->SubscriptionId,
            'Name' => $request->Name,
            'Email' => $request->Email,
            'DateTime' => $request->DateTime,
            'IpAddress' => $request->IpAddress,
            'IpCountry' => $request->IpCountry,
            'IpCity' => $request->IpCity,
            'IpRegion' => $request->IpRegion,
            'IpDistrict' => $request->IpDistrict,
            'IpLatitude' => $request->IpLatitude,
            'IpLongitude' => $request->IpLongitude,
            'CardFirstSix' => $request->CardFirstSix,
            'CardLastFour' => $request->CardLastFour,
            'CardType' => $request->CardType,
            'CardExpDate' => $request->CardExpDate,
            'Issuer' => $request->Issuer,
            'IssuerBankCountry' => $request->IssuerBankCountry,
            'Description' => $request->Description,
            'AuthCode' => $request->AuthCode,
            'Token' => $request->Token,
            'TestMode' => $request->TestMode,
            'Status' => $request->Status,
            'GatewayName' => $request->GatewayName,
            'TotalFee' => $request->TotalFee,
            'CardProduct' => $request->CardProduct,
            'PaymentMethod' => $request->PaymentMethod
        );
        //    if ($order&&$order->confirm == 1){
        //      return json_encode(array('code' => 0));
        //    }
        if ($order) {
            $order->update([
                'amount' => $request->Amount,
                'data_payment' => $params,
                'confirm' => 1,
                'payment_provider' => 'cloudpayments',
            ]);
        } else {
            Log::debug('CloudPayment платеж не найден ' . print_r(array(
                "InvoiceId" => $request->InvoiceId, //	Номер заказа
            ), true));
            return json_encode(array('code' => 0));
        }
        $user = User::find($order->user_id);

        (new OrderController)->finishOrder($user, $order);

        return json_encode(array('code' => 0));
    }

    public function refund(Request $request)
    {
        Log::debug('refund cloudpayments');
        Log::debug(print_r($request->toArray(), true));
    }

    public function receipt(Request $request)
    {
        Log::debug(print_r($request->toArray(), true));
        if (!isset($request->Id) || !isset($request->InvoiceId)) {
            abort(419);
        }
        $params = array(
            "Id" => $request->Id, //	Уникальный идентификатор чека
            "DocumentNumber" => $request->DocumentNumber, //	Номер чека
            "SessionNumber" => $request->SessionNumber, //	Номер смены
            "Number" => $request->Number, //	Номер чека в смене
            "FiscalSign" => $request->FiscalSign, //	Фискальный признак документа
            "DeviceNumber" => $request->DeviceNumber, //	Заводской номер ККТ
            "RegNumber" => $request->RegNumber, //	Регистрационный номер ККТ
            "FiscalNumber" => $request->FiscalNumber, //	Номер фискального накопителя
            "Inn" => $request->Inn, //	ИНН
            "Type" => $request->Type, //	Признак расчета, см. справочник
            "Ofd" => $request->Ofd, //	Наименование оператора фискальных данных
            "Url" => $request->Url, //	URL адрес с копией чека
            "QrCodeUrl" => $request->QrCodeUrl, //	URL адрес с QR кодом для проверки чека в ФНС
            "TransactionId" => $request->TransactionId, //	Идентификатор транзакции
            "Amount" => $request->Amount, //	Сумма чека
            "DateTime" => $request->DateTime, //	Дата/время выдачи чека во временной зоне UTC
            "InvoiceId" => $request->InvoiceId, //	Номер заказа
            "AccountId" => $request->AccountId, //	Идентификатор пользователя
            "Receipt" => $request->Receipt, //	Состав чека
            "CalculationPlace" => $request->CalculationPlace, //	Место осуществления расчетов
            "CashierName" => $request->CashierName, //	Имя кассира
            "SettlePlace" => $request->SettlePlace, //
        );
        $order = Order::find($request->InvoiceId);

        if ($order) {
            $order_options = $order->data_kkt;
            $order_options['kkt_success'] = $params;
            $order->update([
                'data_kkt' => $order_options
            ]);
        } else {
            Log::debug('CloudKassir платеж не найден ' . print_r(array(
                "InvoiceId" => $request->InvoiceId, //	Номер заказа
            ), true));
            return json_encode(array('code' => 0));
        }
        return json_encode(array('code' => 0));
    }

}
