<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart', function (Blueprint $table) {
            $table->bigIncrements('cart_id');
            $table->uuid('uuid');
            $table->text('localID')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('table_id')->nullable();
            $table->double('sub_total',15,2);
            $table->double('discount',15,2);
            $table->integer('discount_type')->nullable();
            $table->text('remark')->nullable();
            $table->double('tax',15,2);
            $table->double('grand_total',15,2);
            $table->double('total_qty',10,2);
            $table->unsignedBigInteger('customer_terminal')->nullable();
            $table->tinyInteger('is_deleted')->default(0)->comment('0 For No, 1 For Yes');
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
        Schema::dropIfExists('cart');
    }
}
