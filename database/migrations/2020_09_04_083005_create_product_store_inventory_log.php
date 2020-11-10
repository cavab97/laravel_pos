<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductStoreInventoryLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_store_inventory_log', function (Blueprint $table) {
            $table->bigIncrements('il_id');
            $table->uuid('uuid');
            $table->unsignedBigInteger('inventory_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('employe_id');
            $table->tinyInteger('il_type')->comment('1 For Add, 2 For Deduct')->default(1);
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
        Schema::dropIfExists('product_store_inventory_log');
    }
}
