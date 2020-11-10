<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePosRolePermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pos_role_permission', function (Blueprint $table) {
            $table->smallIncrements('pos_rp_id');
            $table->uuid('pos_rp_uuid');
            $table->unsignedBigInteger('pos_rp_permission_id');
            $table->dateTime('pos_rp_updated_at')->nullable();
            $table->unsignedBigInteger('pos_rp_updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pos_role_permission');
    }
}
