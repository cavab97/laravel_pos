<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order', function (Blueprint $table) {
            $table->bigIncrements('order_id');
            $table->uuid('uuid');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('terminal_id');
            $table->unsignedBigInteger('app_id');
            $table->string('table_no');
            $table->string('invoice_no');
            $table->unsignedBigInteger('customer_id');
            $table->string('tax_percent');
            $table->float('tax_amount');
            $table->unsignedBigInteger('voucher_id')->nullable();
            $table->float('voucher_amount')->nullable();
            $table->float('sub_total');
            $table->float('sub_total_after_discount');
            $table->float('grand_total');
            $table->tinyInteger('order_source')->default(2)->comment('1 For Web,2 For App');
            $table->tinyInteger('order_status')->default(1)->comment('1 For New,2 For Ongoing,3 For cancelled,4 For Completed,5 For Refunded');
            $table->integer('order_item_count');
            $table->date('order_date');
            $table->unsignedBigInteger('order_by');
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
        Schema::dropIfExists('order');
    }
}
