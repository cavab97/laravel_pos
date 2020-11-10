<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift', function (Blueprint $table) {
            $table->bigIncrements('shift_id');
            $table->uuid('uuid');
            $table->unsignedBigInteger('terminal_id');
            $table->bigInteger('app_id')->nullable();
            $table->bigInteger('user_id');
            $table->bigInteger('branch_id');
            $table->double('start_amount',10,2)->nullable();
            $table->double('end_amount',10,2)->nullable();
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
        Schema::dropIfExists('shift');
    }
}
