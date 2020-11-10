<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_detail', function (Blueprint $table) {
            $table->bigIncrements('cart_detail_id');
            $table->unsignedBigInteger('cart_id');
            $table->text('localID')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->string('product_name')->nullable();
            $table->double('product_price',10,2);
            $table->double('product_old_price',10,2);
            $table->double('product_qty',10,2);
            $table->integer('tax_id')->nullable();
            $table->double('tax_value',10,2);
            $table->double('discount',15,2);
            $table->integer('discount_type')->nullable();
            $table->text('remark')->nullable();
            $table->tinyInteger('is_deleted')->default(0)->comment('0 For No, 1 For Yes');
            $table->tinyInteger('is_send_kichen')->default(0)->comment('0 For No, 1 For Yes');
            $table->tinyInteger('has_composite_inventory')->default(0)->comment('0 For No, 1 For Yes');
            $table->string('item_unit')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cart_detail');
    }
}
