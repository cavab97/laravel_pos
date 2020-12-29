<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpdateDiscountColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (Schema::hasColumn('cart', 'discount'))
        {
            Schema::table('cart', function (Blueprint $table) {
                $table->dropColumn(['discount']);
            });
        }
        if (Schema::hasColumn('cart', 'discount_type'))
        {
            Schema::table('cart', function (Blueprint $table) {
                $table->dropColumn(['discount_type']);
            });
        }
        if (Schema::hasColumn('cart', 'discount_amount'))
        {
            Schema::table('cart', function (Blueprint $table) {
                $table->dropColumn(['discount_amount']);
            });
        }
        if (Schema::hasColumn('cart', 'discount_remark'))
        {
            Schema::table('cart', function (Blueprint $table) {
                $table->dropColumn(['discount_remark']);
            });
        }
        if (Schema::hasColumn('cart_detail', 'discount'))
        {
            Schema::table('cart_detail', function (Blueprint $table) {
                $table->dropColumn(['discount']);
            });
        }
        if (Schema::hasColumn('cart_detail', 'discount_type'))
        {
            Schema::table('cart_detail', function (Blueprint $table) {
                $table->dropColumn(['discount_type']);
            });
        }
        if (Schema::hasColumn('cart_detail', 'discount_remark'))
        {
            Schema::table('cart_detail', function (Blueprint $table) {
                $table->dropColumn(['discount_remark']);
            });
        }
        if (Schema::hasColumn('cart_detail', 'discount_amount'))
        {
            Schema::table('cart_detail', function (Blueprint $table) {
                $table->dropColumn(['discount_amount']);
            });
        }
        Schema::table('cart', function (Blueprint $table) {
            $table->tinyInteger('discount_type')->after('service_charge')->default(0)->comment('0 for no discount, 1 for Percentage , 2 for Amount');
            $table->double('discount_amount', 15, 2)->after('discount_type')->default(0);
            $table->text('discount_remark')->after('discount_amount')->nullable();
        });
        Schema::table('cart_detail', function (Blueprint $table) {
            $table->tinyInteger('discount_type')->after('remark')->default(0)->comment('0 for no discount, 1 for Percentage , 2 for Amount');
            $table->double('discount_amount', 15, 2)->after('discount_type')->default(0);
            $table->text('discount_remark')->after('discount_amount')->nullable();
        });
        Schema::table('order', function (Blueprint $table) {
            $table->tinyInteger('discount_type')->after('service_charge')->default(0)->comment('0 for no discount, 1 for Percentage , 2 for Amount');
            $table->double('discount_amount', 15, 2)->after('discount_type')->default(0);
            $table->text('discount_remark')->after('discount_amount')->nullable();
        });
        Schema::table('order_detail', function (Blueprint $table) {
            $table->tinyInteger('discount_type')->after('product_detail')->default(0)->comment('0 for no discount, 1 for Percentage , 2 for Amount');
            $table->double('discount_amount', 15, 2)->after('discount_type')->default(0);
            $table->text('discount_remark')->after('discount_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        if (Schema::hasColumn('cart', 'discount'))
        {
            Schema::table('cart', function (Blueprint $table) {
                $table->dropColumn(['discount']);
            });
        }
        if (Schema::hasColumn('cart', 'discount_type'))
        {
            Schema::table('cart', function (Blueprint $table) {
                $table->dropColumn(['discount_type']);
            });
        }
        if (Schema::hasColumn('cart', 'discount_amount'))
        {
            Schema::table('cart', function (Blueprint $table) {
                $table->dropColumn(['discount_amount']);
            });
        }
        if (Schema::hasColumn('cart', 'discount_remark'))
        {
            Schema::table('cart', function (Blueprint $table) {
                $table->dropColumn(['discount_remark']);
            });
        }
        if (Schema::hasColumn('cart_detail', 'discount'))
        {
            Schema::table('cart_detail', function (Blueprint $table) {
                $table->dropColumn(['discount']);
            });
        }
        if (Schema::hasColumn('cart_detail', 'discount_type'))
        {
            Schema::table('cart_detail', function (Blueprint $table) {
                $table->dropColumn(['discount_type']);
            });
        }
        if (Schema::hasColumn('cart_detail', 'discount_remark'))
        {
            Schema::table('cart_detail', function (Blueprint $table) {
                $table->dropColumn(['discount_remark']);
            });
        }
        if (Schema::hasColumn('cart_detail', 'discount_amount'))
        {
            Schema::table('cart_detail', function (Blueprint $table) {
                $table->dropColumn(['discount_amount']);
            });
        }
    }
}
