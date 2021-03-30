<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMasterPre extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_preferences', function (Blueprint $table) {
            $table->string('show_on')->comment('both,sp,user')->default('both');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('master_preferences', function (Blueprint $table) {
            $table->dropColumn('show_on');
        });
    }
}
