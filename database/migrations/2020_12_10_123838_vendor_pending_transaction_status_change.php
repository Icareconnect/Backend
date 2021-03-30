<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class VendorPendingTransactionStatusChange extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `transactions` CHANGE `status` `status` varchar(255) COLLATE 'utf8mb4_unicode_ci' NOT NULL DEFAULT 'pending' AFTER `transaction_type`");
        // Schema::table('transactions', function (Blueprint $table) {
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
