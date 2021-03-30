<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterPreferences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_preferences', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('is_multi')->default(1);
            $table->string('name')->comment('Master Filter Name');
            $table->string('module_table')->nullable()->comment('Relation if exist');
            $table->unsignedBigInteger('module_table_id')->nullable()->comment('Relation table id');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('master_preferences_options', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('preference_id');
            $table->string('name');
            $table->longText('image')->nullable();
            $table->foreign('preference_id')->references('id')->on('master_preferences');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('user_master_preferences', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('preference_id');
            $table->unsignedBigInteger('preference_option_id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('preference_id')->references('id')->on('master_preferences');
            $table->foreign('preference_option_id')->references('id')->on('master_preferences_options');
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('master_preferences');
        Schema::dropIfExists('master_preferences_options');
        Schema::dropIfExists('user_master_preferences');
    }
}
