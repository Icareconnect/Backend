<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TableChangesNew extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('categories', function (Blueprint $table) {
            $table->string('description');
            $table->string('multi_select',1);
            $table->string('color_code',6);
            $table->string('enable',1);
            $table->string('image_icon')->nullable();
        });

        Schema::create('filter_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('category_id');
            $table->string('is_multi',1)->comment('Is Multi_select options flag 0 for false, 1  for true');
            $table->string('filter_name')->comment('Show on User Side');
            $table->string('preference_name')->comment('to show to service provider');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('filter_type_options', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('filter_type_id');
            $table->string('option_name');
            $table->foreign('filter_type_id')->references('id')->on('filter_types');
            $table->timestamps();
        });

        // Schema::create('service_types', function (Blueprint $table) {
        //     $table->bigIncrements('id');
        //     $table->string('service_name');
        //     $table->string('description')->nullable();
        //     $table->string('need_availability',1)->default('0');
        //     $table->timestamps();
        // });

        Schema::create('category_service_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('service_id');
            $table->string('is_active',1)->default('1')->comment('active bydefault');
            $table->double('price_fixed')->nullable();
            $table->double('price_minimum');
            $table->double('price_maximum');
            $table->integer('minimum_duration');
            $table->integer('gap_duration');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('service_id')->references('id')->on('services');
            $table->timestamps();
        }); 


        Schema::table('profiles', function (Blueprint $table) {
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('long', 10, 7)->nullable();
        });

        Schema::create('sp_service_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sp_id');
            $table->unsignedBigInteger('service_id');
            $table->double('price')->nullable()->comment('if not fixed, if fixed, pick from Category_Service_type');
            $table->string('minimmum_heads_up');
            $table->foreign('service_id')->references('id')->on('services');
            $table->timestamps();
        });

        Schema::create('sp_availability', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('service_id');
            $table->time('start_time');
            $table->time('end_time');
            $table->tinyInteger('day');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('service_id')->references('id')->on('services');
            $table->timestamps();
        });

        Schema::create('clusters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('description');
            $table->timestamps();
        });

        Schema::create('cluster_category', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('cluster_id');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('cluster_id')->references('id')->on('clusters');
            $table->timestamps();
        });

        Schema::create('banners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('image_web')->nullable();
            $table->string('image_mobile')->nullable();
            $table->string('position');
            $table->string('banner_type');
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('sp_id')->nullable();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('sp_id')->references('id')->on('users');
            $table->foreign('class_id')->references('id')->on('ct_classes');
            $table->timestamps();
        });

        Schema::create('coupons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->string('percent_off');
            $table->string('value_off');
            $table->string('minimum_value');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('limit');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('service_id')->references('id')->on('services');
            $table->timestamps();
        });

        Schema::create('coupon_used', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('coupon_id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('coupon_id')->references('id')->on('coupons');
            $table->foreign('user_id')->references('id')->on('users');
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
         Schema::table('categories', function (Blueprint $table) {
            // 1. Drop foreign key constraints
            // $table->dropForeign(['store_id']);
            // 2. Drop the column
            $table->dropColumn('description');
            $table->dropColumn('color_code');
            $table->dropColumn('image_icon');
        });

         Schema::dropIfExists('filter_types');
         Schema::dropIfExists('filter_type_options');
         Schema::dropIfExists('category_service_types');

         Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('lat');
            $table->dropColumn('long');
        });
         Schema::dropIfExists('sp_service_types');
         Schema::dropIfExists('sp_availability');
         Schema::dropIfExists('cluster');
         Schema::dropIfExists('cluster_category');
         Schema::dropIfExists('banners');
         Schema::dropIfExists('coupons');
         Schema::dropIfExists('coupon_used');
    }
}
