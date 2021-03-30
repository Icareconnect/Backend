<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomMasterFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_master_fields', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_type')->nullable();
            $table->string('name');
            $table->string('type')->comment('file,text');
            $table->longText('title')->nullable();
            $table->text('icon')->nullable();
            $table->string('module_type');
            $table->string('module_table')->comment('Table relation if exist')->nullable();
            $table->string('module_table_id')->comment('table id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
         });

        Schema::create('custom_user_master_fields', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('field_value');
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('custom_field_id');
            $table->string('module_table')->comment('Table relation if exist')->nullable();
            $table->string('module_table_id')->comment('table id')->nullable();
            $table->foreign('custom_field_id')->references('id')->on('custom_fields');
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
        Schema::dropIfExists('custom_master_fields');
        Schema::dropIfExists('custom_user_master_fields');
    }
}
