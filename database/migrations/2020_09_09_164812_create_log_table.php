<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log', function (Blueprint $table) {
            $table->bigIncrements('log_id');
            $table->tinyInteger('type')->default(1)->comment('1 for admin,2 for web,3 for app');
            $table->string('file_name')->nullable();
            $table->string('function')->nullable();
            $table->text('details')->nullable();
            $table->integer('created_by');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log');
    }
}
