<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_detail', function (Blueprint $table) {
            $table->bigIncrements('detail_id');
            $table->uuid('uuid');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('terminal_id');
            $table->unsignedBigInteger('app_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('detail_attribute_id');
            $table->float('detail_attribute_price');
            $table->float('detail_amount');
            $table->integer('detail_qty');
            $table->tinyInteger('detail_status')->default(1)->comment('1 For Placed,2 For Served,3 For Cancelled,4 For Returned');
            $table->dateTime('detail_datetime');
            $table->unsignedBigInteger('detail_by');
            $table->dateTime('updated_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_detail');
    }
}
