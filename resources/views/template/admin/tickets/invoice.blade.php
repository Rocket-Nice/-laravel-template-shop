<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <style>
    body {
      background: rgb(204,204,204);
      font-font: 'Helvetica', 'Arial', serif;
    }
    table {
      margin: 0;
      padding: 0;
      border-spacing: 0;
      border-collapse: separate;
      width: 100%;
    }
    table td {
      border-spacing: 0;
      border: 1px solid #ddd;
      padding: 5px;
      margin: 0;
    }
    page {
      background: white;
      display: block;
      margin: 0 auto;
      margin-bottom: 0.5cm;
      box-shadow: 0 0 0.5cm rgba(0,0,0,0.5);
      padding-top: 1cm;
      padding-bottom: 1cm;
    }
    page h2 {
      margin: 10px;
    }
    page[size="A4"] {
      width: 21cm;
    }
    page[size="A4"][layout="landscape"] {
      width: 29.7cm;
      height: 21cm;
    }
    page[size="A3"] {
      width: 29.7cm;
      height: 42cm;
    }
    page[size="A3"][layout="landscape"] {
      width: 42cm;
      height: 29.7cm;
    }
    page[size="A5"] {
      width: 14.8cm;
      height: 21cm;
    }
    page[size="A5"][layout="landscape"] {
      width: 21cm;
      height: 14.8cm;
    }
    @media print {
      body, page {
        margin: 0;
        box-shadow: none;
      }
    }
  </style>
</head>
<body>
<page size="A4">
  <h2>{{ $invoice_name }}</h2>
  <table>
    <thead>
    <tr>
      <th></th>
      <th>Наименование</th>
      <th>Объем</th>
      <th>Артикул</th>
      <th>Количество</th>
    </tr>
    </thead>
    <tbody>
    @php
      $i = 0;
    @endphp
    @foreach($cart as $model => $item)
      @php
        $i++;
      @endphp
      <tr>
        <td>{{ $i }}</td>
        <td>{{ $item['name'] }}</td>
        <td>{{ $item['volume'] ?? '' }}</td>
        <td>{{ $item['model'] }}</td>
        <td>{{ $item['qty'] }}шт.</td>
      </tr>
    @endforeach
    </tbody>
  </table>
  <div style="padding: .5cm 1cm;">
    Заказы:
    @foreach($orders as $order)
      {{ $order->getOrderNumber().', ' }}
    @endforeach
  </div>
  <div style="padding: 1cm;">
    <div>
      <img src="{{ $linkQrUri }}" alt="qr" style="max-width: 4cm;">
    </div>
    <div>{{ $short_link }}</div>
  </div>
</page>
<!-- <page size="A4" layout="landscape"></page>
<page size="A5"></page>
<page size="A5" layout="landscape"></page>
<page size="A3"></page>
<page size="A3" layout="landscape"></page> -->
</body>
</html>
