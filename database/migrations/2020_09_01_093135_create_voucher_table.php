<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoucherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucher', function (Blueprint $table) {
            $table->bigIncrements('voucher_id');
            $table->uuid('uuid');
            $table->string('voucher_name');
            $table->string('voucher_banner');
            $table->string('voucher_code');
            $table->tinyInteger('voucher_discount_type')->default(1)->comment('1 for Fixed , 2 For Percentage');
            $table->float('voucher_discount');
            $table->dateTime('voucher_applicable_from');
            $table->dateTime('voucher_applicable_to');
            $table->longText('voucher_categories')->nullable();
            $table->longText('voucher_products')->nullable();
            $table->tinyInteger('status')->default(1)->comment('0 For InActive, 1 For Active');
            $table->dateTime('updated_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('voucher');
    }
}
