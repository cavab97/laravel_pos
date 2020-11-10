<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('localID');
            $table->integer('employee_id');
            $table->integer('branch_id');
            $table->integer('terminal_id');
            $table->integer('in_out');
            $table->dateTime('in_out_datetime')->nullable();
            $table->integer('created_by')->nullable();
            $table->dateTime('created_date')->nullable();
            $table->dateTime('updated_date')->nullable();
            $table->integer('server_id');
            $table->integer('sync');
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
        Schema::dropIfExists('attendance');
    }
}
