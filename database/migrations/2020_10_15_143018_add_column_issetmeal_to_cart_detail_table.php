<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIssetmealToCartDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cart_detail', function (Blueprint $table) {
            $table->tinyInteger('issetMeal')->default(0)->after('product_qty')->comment('0 For No, 1 For Yes');
            $table->text('setmeal_product_detail')->after('issetMeal')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cart_detail', function (Blueprint $table) {
            //
        });
    }
}
