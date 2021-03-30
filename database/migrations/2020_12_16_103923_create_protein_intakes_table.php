<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProteinIntakesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('protein_intakes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->comment('User ID');
            $table->double('daily_limit')->comment('In Gms.');
            $table->timestamps();
        });
        Schema::create('daily_protein_dates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->comment('User ID');
            $table->date('date')->comment('Date Type UTC');
            $table->double('daily_limit')->comment('In Gms');
            $table->double('total_usage')->comment('In Gms');
            $table->timestamps();
        });
        Schema::create('daily_protein_takes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->comment('User ID');
            $table->dateTime('date_time')->comment('Date Time');
            $table->double('quantity')->comment('In Gms');
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
        Schema::dropIfExists('protein_intakes');
        Schema::dropIfExists('daily_protein_dates');
        Schema::dropIfExists('daily_protein_takes');
    }
}
