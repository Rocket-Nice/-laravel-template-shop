<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NpsSurveyAnswer extends Model
{
    use HasFactory;

  public function question()
  {
    return $this->belongsTo(NpsSurveyQuestion::class, 'survey_question_id');
  }

  protected $fillable = [
      'user_id',
      'survey_question_id',
      'score',
      'comment'
  ];
}
