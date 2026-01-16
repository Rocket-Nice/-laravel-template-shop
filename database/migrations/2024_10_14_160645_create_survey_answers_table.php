<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nps_survey_answers', function (Blueprint $table) {
          $table->id();
          $table->unsignedBigInteger('user_id');
          $table->unsignedBigInteger('survey_question_id');
          $table->float('score');
          $table->timestamps();

          $table->foreign('user_id')
              ->references('id')
              ->on('users')
              ->onDelete('cascade');
          $table->foreign('survey_question_id')
              ->references('id')
              ->on('nps_survey_questions')
              ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nps_survey_answers');
    }
};
