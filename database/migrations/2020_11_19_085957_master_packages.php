<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MasterPackages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_packages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->double('price');
            $table->longText('description')->nullable();
            $table->string('color_code',6);
            $table->longText('image_icon')->nullable();
            $table->string('type');
            $table->unsignedBigInteger('created_by')->nullable()->comment('Created By Admin');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('supports', function (Blueprint $table) {
            $table->unsignedBigInteger('master_package_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_packages');

        Schema::table('supports', function (Blueprint $table) {
            $table->dropColumn('master_package_id');
        });
    }
}
