<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLichsuchamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lichsuchams', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->float('congcu',2,1);
            $table->float('congmoi', 2,1);
            $table->text('lydo');
            $table->softDeletes();
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
        Schema::dropIfExists('lichsuchams');
    }
}
