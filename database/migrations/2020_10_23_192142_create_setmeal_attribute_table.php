<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSetmealAttributeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setmeal_attribute', function (Blueprint $table) {
            $table->bigIncrements('setmeal_att_id');
            $table->uuid('uuid');
            $table->unsignedBigInteger('setmeal_id');
            $table->unsignedBigInteger('ca_id');
            $table->unsignedBigInteger('attribute_id');
            $table->double('price',10,2);
            $table->tinyInteger('status')->comment('0 For InActive, 1 For Active')->default(1);
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
        Schema::dropIfExists('setmeal_attribute');
    }
}
