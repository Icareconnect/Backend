<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceProviderSlotsDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('service_provider_slots_dates');
        Schema::create('service_provider_slots_dates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('service_provider_id');
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('category_id');
            $table->time('start_time');
            $table->time('end_time');
            $table->date('date');
            $table->foreign('service_provider_id')->references('id')->on('users');
            $table->foreign('service_id')->references('id')->on('services');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->timestamps();
        });
        Schema::dropIfExists('service_provider_slots');
        Schema::create('service_provider_slots', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('service_provider_id');
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('category_id');
            $table->time('start_time');
            $table->time('end_time');
            $table->tinyInteger('day');
            $table->foreign('service_provider_id')->references('id')->on('users');
            $table->foreign('service_id')->references('id')->on('services');
            $table->foreign('category_id')->references('id')->on('categories');
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
        Schema::dropIfExists('service_provider_slots_dates');
        Schema::dropIfExists('service_provider_slots');
    }
}
