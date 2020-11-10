<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrinterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('printer', function (Blueprint $table) {
            $table->bigIncrements('printer_id');
            $table->uuid('uuid');
            $table->unsignedBigInteger('branch_id');
            $table->string('printer_name');
            $table->string('printer_ip');
            $table->tinyInteger('printer_is_cashier')->default(0)->comment('1 for Assigned,0 For not assigned');
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
        Schema::dropIfExists('printer');
    }
}
