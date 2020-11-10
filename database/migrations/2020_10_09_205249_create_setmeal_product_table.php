<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSetmealProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setmeal_product', function (Blueprint $table) {
            $table->bigIncrements('setmeal_product_id');
            $table->unsignedBigInteger('setmeal_id');
            $table->unsignedBigInteger('product_id');
            $table->double('quantity',8,2)->nullable();
            $table->dateTime('created_at')->nullable();
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
        Schema::dropIfExists('setmeal_product');
    }
}
