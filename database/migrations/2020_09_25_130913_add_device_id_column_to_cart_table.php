<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeviceIdColumnToCartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cart', function (Blueprint $table) {
            $table->text('device_id')->after('user_id')->nullable();
            $table->integer('voucher_id')->after('discount_type')->nullable();
            $table->text('voucher_detail')->after('voucher_id')->nullable();
            $table->double('sub_total_after_discount',10,2)->after('voucher_detail')->nullable();
            $table->tinyInteger('source')->default(1)->comment('1 For WEB, 2 For APP')->after('sub_total_after_discount');
            $table->integer('total_item')->after('source')->nullable();
            $table->unsignedBigInteger('cart_payment_id')->after('total_item')->nullable();
            $table->text('cart_payment_response')->after('cart_payment_id')->nullable();
            $table->tinyInteger('cart_payment_status')->after('cart_payment_response')->comment('0 For Pending, 1 Complete');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cart', function (Blueprint $table) {
            //
        });
    }
}
