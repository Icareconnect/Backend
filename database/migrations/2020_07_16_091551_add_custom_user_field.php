<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomUserField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('custom_fields', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_type');
            $table->string('field_name');
            $table->string('field_type');
            $table->string('required_sign_up',1);
            $table->foreign('user_type')->references('id')->on('roles');
            $table->timestamps();
         });

         Schema::create('custom_user_fields', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('field_value');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('custom_field_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('custom_field_id')->references('id')->on('custom_fields');
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
        Schema::dropIfExists('custom_fields');
        Schema::dropIfExists('custom_user_fields');
    }
}
