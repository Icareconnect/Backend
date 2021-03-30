<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableService extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('services', function (Blueprint $table) {
            $table->string('description')->nullable();
            $table->string('need_availability',1)->default('0');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('services', function (Blueprint $table) {
            // 1. Drop foreign key constraints
            // $table->dropForeign(['store_id']);
            // 2. Drop the column
            $table->dropColumn('description');
            $table->dropColumn('need_availability');
        });
    }
}
