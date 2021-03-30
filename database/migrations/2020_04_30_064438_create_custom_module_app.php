<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomModuleApp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_module_apps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('domain_url')->nullable();
            $table->string('domain_name')->nullable();
            $table->string('app_url')->nullable();
            $table->string('image')->nullable();
            $table->string('country_code')->nullable();
            $table->string('country_name')->nullable();
            $table->json('properties')->nullable();
            $table->string('status')->nullable()->default('enable');
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
        Schema::dropIfExists('custom_module_apps');
    }
}
