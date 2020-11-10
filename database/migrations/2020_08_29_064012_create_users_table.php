<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('app_id')->nullable();
            $table->uuid('uuid');
            $table->string('name', 255);
            $table->unsignedSmallInteger('role');
            $table->string('username');
            $table->string('password');
            $table->string('country_code', 5);
            $table->string('mobile');
            $table->string('profile', 255);
            $table->tinyInteger('status')->default(1)->comment('1 For Active,0 For Deactive');
            $table->tinyInteger('is_admin')->comment('0 For Not admin,1 For Admin')->default(0);
            $table->string('device_id', 255)->nullable();
            $table->string('device_token', 255)->nullable();
            $table->string('auth_key', 255)->nullable();
            $table->dateTime('last_login')->nullable();
            $table->string('remember_token')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
