<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartSubDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_sub_detail', function (Blueprint $table) {
            $table->bigIncrements('csd_id');
            $table->unsignedBigInteger('cart_detail_id');
            $table->unsignedBigInteger('cart_id');
            $table->text('localID')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->integer('modifier_id')->nullable();
            $table->double('modifire_price',10,2)->nullable();
            $table->integer('attribute_id')->nullable();
            $table->double('attribute_price',10,2)->nullable();
            $table->integer('ca_id')->nullable();
            $table->tinyInteger('is_deleted')->default(0)->comment('0 For No, 1 For Yes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cart_sub_detail');
    }
}
