<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceProviderFilterOptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_provider_filter_options', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('filter_type_id');
            $table->unsignedBigInteger('filter_option_id');
            $table->unsignedBigInteger('sp_id');
            $table->foreign('sp_id')->references('id')->on('users');
            $table->foreign('filter_option_id')->references('id')->on('filter_type_options');
            $table->foreign('filter_type_id')->references('id')->on('filter_types');
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
        Schema::dropIfExists('service_provider_filter_options');
    }
}
