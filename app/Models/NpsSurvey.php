<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NpsSurvey extends Model
{
    use HasFactory;

  public function users()
  {
    return $this->belongsToMany(User::class, 'nps_survey_user')->withPivot(['nps_score', 'comment'])->withTimestamps();
  }
  public function questions()
  {
    return $this->hasMany(NpsSurveyQuestion::class, 'survey_id')->orderBy('order');
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
