<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMasterSlots extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('master_slots', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('type',30)->default('all_day');
            $table->unsignedBigInteger('service_id')->nullable()->comment('service_id for future');
            $table->unsignedBigInteger('category_id')->nullable()->comment('category_id for future');
            $table->date('date')->nullable();
            $table->tinyInteger('day')->nullable();
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
        Schema::dropIfExists('master_slots');
    }
}
