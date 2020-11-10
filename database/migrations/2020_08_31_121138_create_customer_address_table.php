<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_address', function (Blueprint $table) {
            $table->bigIncrements('address_id');
            $table->bigInteger('app_id')->nullable();
            $table->uuid('uuid');
            $table->unsignedBigInteger('user_id');
            $table->text('address_line1');
            $table->text('address_line2')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->tinyInteger('is_default')->default(1)->comment('1 For Yes, 0 For No');
            $table->tinyInteger('status')->default(1)->comment('1 For Active, 2 For InActive');
            $table->softDeletes();
            $table->dateTime('updated_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_address');
    }
}
