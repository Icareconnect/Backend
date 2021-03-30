<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('additional_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('type',20);
            $table->string('is_enable',1)->default("0");
            $table->foreign('category_id')->references('id')->on('categories');
            $table->timestamps();
         });
        Schema::create('sp_additional_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sp_id');
            $table->foreign('sp_id')->references('id')->on('users');
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string('file_name')->nullable();
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
       Schema::dropIfExists('additional_details');
       Schema::dropIfExists('sp_additional_details');
    }
}
