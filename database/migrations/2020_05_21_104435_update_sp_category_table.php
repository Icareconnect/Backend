<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSpCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('sp_service_types');
        Schema::create('sp_service_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sp_id');
            $table->unsignedBigInteger('category_service_id');
            $table->string('duration',10)->nullable();
            $table->string('available',1)->nullable();
            $table->double('price')->nullable()->comment('if not fixed, if fixed, pick from Category_Service_type');
            $table->string('minimmum_heads_up');
            $table->foreign('category_service_id')->references('id')->on('category_service_types');
            $table->softDeletes();
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
        Schema::dropIfExists('sp_service_types');
    }
}
