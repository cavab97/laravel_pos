<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnToOrderModifierTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_modifier', function (Blueprint $table) {
            $table->unsignedBigInteger('terminal_id')->nullable()->change();
            $table->unsignedBigInteger('app_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_modifier', function (Blueprint $table) {
            //
        });
    }
}
