<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnTypeToProductStoreInventoryLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_store_inventory_log', function (Blueprint $table) {
            $table->dropColumn(['qty','qty_before_change','qty_after_change']);
        });

        Schema::table('product_store_inventory_log', function (Blueprint $table) {
            $table->double('qty',8,2)->nullable()->after('il_type');
            $table->double('qty_before_change',8,2)->nullable()->after('qty');
            $table->double('qty_after_change',8,2)->nullable()->after('qty_before_change');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_store_inventory_log', function (Blueprint $table) {
            //
        });
    }
}
