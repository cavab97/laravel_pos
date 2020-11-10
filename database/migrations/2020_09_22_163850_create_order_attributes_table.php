<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_attributes', function (Blueprint $table) {
            $table->bigIncrements('oa_id');
            $table->uuid('uuid');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('detail_id');
            $table->unsignedBigInteger('terminal_id')->nullable();
            $table->unsignedBigInteger('app_id')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('attribute_id');
            $table->double('attr_price',10,2);
            $table->integer('ca_id')->nullable();
            $table->tinyInteger('oa_status')->comment('0 For InActive, 1 For Active');
            $table->dateTime('oa_datetime')->nullable();
            $table->integer('oa_by')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_attributes');
    }
}
