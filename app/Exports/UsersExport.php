<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use SafeObject;

class UsersExport implements FromQuery, WithChunkReading
{
  use Exportable;

  private $request;

  public function __construct($request){
    $this->request = $request;
  }
  public function query()
  {
    return User::query()->select('id', 'name', 'phone', 'email')->filter(new SafeObject($this->request)); // Тут можно добавить условия, сортировки и т.д.
  }

  public function chunkSize(): int
  {
    return 5000;
  }

  public function headings(): array
  {
    return [
        'ID',
        'Имя',
        'Телефон',
        'Email',
    ];
  }
}
