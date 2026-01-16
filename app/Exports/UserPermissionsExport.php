<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserPermissionsExport implements FromQuery, WithChunkReading, WithHeadings, WithMapping
{
  use Exportable;

  private $request;

  public function __construct($request){
    $this->request = $request;
  }
  public function query()
  {
    $roles = Role::whereHas('permissions', function ($query) {
      $query->where('name', 'Доступ к админпанели');
    })->pluck('name')->toArray();

    $users = User::query()
        ->whereHas('roles', function ($query) use ($roles) {
          $query->whereIn('name', $roles);
        })
        ->orWhereHas('permissions', function ($query) {
          $query->where('name', 'Доступ к админпанели');
        });
    return $users;
  }

  public function chunkSize(): int
  {
    return 5000;
  }

  public function map($row): array
  {
    $permissions = Permission::get();
    $permissions_string = '';
    foreach($permissions as $permission){
      if($row->hasPermissionTo($permission->name)){
        $permissions_string .= $permission->name.', ';
      }
    }
    $permissions_string = rtrim($permissions_string, ', ');
    return [
        $row->id, //
        $row->name, //
        $row->phone, //
        $row->email,
        $permissions_string
    ];
  }

  public function headings(): array
  {
    return [
        'ID',
        'Имя',
        'Телефон',
        'Email',
        'Права'
    ];
  }
}
