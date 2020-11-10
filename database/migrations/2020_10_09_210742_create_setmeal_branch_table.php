<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSetmealBranchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setmeal_branch', function (Blueprint $table) {
            $table->bigIncrements('setmeal_branch_id');
            $table->uuid('uuid');
            $table->unsignedBigInteger('setmeal_id');
            $table->unsignedSmallInteger('branch_id');
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
        Schema::dropIfExists('setmeal_branch');
    }
}
