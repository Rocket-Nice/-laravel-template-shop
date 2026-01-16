<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRegionIdToCdekCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cdek_cities', function (Blueprint $table) {
          $table->unsignedBigInteger('region_id')->after('region_code')->nullable();

          $table->foreign('region_id')
              ->references('id')
              ->on('cdek_regions')
              ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cdek_cities', function (Blueprint $table) {
            //
        });
    }
}
