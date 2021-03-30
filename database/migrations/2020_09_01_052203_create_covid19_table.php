<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCovid19Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('covid19s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type',20);
            $table->string('image_web')->nullable();
            $table->string('image_mobile')->nullable();
            $table->string('title')->nullable();
            $table->mediumText('description')->nullable();
            $table->string('on_click_info')->nullable();
            $table->tinyInteger('home_screen')->default(0);
            $table->tinyInteger('enable')->default(1);
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
        Schema::dropIfExists('covid19s');
    }
}
