<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnNullableToCartDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cart_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('product_old_price')->nullable()->change();
            $table->unsignedBigInteger('tax_value')->nullable()->change();
            $table->unsignedBigInteger('discount')->nullable()->change();
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
