<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldCategoryBranch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('category_branch', function (Blueprint $table) {
            $table->tinyInteger('status')->after('display_order')->default(1)->comment('1 For Active,0 For De-active	2 For delete');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('category_branch', function (Blueprint $table) {
        });
    }
}
