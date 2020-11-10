<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRacAndBoxColumnToProductStoreInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_store_inventory', function (Blueprint $table) {
            $table->integer('rac_id')->after('qty')->nullable();
            $table->integer('box_id')->after('rac_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_store_inventory', function (Blueprint $table) {
            //
        });
    }
}
