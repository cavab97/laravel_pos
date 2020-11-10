<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category', function (Blueprint $table) {
            $table->bigIncrements('category_id');
            $table->uuid('uuid');
            $table->string('name');
            $table->string('slug');
            $table->string('category_icon');
            $table->bigInteger('parent_id');
            $table->tinyInteger('is_for_web')->default(0)->comment('0 for Backend 1 for Web');
            $table->tinyInteger('status')->default(1)->comment('1 For Active,0 For De-active');
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
        Schema::dropIfExists('category');
    }
}
