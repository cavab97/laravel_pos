<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPosPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_pos_permission', function (Blueprint $table) {
            $table->bigIncrements('up_pos_id');
            $table->uuid('up_pos_uuid');
            $table->unsignedBigInteger('user_id');
            $table->tinyInteger('status')->comment('0 For InActive, 1 For Active')->default(1);
            $table->unsignedBigInteger('pos_permission_id');
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
        Schema::dropIfExists('user_pos_permission');
    }
}
