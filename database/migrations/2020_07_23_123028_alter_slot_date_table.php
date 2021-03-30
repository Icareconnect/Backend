<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSlotDateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_provider_slots_dates', function (Blueprint $table) {
            $table->string('working_today',1)->default('y')->comment('y for Yes working,n for not working ');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('service_provider_slots_dates', function (Blueprint $table) {
            $table->dropColumn('working_today');
        });
    }
}
