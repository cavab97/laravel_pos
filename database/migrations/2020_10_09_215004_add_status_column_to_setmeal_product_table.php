<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusColumnToSetmealProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('setmeal_product', function (Blueprint $table) {
            $table->tinyInteger('status')->comment('0 For InActive, 1 For Active, 2 For Delete')->default(1)->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('setmeal_product', function (Blueprint $table) {
            //
        });
    }
}
