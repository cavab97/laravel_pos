<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTableNoColumnOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order', function (Blueprint $table) {
            $table->unsignedBigInteger('terminal_id')->nullable()->change();
            $table->unsignedBigInteger('app_id')->nullable()->change();
            $table->string('table_no')->nullable()->change();
            $table->unsignedBigInteger('table_id')->after('table_no')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable()->change();
            $table->string('tax_percent')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
