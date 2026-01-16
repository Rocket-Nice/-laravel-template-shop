<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomFormData extends Model
{
    use HasFactory;

    public function form()
    {
      return $this->belongsTo(CustomForm::class, 'form_id');
    }


  public function field()
  {
    return $this->belongsTo(CustomFormField::class, 'field_id');
  }

    protected $fillable = [
        'value',
        'form_id',
        'field_id',
        'user_id',
    ];
}
