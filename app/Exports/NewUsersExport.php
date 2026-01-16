<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class NewUsersExport implements FromArray, WithHeadings
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
          $row->new_user_count_diff
      ];
    }, $this->data);
  }

  public function headings(): array
  {
    return [
        'Период',
        'Новые клиенты',
    ];
  }
}
