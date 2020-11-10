<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch', function (Blueprint $table) {
            $table->smallIncrements('branch_id');
            $table->uuid('uuid');
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('contact_no');
            $table->string('email');
            $table->string('contact_person')->nullable();
            $table->time('open_from');
            $table->time('closed_on');
            $table->integer('tax');
            $table->string('branch_banner')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
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
        Schema::dropIfExists('branch');
    }
}
