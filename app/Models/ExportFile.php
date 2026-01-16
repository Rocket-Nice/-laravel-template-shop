<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExportFile extends Model
{
    use HasFactory;

    public function creator(): BelongsTo
    {
      return $this->belongsTo(User::class, 'exported_by');
    }


  protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s'
  ];


  protected $fillable = [
        'name',
        'path',
        'lines_count',
        'size',
        'type',
        'exported_by',
    ];
}
