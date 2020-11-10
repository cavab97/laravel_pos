<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactUsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_us', function (Blueprint $table) {
            $table->bigIncrements('contact_us_id');
            $table->uuid('uuid');
            $table->string('name',40)->nullable();
            $table->string('email',100)->nullable();
            $table->string('mobile',20)->nullable();
            $table->string('subject',200)->nullable();
            $table->text('message')->nullable();
            $table->timestamps();
            $table->integer('created_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact_us');
    }
}
