<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MasterPreferencesOptionsUpdateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_preferences_options', function (Blueprint $table) {
            $table->unsignedBigInteger('map_option_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_preferences_options', function (Blueprint $table) {
            $table->dropColumn('map_option_id');
        });
    }
}
