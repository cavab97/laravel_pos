<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFocProductColumnToCartDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cart_detail', function (Blueprint $table) {
            $table->tinyInteger('isFoc_Product')->after('is_deleted')->comment('0 For No, 1 For Yes')->default(0);
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
