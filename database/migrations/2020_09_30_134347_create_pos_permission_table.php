<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePosPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pos_permission', function (Blueprint $table) {
            $table->smallIncrements('pos_permission_id');
            $table->string('pos_permission_name','40');
            $table->dateTime('pos_permission_updated_at')->nullable();
            $table->unsignedBigInteger('pos_permission_updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pos_permission');
    }
}
