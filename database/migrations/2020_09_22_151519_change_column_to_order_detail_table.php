<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnToOrderDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('terminal_id')->nullable()->change();
            $table->unsignedBigInteger('app_id')->nullable()->change();
            $table->unsignedBigInteger('category_id')->nullable()->change();
            $table->dropColumn(['detail_attribute_id', 'detail_attribute_price']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_detail', function (Blueprint $table) {
            //
        });
    }
}
