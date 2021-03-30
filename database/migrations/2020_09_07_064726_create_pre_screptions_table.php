<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreScreptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pre_scriptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type');
            $table->text('title')->nullable();
            $table->text('pre_scription_notes')->nullable();
            $table->unsignedBigInteger('request_id');
            $table->foreign('request_id')->references('id')->on('requests');
            $table->timestamps();
        });

        Schema::create('pre_scription_medicines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('medicine_name')->nullable();
            $table->string('duration',20)->nullable();
            $table->string('dosage_type',50)->nullable();
            $table->json('dosage_timing')->nullable();
            $table->unsignedBigInteger('pre_scription_id');
            $table->foreign('pre_scription_id')->references('id')->on('pre_scriptions');
            $table->timestamps();
        });

        Schema::create('images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('module_table');
            $table->string('module_table_id');
            $table->mediumText('image_name');
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
        Schema::dropIfExists('pre_scriptions');
        Schema::dropIfExists('pre_scription_medicines');
        Schema::dropIfExists('images');
    }
}
