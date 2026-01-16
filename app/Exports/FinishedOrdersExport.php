<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FinishedOrdersExport implements FromArray, WithHeadings
{
  protected array $data;
  protected string $unit;

  public function __construct(array $data, string $unit = 'day')
  {
    $this->data = $data;
    $this->unit = $unit;
  }

  public function array(): array
  {
    return array_map(function ($row) {
      return [
          $row->bucket_label,
          $row->total_count_diff,
          $row->status_count_diff
      ];
    }, $this->data);
  }

  public function headings(): array
  {
    return [
          'Период',
          'Количество заказов',
          'Завершенные заказы',
    ];
  }
}
