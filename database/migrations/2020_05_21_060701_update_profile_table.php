<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('profiles', function (Blueprint $table) {
            $table->string('location_name')->nullable();
            $table->string('title')->nullable();
            $table->date('working_since')->nullable();
        });
         Schema::table('users', function (Blueprint $table) {
            $table->dateTime('account_verified')->nullable();
            $table->integer('account_step')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('location_name');
            $table->dropColumn('title');
            $table->dropColumn('working_since');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('account_verified');
            $table->dropColumn('account_step');
        });
    }
}
