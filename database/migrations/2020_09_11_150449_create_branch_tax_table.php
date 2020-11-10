<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchTaxTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_tax', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('tax_id');
            $table->unsignedBigInteger('branch_id');
            $table->decimal('rate', 10, 2);
            $table->tinyInteger('status')->default(1)->comment('0 For disabled, 1 For enabled,2 For Deleted');
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
        Schema::dropIfExists('branch_tax');
    }
}
