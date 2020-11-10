<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTerminalLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('terminal_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid');
            $table->unsignedBigInteger('terminal_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->enum('module_name',['','Login','Verify Key','Customer','Customer Address','Role','Attribute','Modifier','Category','Category Branch','Kitchen','Clear','Check in','Check out','Branch','Shift open','Shift close','Product','Delete all items','Save order','Open order','Split order','Invoice','Refund','Open Cash Drawer','Print Receipt','Print Test Receipt','Auto sync','Print Full Receipt','Print Half Receipt'])->nullable()->default('');
            $table->text('description')->nullable();
            $table->date('activity_date')->nullable();
            $table->time('activity_time')->nullable();
            $table->string('table_name')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
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
        Schema::dropIfExists('terminal_log');
    }
}
