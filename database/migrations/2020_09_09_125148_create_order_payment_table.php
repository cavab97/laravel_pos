<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderPaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_payment', function (Blueprint $table) {
            $table->bigIncrements('op_id');
            $table->uuid('uuid');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('terminal_id');
            $table->unsignedBigInteger('app_id');
            $table->unsignedBigInteger('op_method_id');
            $table->float('op_amount');
            $table->text('op_method_response');
            $table->tinyInteger('op_status')->comment('1 For success , 2 For Failed');
            $table->dateTime('op_datetime');
            $table->unsignedBigInteger('op_by');
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
        Schema::dropIfExists('order_payment');
    }
}
