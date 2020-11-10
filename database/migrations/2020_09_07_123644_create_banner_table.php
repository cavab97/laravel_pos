<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banner', function (Blueprint $table) {
            $table->integerIncrements('banner_id');
            $table->uuid('uuid');
            $table->string('title');
            $table->text('description');
            $table->string('banner_for_mobile');
            $table->string('banner_for_web');
            $table->string('status');
            $table->dateTime('updated_at');
            $table->integer('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banner');
    }
}
