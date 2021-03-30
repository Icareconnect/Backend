<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMasterPreferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('master_preferences', function (Blueprint $table) {
            $table->string('type',30)->default('preferences')->comment('preferences,symptoms');
            $table->string('image')->nullable();
            $table->longText('description')->nullable();
        });
       Schema::table('master_preferences_options', function (Blueprint $table) {
            $table->longText('description')->nullable();
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
            $table->dropColumn('type');
            $table->dropColumn('image');
            $table->dropColumn('description');
        });
        Schema::table('master_preferences_options', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
}
