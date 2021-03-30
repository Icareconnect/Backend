<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRequestTableChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->unsignedBigInteger('sp_service_type_id')->nullable();
            $table->foreign('sp_service_type_id')->references('id')->on('sp_service_types');
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
       Schema::table('requests', function (Blueprint $table) {
            // 1. Drop foreign key constraints
            $table->dropForeign(['sp_service_type_id']);
            // 2. Drop the column
            $table->dropColumn('sp_service_type_id');
        });
    }
}
