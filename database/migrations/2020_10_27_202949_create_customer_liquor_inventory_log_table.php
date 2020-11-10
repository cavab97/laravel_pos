<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerLiquorInventoryLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_liquor_inventory_log', function (Blueprint $table) {
            $table->bigIncrements('li_id');
            $table->uuid('uuid');
            $table->unsignedBigInteger('cl_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('customer_id');
            $table->tinyInteger('li_type')->comment('1 For Add, 2 For Deduct')->default(1);
            $table->string('qty');
            $table->string('qty_before_change')->nullable();
            $table->string('qty_after_change')->nullable();
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
        Schema::dropIfExists('customer_liquor_inventory_log');
    }
}
