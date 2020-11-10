<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTerminalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('terminal', function (Blueprint $table) {
            $table->bigIncrements('terminal_id');
            $table->uuid('uuid');
            $table->string('terminal_device_id')->nullable();
            $table->unsignedSmallInteger('branch_id');
            $table->string('terminal_name')->nullable();
            $table->string('terminal_key')->nullable();
            $table->tinyInteger('terminal_type')->default(1)->comment('1 for Cashier,2 For Waiter,3 For Attendance');
            $table->tinyInteger('terminal_is_mother')->default(1)->comment('1 for Yes , 0 For No');
            $table->tinyInteger('status')->default(1)->comment('0 For InActive, 1 For Active');
            $table->dateTime('updated_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('terminal');
    }
}
