<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRemarkToReservation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reservation', function (Blueprint $table) {
            $table->integer('pax')->after('terminal_id')->nullable();
            $table->integer('table_id')->after('pax')->nullable();
            $table->integer('remark')->after('table_id')->nullable();
            $table->boolean('is_arr')->after('remark')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reservation', function (Blueprint $table) {
            if (Schema::hasColumn('reservation', 'remark'))
            {
                $table->dropColumn(['remark']);
            }
            if (Schema::hasColumn('reservation', 'table_id'))
            {
                $table->dropColumn(['table_id']);
            }
            if (Schema::hasColumn('reservation', 'pax'))
            {
                $table->dropColumn(['pax']);
            }
            if (Schema::hasColumn('reservation', 'is_arr'))
            {
                $table->dropColumn(['is_arr']);
            }
        });
    }
}
