<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations. 2024-08-05 10:00:00
     */
    public function up(): void
    {
        Schema::create('nps_survey_user', function (Blueprint $table) {
          $table->id();
          $table->foreignId('survey_id')->constrained('nps_surveys')->onDelete('cascade');
          $table->foreignId('user_id')->constrained()->onDelete('cascade');
          $table->text('comment')->nullable();
          $table->float('nps_score');
          $table->timestamps();
//
//          // Опционально: если вы хотите, чтобы комбинация survey_id и user_id была уникальной
//          $table->unique(['survey_id', 'user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nps_survey_user');
    }
};
