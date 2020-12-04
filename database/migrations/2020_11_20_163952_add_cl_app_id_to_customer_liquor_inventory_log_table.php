<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClAppIdToCustomerLiquorInventoryLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_liquor_inventory_log', function (Blueprint $table) {
            $table->integer('cl_appId')->after('cl_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_liquor_inventory_log', function (Blueprint $table) {
            //
        });
    }
}
