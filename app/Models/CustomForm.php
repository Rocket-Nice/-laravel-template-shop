<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomForm extends Model
{
    use HasFactory;

    public function fields()
    {
      return $this->hasMany(CustomFormField::class, 'form_id');
    }

    public function data()
    {
      return $this->hasMany(CustomFormData::class, 'form_id');
    }

  public function users()
  {
    return $this->belongsToMany(User::class, 'custom_form_user', 'form_id', 'user_id')->withPivot(['status'])->withTimestamps();
  }

  const STATUS = [
      0 => 'Новый',
      1 => 'В обработке',
      2 => 'Обработан',
  ];

  public function getRouteKeyName()
  {
    return 'slug';
  }

    protected $fillable = [
        'name',
        'slug'
    ];
}
