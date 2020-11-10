<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPermissionStatusToPosRolePermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pos_role_permission', function (Blueprint $table) {
            $table->tinyInteger('pos_rp_permission_status')->after('pos_rp_permission_id')->default(1)->comment('0 For InActive, 1 For Active, 2 For Deleted');
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
