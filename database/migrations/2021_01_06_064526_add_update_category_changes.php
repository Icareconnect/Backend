<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUpdateCategoryChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
             $table->text('description_text')->nullable()->after('description')->comment('description_text');
             $table->text('banner')->nullable()->after('description')->comment('banner');
             $table->text('video')->nullable()->after('description')->comment('video');
        });

        Schema::table('filter_type_options', function (Blueprint $table) {
             $table->text('banner')->nullable()->after('description')->comment('banner');
             $table->text('video')->nullable()->after('description')->comment('video');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('description_text');
            $table->dropColumn('banner');
            $table->dropColumn('video');
        });
        Schema::table('filter_type_options', function (Blueprint $table) {
            $table->dropColumn('banner');
            $table->dropColumn('video');
        });
    }
}
