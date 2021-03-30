<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request_history', function (Blueprint $table) {
            $table->string('sid')->nullable()->comment('string; an alpha-numeric unique identifier of the call');
            $table->string('calling_type')->nullable()->comment('calling_type exotel etc');
            $table->string('account_sid')->nullable()->comment('account sid');
            $table->string('virtual_number')->nullable()->comment('Virtual Number');
            $table->text('recording_url')->nullable()->comment('Link to the call recording if present');
            $table->unsignedBigInteger('from_on_call_duration')->nullable()->comment('Indicates the duration that this leg was on a call. This value could be 0 if the call was not picked up by the respective leg');
            $table->unsignedBigInteger('to_on_call_duration')->nullable()->comment('Indicates the duration that this leg was on a call. This value could be 0 if the call was not picked up by the respective leg');
            $table->string('from_pick_status')->nullable()->comment('This denotes the terminal status of the particular leg of the call');
            $table->string('to_pick_status')->nullable()->comment('This denotes the terminal status of the particular leg of the call,completed,busy,failed,no-answer,canceled');
            $table->enum('callback_return',['0','1'])->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
