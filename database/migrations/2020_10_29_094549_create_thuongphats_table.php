<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThuongphatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('thuongphats', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('amount');
            $table->string('reason');
            $table->date('issued_at');
            $table->integer('issue_user_id');
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
        Schema::dropIfExists('thuongphats');
    }
}
