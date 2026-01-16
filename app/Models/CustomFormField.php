<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomFormField extends Model
{
    use HasFactory;

  public function form()
  {
    return $this->belongsTo(CustomForm::class, 'form_id');
  }

  public function data()
  {
    return $this->hasMany(CustomFormData::class, 'field_id');
  }

  protected $fillable = [
      'form_id',
      'key',
      'description',
      'order',
      'is_hidden',
  ];
}
