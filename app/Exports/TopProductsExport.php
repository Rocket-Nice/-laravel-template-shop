<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TopProductsExport implements FromArray, WithHeadings
{
  protected array $data;

  public function __construct(array $data)
  {
    $this->data = $data;
  }

  public function array(): array
  {
    return array_map(function ($row) {
      return [
          $row->product_name,
          $row->total_qty,
          $row->total_amount
      ];
    }, $this->data);
  }

  public function headings(): array
  {
    return [
        'Наименование',
        'Количество',
        'Сумма, ₽',
    ];
  }
}
