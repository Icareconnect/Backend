<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAdditionalDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sp_additional_details', function (Blueprint $table) {
            $table->text('file_name')->change();
            $table->string('status')->comment('approved,in_progress,declined')->default('in_progress');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('sp_additional_details', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
