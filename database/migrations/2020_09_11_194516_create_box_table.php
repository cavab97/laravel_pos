<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoxTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('box', function (Blueprint $table) {
            $table->bigIncrements('box_id');
            $table->uuid('uuid');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('rac_id');
            $table->string('name');
            $table->string('slug');
            $table->tinyInteger('status')->default(1)->comment('0 For disabled, 1 For enabled,2 For deleted');
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
        Schema::dropIfExists('box');
    }
}
