<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnTypeToProductStoreInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_store_inventory', function (Blueprint $table) {
            $table->dropColumn('qty');
        });

        Schema::table('product_store_inventory', function (Blueprint $table) {
            $table->double('qty',8,2)->nullable();
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
