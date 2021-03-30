<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dateTime('insurance_verified')->nullable();
        });

        Schema::create('custom_infos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('info_type');
            $table->json('raw_detail')->nullable();
            $table->string('ref_table')->comment('Refrence Table for e.g users');
            $table->unsignedBigInteger('ref_table_id')->nullable()->comment('Refrence Table Id for e.g users id ');
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('insurance_verified');
        });

        Schema::dropIfExists('custom_infos');
    }
}
