<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddServerIdToShift extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shift', function (Blueprint $table) {
            $table->integer('server_id')->after('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shift', function (Blueprint $table) {
            if (Schema::hasColumn('shift', 'server_id'))
            {
                $table->dropColumn(['server_id']);
            }
        });
    }
}