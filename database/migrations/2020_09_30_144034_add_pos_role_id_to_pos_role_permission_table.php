<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPosRoleIdToPosRolePermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pos_role_permission', function (Blueprint $table) {
            $table->unsignedBigInteger('pos_rp_role_id')->after('pos_rp_uuid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pos_role_permission', function (Blueprint $table) {
            //
        });
    }
}
