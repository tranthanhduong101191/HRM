<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTongketluongTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tongketluong', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('month');
            $table->double('congthucte');
            $table->double('congchuan');
            $table->double('thuong');
            $table->double('phat');
            $table->double('tongluongnhan');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tongketluong');
    }
}
