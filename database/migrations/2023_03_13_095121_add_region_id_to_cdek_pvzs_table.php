<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRegionIdToCdekPvzsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cdek_pvzs', function (Blueprint $table) {
            $table->unsignedBigInteger('region_id')->after('region')->nullable();

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
        Schema::table('cdek_pvzs', function (Blueprint $table) {
            //
        });
    }
}
