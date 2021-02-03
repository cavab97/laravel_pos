<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstCartSubDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_cart_sub_detail', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('cart_details_id')->nullable();
            $table->string('local_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->integer('modifier_id')->nullable();
            $table->double('modifier_price')->nullable();
            $table->double('attr_price')->nullable();
            $table->integer('attribute_id')->nullable();
            $table->string('res_no')->nullable();
            $table->integer('ca_id')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_cart_sub_detail');
    }
}
