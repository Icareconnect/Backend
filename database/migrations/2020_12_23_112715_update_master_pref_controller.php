<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMasterPrefController extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_preferences', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('deleted_at')->comment('custom created_by');
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
            $table->dropColumn('created_by');
        });
    }
}
