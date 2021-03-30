<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRequestHistoryTableAddField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('request_history', function (Blueprint $table) {
            $table->string('module_table')->nullable();
            $table->unsignedBigInteger('module_id')->nullable();
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
            $table->dropColumn('module_table');
            $table->dropColumn('module_id');
        });
    }
}
