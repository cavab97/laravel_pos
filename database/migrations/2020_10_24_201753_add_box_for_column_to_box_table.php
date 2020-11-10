<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBoxForColumnToBoxTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('box', function (Blueprint $table) {
            $table->tinyInteger('box_for')->after('slug')->comment('1 For Wine, 2 For Beer')->default(1);
            $table->double('wine_qty')->after('box_for')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('box', function (Blueprint $table) {
            //
        });
    }
}
