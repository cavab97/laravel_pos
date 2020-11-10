<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoucherHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucher_history', function (Blueprint $table) {
            $table->bigIncrements('voucher_history_id');
            $table->uuid('uuid');
            $table->integer('voucher_id');
            $table->bigInteger('order_id');
            $table->integer('user_id');
            $table->double('amount', 15, 4);
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('voucher_history');
    }
}
