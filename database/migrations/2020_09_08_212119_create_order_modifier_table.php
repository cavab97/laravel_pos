<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderModifierTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_modifier', function (Blueprint $table) {
            $table->bigIncrements('om_id');
            $table->uuid('uuid');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('detail_id');
            $table->unsignedBigInteger('terminal_id');
            $table->unsignedBigInteger('app_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('modifier_id');
            $table->float('om_amount');
            $table->tinyInteger('om_status');
            $table->dateTime('om_datetime');
            $table->unsignedBigInteger('om_by');
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
        Schema::dropIfExists('order_modifier');
    }
}
