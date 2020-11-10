<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_setting', function (Blueprint $table) {
            $table->bigIncrements('system_setting_id');
            $table->uuid('uuid');
            $table->tinyInteger('is_system_setting')->default(1)->comment('0 For No, 1 For Yes');
            $table->string('display_name',191)->nullable();
            $table->tinyInteger('type')->default(1)->comment('1 For String, 2 For Integer, 3 For Float, 4 For Boolean, 5 For Color');
            $table->string('namespace',100);
            $table->string('key',100);
            $table->string('value',255);
            $table->softDeletes();
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
        Schema::dropIfExists('system_setting');
    }
}
