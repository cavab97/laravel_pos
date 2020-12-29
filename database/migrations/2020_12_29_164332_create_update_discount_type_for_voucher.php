<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpdateDiscountTypeForVoucher extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('voucher', 'voucher_discount_type')) {
            Schema::table('voucher', function (Blueprint $table) {
                $table->dropColumn(['voucher_discount_type']);
            });
        }
        Schema::table('voucher', function (Blueprint $table) {
            $table->tinyInteger('voucher_discount_type')->default(1)->comment('1 for Percentage, 2 for Amount')->after('voucher_products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('voucher', function (Blueprint $table) {
            if (Schema::hasColumn('voucher', 'voucher_discount_type')) {
                Schema::table('voucher', function (Blueprint $table) {
                    $table->dropColumn(['voucher_discount_type']);
                });
            }
        });
    }
}
