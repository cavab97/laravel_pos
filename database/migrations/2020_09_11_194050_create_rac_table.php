<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRacTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rac', function (Blueprint $table) {
            $table->bigIncrements('rac_id');
            $table->uuid('uuid');
            $table->unsignedBigInteger('branch_id');
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
        Schema::dropIfExists('rac');
    }
}
