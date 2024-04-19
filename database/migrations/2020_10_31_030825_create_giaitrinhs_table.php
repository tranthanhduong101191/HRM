<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGiaitrinhsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('giaitrinhs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('chamcong_id');
            $table->text('content');            
            $table->text('phanhoi');
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
        Schema::dropIfExists('giaitrinhs');
    }
}
