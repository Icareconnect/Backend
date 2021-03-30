<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('address')->nullable();
            $table->string('avatar')->nullable();
            $table->date('dob');
            $table->string('qualification')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('experience')->nullable();
            $table->string('speciality')->nullable();
            $table->string('call_price')->nullable()->comment('per second charge');
            $table->string('chat_price')->nullable()->comment('per minute charge');;
            $table->string('rating')->nullable();
            $table->string('about');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });

        Schema::create('feeds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('image')->nullable();
            $table->text('description');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });

        Schema::create('wallets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->double('balance');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('amount');
            $table->string('transaction_type')->comment('deposit withdrawal add_money refund payout');
            $table->enum('status',['success','pending','failed']);
            $table->unsignedBigInteger('wallet_id');
            $table->unsignedBigInteger('request_id')->nullable();
            $table->double('closing_balance')->nullable();
            $table->foreign('wallet_id')->references('id')->on('wallets');
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('from');
            $table->unsignedBigInteger('to');
            $table->foreign('from')->references('id')->on('users');
            $table->foreign('to')->references('id')->on('users');
            $table->unsignedBigInteger('transaction_id');
            $table->foreign('transaction_id')->references('id')->on('transactions');
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type')->comment('chat','feed','call');
            $table->timestamps();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('charges');
            $table->unsignedBigInteger('duration');
            $table->unsignedBigInteger('consultant_id');
            $table->unsignedBigInteger('service_id');
            $table->foreign('consultant_id')->references('id')->on('users');
            $table->foreign('service_id')->references('id')->on('services');
            $table->timestamps();
        });

        Schema::create('requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('booking_date');
            $table->unsignedBigInteger('from_user');
            $table->unsignedBigInteger('to_user');
            $table->unsignedBigInteger('service_id');
            $table->foreign('from_user')->references('id')->on('users');
            $table->foreign('to_user')->references('id')->on('users');
            $table->foreign('service_id')->references('id')->on('services');
            $table->timestamps();
        });

        Schema::create('request_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('duration');
            $table->string('total_charges');
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('request_id');
            $table->foreign('request_id')->references('id')->on('requests');
            $table->timestamps();
        });

        Schema::create('commissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('request_id');
            $table->foreign('request_id')->references('id')->on('requests');
            $table->string('charges');
            $table->timestamps();
        });


        Schema::create('feedbacks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('consultant_id');
            $table->unsignedBigInteger('from_user');
            $table->unsignedBigInteger('request_id')->nullable();
            $table->foreign('consultant_id')->references('id')->on('users');
            $table->foreign('from_user')->references('id')->on('users');
            $table->float('rating', 2, 1)->nullable();
            $table->string('comment');
            $table->timestamps();
        });

        
        Schema::create('verifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('phone');
            $table->string('code');
            $table->string('country_code');
            $table->enum('status',['pending','failed','verified'])->default('pending');
            $table->dateTime('expired_at');
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
        Schema::dropIfExists('profles');
        Schema::dropIfExists('feeds');
        Schema::dropIfExists('wallets');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('services');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('requests');
        Schema::dropIfExists('request_history');
        Schema::dropIfExists('commissions');
        Schema::dropIfExists('feedbacks');
        Schema::dropIfExists('verifications');
    }
}
