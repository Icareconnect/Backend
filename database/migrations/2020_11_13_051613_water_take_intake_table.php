<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class WaterTakeIntakeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('water_intakes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->comment('User ID');
            $table->double('daily_limit')->comment('In Liter');
            $table->timestamps();
        });

        Schema::create('daily_water_dates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->comment('User ID');
            $table->date('date')->comment('Date Type UTC');
            $table->double('daily_limit')->comment('In Liter');
            $table->double('total_usage')->comment('In Liter');
            $table->timestamps();
        });

        Schema::create('daily_glasses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->comment('User ID');
            $table->dateTime('date_time')->comment('Date TIme');
            $table->double('glass_quantity')->comment('In Liter');
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
        Schema::dropIfExists('water_intakes');
        Schema::dropIfExists('daily_water_dates');
        Schema::dropIfExists('daily_glasses');
    }
}
