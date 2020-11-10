<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryBranch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_branch', function (Blueprint $table) {
            $table->bigIncrements('cb_id');
            $table->uuid('uuid');
            $table->unsignedBigInteger('category_id');
            $table->unsignedSmallInteger('branch_id');
            $table->integer('display_order');
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
        Schema::dropIfExists('category_branch');
    }
}
