<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCityStateColumnToCustomerAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_address', function (Blueprint $table) {
            $table->smallInteger('country_id')->after('longitude')->nullable();
            $table->smallInteger('state_id')->after('country_id')->nullable();
            $table->integer('city_id')->after('state_id')->nullable();
            $table->integer('zipcode')->after('city_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_address', function (Blueprint $table) {
            //
        });
    }
}
