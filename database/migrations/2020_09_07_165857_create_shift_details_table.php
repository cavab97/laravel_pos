<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_details', function (Blueprint $table) {
            $table->bigIncrements('shift_details_id');
            $table->uuid('uuid');
            $table->unsignedBigInteger('terminal_id');
            $table->bigInteger('app_id')->nullable();
            $table->bigInteger('shift_id');
            $table->bigInteger('invoice_id')->nullable();
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
        Schema::dropIfExists('shift_details');
    }
}
