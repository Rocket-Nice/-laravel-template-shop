<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrderTotalsExport implements FromArray, WithHeadings
{
  protected array $data;
  protected string $unit;

  public function __construct(array $data, string $unit = 'hour')
  {
    $this->data = $data;
    $this->unit = $unit;
  }

  public function array(): array
  {
    return array_map(function ($row) {
      $period = $row->bucket_label;

      $orders_total = number_format($row->order_count, 0, '.', ' ');
      $orders = 0;
      if ($row->order_count_diff > 0) {
        $orders = '+' . number_format($row->order_count_diff, 0, '.', ' ');
      }

      $items_total = number_format($row->item_count, 0, '.', ' ');
      $items = 0;
      if ($row->item_count_diff > 0) {
        $items = '+' . number_format($row->item_count_diff, 0, '.', ' ');
      }

      $sum_total = number_format($row->total_sum, 0, '.', ' ');
      $sum = 0;
      if ($row->total_sum_diff > 0) {
        $sum = '+' . number_format($row->total_sum_diff, 0, '.', ' ');
      }

      return [
          $period,
          $orders,
          $orders_total,
          $items,
          $items_total,
          $sum . ' ₽',
          $sum_total . ' ₽',
      ];
    }, $this->data);
  }

  public function headings(): array
  {
    return [
        'Период',
        'Заказы',
        'Заказы (всего)',
        'Товары',
        'Товары (всего)',
        'Сумма, ₽',
        'Сумма, ₽ (всего)',
    ];
  }
}
