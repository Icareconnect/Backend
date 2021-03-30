<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RequestDetailUpdateData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request_history', function (Blueprint $table) {
            $table->unsignedInteger('tax_percantage')->after('request_id')->default(0)->nullable();
            $table->double('service_tax')->after('request_id')->default(0)->nullable();
            $table->double('discount')->after('request_id')->nullable();
            $table->double('without_discount')->after('request_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('request_history', function (Blueprint $table) {
            $table->dropColumn('tax_percantage');
            $table->dropColumn('service_tax');
            $table->dropColumn('discount');
            $table->dropColumn('without_discount');
        });
    }
}
