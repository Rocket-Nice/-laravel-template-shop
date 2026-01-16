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
        Schema::create('nps_survey_questions', function (Blueprint $table) {
          $table->id();
          $table->unsignedBigInteger('survey_id');
          $table->string('text');
          $table->string('comment_text')->nullable();
          $table->text('description')->nullable();
          $table->integer('order')->nullable();
          $table->boolean('is_hidden')->default(false);
          $table->timestamps();

          $table->foreign('survey_id')
              ->references('id')
              ->on('nps_surveys')
              ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nps_survey_questions');
    }
};
