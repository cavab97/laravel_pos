<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLimitAmountColumnToVoucherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('voucher', function (Blueprint $table) {
            $table->double('minimum_amount',15,4)->after('voucher_discount');
            $table->double('maximum_amount',15,4)->after('minimum_amount');
            $table->integer('uses_total')->after('maximum_amount');
            $table->integer('uses_customer')->after('uses_total');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('voucher', function (Blueprint $table) {
            //
        });
    }
}
