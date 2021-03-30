<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditCustomMasterFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('custom_user_master_fields', function (Blueprint $table) {
            $table->string('field_value_type',30)->nullable()->after('deleted_at')->comment('field_value_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('custom_user_master_fields', function (Blueprint $table) {
            $table->dropColumn('field_value_type');
        });
    }
}
