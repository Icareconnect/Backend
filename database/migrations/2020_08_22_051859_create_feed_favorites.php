<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedFavorites extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feed_favorites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('favorite');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('feed_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('feed_id')->references('id')->on('feeds');
            $table->timestamps();
        });

        Schema::create('feed_views', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('feed_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('feed_id')->references('id')->on('feeds');
            $table->timestamps();
        });

        Schema::table('feeds', function (Blueprint $table) {
            $table->bigInteger('views')->default(0);
            $table->bigInteger('favorite')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feed_favorites');
        Schema::dropIfExists('feed_views');
        Schema::table('feeds', function (Blueprint $table) {
            $table->dropColumn('views');
            $table->dropColumn('favorite');
        });
    }
}
