<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NpsSurveyQuestion extends Model
{
    use HasFactory;


  public function survey()
  {
    return $this->belongsTo(NpsSurvey::class, 'survey_id');
  }

  public function answers()
  {
    return $this->hasMany(NpsSurveyAnswer::class, 'survey_question_id');
  }

  protected $fillable = [
      'survey_id',
      'text',
      'comment_text',
      'description',
      'is_hidden',
      'order'
  ];
}
