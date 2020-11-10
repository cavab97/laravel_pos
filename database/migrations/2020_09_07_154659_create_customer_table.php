<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer', function (Blueprint $table) {
            $table->bigIncrements('customer_id');
            $table->uuid('uuid');
            $table->integer('app_id')->nullable();
            $table->integer('terminal_id')->nullable();
            $table->string('first_name',50)->nullable();
            $table->string('last_name',50)->nullable();
            $table->string('name',50)->nullable();
            $table->string('email',100)->nullable();
            $table->integer('phonecode')->nullable();
            $table->string('mobile',20)->nullable();
            $table->string('password');
            $table->text('address')->nullable();
            $table->integer('country_id')->nullable();
            $table->integer('state_id')->nullable();
            $table->integer('city_id')->nullable();
            $table->string('zipcode',10)->nullable();
            $table->string('api_token')->nullable();
            $table->string('profile',255)->nullable();
            $table->dateTime('last_login')->nullable();
            $table->tinyInteger('status')->comment('0 For InActive, 1 For Active')->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer');
    }
}
