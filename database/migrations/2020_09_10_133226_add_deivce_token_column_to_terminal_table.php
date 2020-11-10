<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeivceTokenColumnToTerminalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('terminal', function (Blueprint $table) {
            $table->string('terminal_device_token')->after('terminal_device_id')->nullable();
            $table->dateTime('terminal_verified_at')->after('terminal_is_mother')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('terminal', function (Blueprint $table) {
            //
        });
    }
}
