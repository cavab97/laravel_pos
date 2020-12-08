<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAppIdColumnToVoucherHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('voucher_history', function (Blueprint $table) {
            $table->integer('app_id')->after('order_id')->nullable();
            $table->integer('terminal_id')->after('uuid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('voucher_history', function (Blueprint $table) {
            //
        });
    }
}
