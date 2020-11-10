<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSetmealTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setmeal', function (Blueprint $table) {
            $table->bigIncrements('setmeal_id');
            $table->uuid('uuid');
            $table->string('name');
            $table->double('price',8,2)->nullable();
            $table->tinyInteger('status')->default(1)->comment('0 For InActive, 1 For Active');
            $table->dateTime('created_at')->nullable();
            $table->integer('created_by')->nullable();
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
        Schema::dropIfExists('setmeal');
    }
}
