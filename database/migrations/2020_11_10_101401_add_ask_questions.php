<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAskQuestions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('title');
            $table->string('type',50);
            $table->unsignedBigInteger('created_by')->comment('Created By Question or Support');
            $table->string('status',50)->default('pending')->comment('pending,answered');
            $table->timestamps();
        });

        Schema::create('support_assignees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('assigned_to')->comment('Assined to user');
            $table->unsignedBigInteger('support_id')->comment('Support like ask question id');
            $table->timestamps();
        });


        Schema::create('support_replies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('support_id')->comment('Support like ask question id');
            $table->unsignedBigInteger('answered_by')->comment('answer by user id');
            $table->longText('description')->comment('answer---');
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
        Schema::dropIfExists('supports');
        Schema::dropIfExists('support_assignees');
        Schema::dropIfExists('support_replies');
    }
}
