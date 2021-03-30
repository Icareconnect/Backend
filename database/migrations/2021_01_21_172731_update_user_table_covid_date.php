<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUserTableCovidDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dateTime('account_covid_rejected')->nullable()->after('remember_token');
            $table->string('custom_message')->nullable()->after('remember_token');
        });
        Schema::table('sp_additional_details', function (Blueprint $table) {
            $table->string('comment')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('account_covid_rejected');
            $table->dropColumn('custom_message');
        });
       Schema::table('sp_additional_details', function (Blueprint $table) {
            $table->dropColumn('comment');
        });
    }
}
