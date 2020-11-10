<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role', function (Blueprint $table) {
            $table->smallIncrements('role_id');
            $table->uuid('uuid');
            $table->string('role_name', '40');
            $table->tinyInteger('role_status')->default(1)->comment('1 For active,0 for deactive');
            $table->dateTime('role_updated_at')->nullable();
            $table->integer('role_updated_by')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role');
    }
}
