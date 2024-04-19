<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuacongsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suacongs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('chamcong_id');
            $table->float('congcu', 2, 1);
            $table->float('congmoi', 2, 1);
            $table->text('reason');
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
        Schema::dropIfExists('suacongs');
    }
}
