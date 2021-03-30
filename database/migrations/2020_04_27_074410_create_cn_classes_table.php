<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCnClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ct_classes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('image')->nullable();
            $table->string('status')->nullable()->default('added');
            $table->string('calling_type')->nullable();
            $table->dateTime('booking_date');
            $table->double('price');
            $table->integer('limit_enroll')->default(10);
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('created_by');
            $table->softDeletes();
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('created_by')->references('id')->on('users');
            $table->timestamps();
        });

        Schema::create('enrolled_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('assinged_user');
            $table->foreign('class_id')->references('id')->on('ct_classes');
            $table->foreign('assinged_user')->references('id')->on('users');
            $table->softDeletes();
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
        Schema::dropIfExists('ct_classes');
        Schema::dropIfExists('enrolled_users');
    }
}
