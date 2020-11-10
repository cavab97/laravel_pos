<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannerBranchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banner_branch', function (Blueprint $table) {
            $table->bigIncrements('bb_id');
            $table->uuid('uuid');
            $table->unsignedBigInteger('banner_id');
            $table->unsignedBigInteger('branch_id');
            $table->tinyInteger('status')->comment('0 For InActive, 1 For Active, 2 For Deleted')->default(1);
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
        Schema::dropIfExists('banner_branch');
    }
}
