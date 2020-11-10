<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product', function (Blueprint $table) {
            $table->bigIncrements('product_id');
            $table->uuid('uuid');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('sku', 25);
            $table->unsignedInteger('price_type_id');
            $table->string('price_type_value');
            $table->double('price',10,2);
            $table->double('old_price',10,2)->nullable();
            $table->tinyInteger('has_inventory')->comment('0 For Off, 1 For On')->default(1);
            $table->tinyInteger('status')->comment('0 For disabled, 1 For enabled')->default(1);
            $table->dateTime('updated_at')->nullable();
            $table->softDeletes();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product');
    }
}
