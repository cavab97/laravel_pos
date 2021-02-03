<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstCartDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_cart_detail', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('cart_id')->nullable();
            $table->integer('local_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->integer('printer_id')->nullable();
            $table->string('res_no')->nullable();
            $table->string('product_name')->nullable();
            $table->string('name_2')->nullable();
            $table->float('product_price')->nullable();
            $table->float('product_net_price')->nullable();
            $table->float('product_detail_amount')->nullable();
            $table->float('product_qty')->nullable();
            $table->integer('tax_id')->nullable();
            $table->float('tax_value')->nullable();
            $table->string('remark')->nullable();
            $table->integer('discount_type')->nullable();
            $table->float('discount_amount')->nullable();
            $table->string('discount_remark')->nullable();
            $table->string('item_unit')->nullable();
            $table->string('cart_detail')->nullable();
            $table->string('setmeal_product_detail')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('is_set_meal')->nullable();
            $table->integer('is_foc_product')->nullable();
            $table->integer('is_send_kichen')->nullable();
            $table->integer('has_rac_managemant')->nullable();
            $table->integer('has_composite_inventory')->nullable();
            $table->integer('attr_name')->nullable();
            $table->integer('modi_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_cart_detail');
    }
}
