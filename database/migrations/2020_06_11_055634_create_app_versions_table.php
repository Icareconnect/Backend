<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_versions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('device_type')->comment('1: iOS, 2: Android');
            $table->tinyInteger('app_type')->comment('1: User App, 2: Doctor App');            
            $table->string('version_name',100);            
            $table->bigInteger('version');           
            $table->tinyInteger('update_type')->default(0)->comment('0 no_update 1: Minor, 2: Major');
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
        Schema::dropIfExists('app_versions');
    }
}
