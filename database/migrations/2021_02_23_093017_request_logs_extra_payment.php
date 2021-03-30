<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RequestLogsExtraPayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request_history', function (Blueprint $table) {
            $table->double('extra_payment')->after('request_id')->nullable();
            $table->string('extra_payment_status')->after('request_id')->nullable();
            $table->string('extra_payment_description')->after('request_id')->nullable();
            $table->dateTime('extra_payment_datetime')->after('request_id')->nullable();
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
            $table->dropColumn('extra_payment');
            $table->dropColumn('extra_payment_status');
            $table->dropColumn('extra_payment_description');
            $table->dropColumn('extra_payment_datetime');
        });
    }
}
