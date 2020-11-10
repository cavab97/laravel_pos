<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table', function (Blueprint $table) {
            $table->bigIncrements('table_id');
            $table->uuid('uuid');
            $table->unsignedSmallInteger('branch_id');
            $table->string('table_name');
            $table->tinyInteger('table_type')->default(1)->comment('1 for Dine-in 2 For Take away');
            $table->string('table_qr');
            $table->integer('table_capacity');
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
        Schema::dropIfExists('table');
    }
}
