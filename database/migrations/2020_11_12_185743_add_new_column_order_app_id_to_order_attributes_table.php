<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnOrderAppIdToOrderAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_attributes', function (Blueprint $table) {
            $table->integer('order_app_id')->after('app_id')->nullable();
            $table->integer('detail_app_id')->after('order_app_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_attributes', function (Blueprint $table) {
            //
        });
    }
}
