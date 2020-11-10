<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tax', function (Blueprint $table) {
            $table->bigIncrements('tax_id');
            $table->uuid('uuid');
            $table->string('code');
            $table->text('description')->nullable();
            $table->decimal('rate', 10, 2);
            $table->tinyInteger('status')->default(1)->comment('0 For disabled, 1 For enabled');
            $table->tinyInteger('is_fixed')->comment('0 For NOT Fixed, 1 For Fixed')->default(0);
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
        Schema::dropIfExists('tax');
    }
}
