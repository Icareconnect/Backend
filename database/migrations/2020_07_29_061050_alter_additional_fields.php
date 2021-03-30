<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAdditionalFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sp_additional_details', function (Blueprint $table) {
            $table->unsignedBigInteger('additional_detail_id');
            $table->foreign('additional_detail_id')->references('id')->on('additional_details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('sp_additional_details', function (Blueprint $table) {
            $table->dropColumn('additional_detail_id');
        });
    }
}
