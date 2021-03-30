<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RequestDetailUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request_details', function (Blueprint $table) {
            $table->string('country_code')->nullable();
            $table->unsignedBigInteger('phone_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('request_details', function (Blueprint $table) {
            $table->dropColumn('country_code');
            $table->dropColumn('phone_number');
        });
    }
}
