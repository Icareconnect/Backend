<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRequestTableData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->string('admin_status',50)->default('pending')->comment('pending,approved,declined')->nullable();
            $table->double('user_by_hours')->nullable();
            $table->double('verified_hours')->nullable();
            $table->json('custom_info_1')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn('admin_status');
            $table->dropColumn('user_by_hours');
            $table->dropColumn('verified_hours');
            $table->dropColumn('custom_info_1');
        });
    }
}
