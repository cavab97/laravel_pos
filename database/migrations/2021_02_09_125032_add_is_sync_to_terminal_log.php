<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsSyncToTerminalLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('terminal_log', function (Blueprint $table) {
            $table->boolean('is_sync')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('terminal_log', function (Blueprint $table) {
            if (Schema::hasColumn('terminal_log', 'is_sync'))
            {
                $table->dropColumn(['is_sync']);
            }
        });
    }
}
