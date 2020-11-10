<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnToOrderPaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_payment', function (Blueprint $table) {
            $table->tinyInteger('is_split')->after('app_id')->default(0)->comment('0 For No, 1 For Yes');
            $table->string('remark')->after('is_split')->nullable();
            $table->string('last_digits')->after('remark')->nullable();
            $table->string('approval_code')->after('last_digits')->nullable();
            $table->string('reference_number')->after('approval_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_payment', function (Blueprint $table) {
            //
        });
    }
}
