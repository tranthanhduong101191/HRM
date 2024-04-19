<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSapLichSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sap_lich_setting', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ca');
            $table->string('vao1');
            $table->string('ra1');
            $table->string('vao2');
            $table->string('ra2');
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
        Schema::dropIfExists('sap_lich_setting');
    }
}
