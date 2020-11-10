<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerLiquorInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_liquor_inventory', function (Blueprint $table) {
            $table->bigIncrements('cl_id');
            $table->uuid('uuid');
            $table->unsignedBigInteger('cl_customer_id');
            $table->unsignedBigInteger('cl_product_id');
            $table->unsignedBigInteger('cl_branch_id');
            $table->integer('cl_rac_id')->nullable();
            $table->integer('cl_box_id')->nullable();
            $table->tinyInteger('type')->comment('1 For Other, 2 For Beer')->default(1);
            $table->double('cl_total_quantity')->nullable();
            $table->date('cl_expired_on')->nullable();
            $table->double('cl_left_quantity')->nullable();
            $table->tinyInteger('status')->comment('0 For InActive, 1 For Active')->default(1);
            $table->dateTime('updated_at')->nullable();
            $table->integer('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_liquor_inventory');
    }
}
