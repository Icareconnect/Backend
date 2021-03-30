<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFeedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('feeds', function (Blueprint $table) {
            $table->unsignedBigInteger('like')->nullable();
            $table->unsignedBigInteger('dislike')->nullable();
            $table->softDeletes();
        });

        Schema::create('feed_comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('feed_id');
            $table->unsignedBigInteger('comment_id')->nullable();
            $table->text('comment');
            $table->foreign('feed_id')->references('id')->on('feeds');
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
        Schema::dropIfExists('feed_comments');
    }
}
