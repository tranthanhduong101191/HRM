<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChamlaisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chamlais', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('chamcong_id');
            $table->float('congcu', 2, 1);
            $table->float('cong', 2, 1);
            $table->text('reason');
            $table->text('file_url');
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
        Schema::dropIfExists('chamlais');
    }
}
