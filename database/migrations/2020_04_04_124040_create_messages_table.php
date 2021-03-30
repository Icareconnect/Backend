<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
             $table->bigIncrements('id');
             $table->unsignedBigInteger('user_id');
             $table->unsignedBigInteger('receiver_id')->nullable();
             $table->unsignedBigInteger('request_id');
             $table->text('message');
             $table->string('image_url')->nullable();
             $table->string('message_type')->nullable();
             $table->enum('status',['NOT_SENT','SENT','DELIVERED','SEEN'])->nullable()->default('SENT');
             $table->enum('read',['1','0'])->default('0');
             $table->enum('delivered',['1','0'])->default('0');
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
        Schema::dropIfExists('messages');
    }
}
