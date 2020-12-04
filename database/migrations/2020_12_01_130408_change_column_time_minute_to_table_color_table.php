<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnTimeMinuteToTableColorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('table_color', function (Blueprint $table) {
            $table->dropColumn(['time_minute']);
        });

        Schema::table('table_color', function (Blueprint $table) {
            $table->double('time_minute',10,2)->nullable()->after('uuid');
        });

        Schema::table('order', function (Blueprint $table) {
            $table->double('rounding_amount',10,2)->after('grand_total')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('table_color', function (Blueprint $table) {
            //
        });
    }
}
