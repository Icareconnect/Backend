<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertFilterDutyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_pre_option_filters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('option_id')->comment('master_preferences_options ID');
            $table->string('module_table',30)->comment('like filter,category');
            $table->unsignedBigInteger('module_id')->comment('like master_preferences_options id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::dropIfExists('master_pre_option_filters');
    }
}
