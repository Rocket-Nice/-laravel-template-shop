<?php

namespace App\Exports;

use App\Models\BoxberryPvz;
use App\Models\CdekPvz;
use App\Models\Order;
use App\Models\Product;
use App\Models\Status;
use App\Models\X5PostPvz;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use SafeObject;

class OrdersExport implements FromQuery, WithChunkReading, WithHeadings, WithMapping
{
  use Exportable;

  public function chunkSize(): int
  {
    return 500;
  }

  private $request;

  public function __construct($request){
    $this->request = $request;
  }
  public function query()
  {
    $select = [
        'id', //  'Номер заказа',
        'created_at', //  'Дата',
        'data->form->full_name as full_name', //  'ФИО',
        'data->form->email as email', //  'Почта',
        'data->form->phone as phone',//  'Телефон',
        'data_cart',//  'Корзина',
        'confirm', //  'Оплачен',
        'status', //  'Статус',
        'payment_provider', //  'Платежка',
        'data_shipping', //  'Способ доставки', //  'Адрес доставки',
        'amount', //  сумма заказа
        'data_shipping->price as shipping_price', //  стоимость доставки
        'data->total as product_price', //  стоимость товаров
        DB::raw('json_extract(data_shipping, "$.price") + json_extract(data, "$.total") - amount as discount'), //  скидка
    ];
    return Order::query()
        ->select($select)
        ->filtered(new SafeObject($this->request));
  }

  public function map($order): array
  {
    $cart = '';
    if ($order->data_cart) {
      foreach ($order->data_cart as $item) {
        $cart .= $item['name'] . ' (' . $item['qty'] . 'шт.), ';
      }
    }
    $cart = trim($cart, ', ');
    $date = Carbon::parse($order->created_at)->format('d.m.Y H:i');
    $status = Status::query()->where('key', $order->status)->first();
    $shipping_method = $order->data_shipping['shipping-method'] ?? '';
    $address = '';
    if ($order->data_shipping['shipping-code'] == 'ozon') {
      $address = $order->data_shipping['ozon-pvz-address'];
    } elseif ($order->data_shipping['shipping-code'] == 'boxberry'){
      $boxberry_pvz = BoxberryPvz::where('code', $order->data_shipping['boxberry-pvz-id'])->first();
      $region = $boxberry_pvz->area ?? null;
      $city = $boxberry_pvz->city_name ?? null;
      $address = $region . ', ' . $city . ', ' . $order->data_shipping['boxberry-pvz-address'];
    }elseif($order->data_shipping['shipping-code'] == 'x5post'){
      $x5post_pvz = X5PostPvz::where('mdmCode', $order->data_shipping['x5post-pvz-id'])->first();
      $address = $x5post_pvz->fullAddress ?? $order->data_shipping['x5post-pvz-address'];
    }elseif($order->data_shipping['shipping-code'] == 'cdek'){
      $cdek_pvz = CdekPvz::where('code', $order->data_shipping['cdek-pvz-id'])->first();
      $region = $cdek_pvz->region ?? null;
      $city = $cdek_pvz->city ?? null;
      $address = $region . ', ' . $city . ', ' . $order->data_shipping['cdek-pvz-address'];
    }elseif($order->data_shipping['shipping-code'] == 'cdek_courier'){
      $address = $order->data_shipping['cdek_courier-form-address'];
    }elseif($order->data_shipping['shipping-code'] == 'pochta'){
      $address = $order->data_shipping['full_address'];
    }

    $gifts = '';
    $order_gifts = $order->gifts;
    if ($order_gifts) {
      foreach ($order_gifts as $gift) {
        $gifts .= $gift->prize->name . ', ';
      }
    }
    $gifts = trim($gifts, ', ');
    return [
        $order->getOrderNumber(), //  'Номер заказа',
        $date, //  'Дата',
        $order->full_name, //  'ФИО',
        $order->email, //  'Почта',
        $order->phone, //  'Телефон',
        $cart, //  'Корзина',
        $order->amount,
        $order->product_price,
        $order->shipping_price,
        $order->discount,
        $order->confirm ? 'Да' : 'Нет', //  'Оплачен',
        $status ? $status->name : $order->status, //  'Статус',
        ucfirst($order->payment_provider), // Платежка
        $shipping_method, //  'Способ доставки',
        $address, //  'Адрес доставки',
        $gifts //  'Подарки'
    ];
  }

  public function headings(): array
  {
    return [
        'Номер заказа',
        'Дата',
        'ФИО',
        'Почта',
        'Телефон',
        'Корзина',
        'Сумма заказа',
        'Стоимость товаров',
        'Стоимость доставки',
        'Скидка',
        'Оплачен',
        'Статус',
        'Платежный сервис',
        'Способ доставки',
        'Адрес доставки',
        'Подарки'
    ];
  }
}
