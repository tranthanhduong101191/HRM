<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChamcongsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chamcongs', function (Blueprint $table) {
            $table->increments('id');
            $table->date('ngay');
            $table->integer('user_id');
            $table->string('ca_name');
            $table->float('congmaycham', 2,1)->nullable();
            $table->float('congnguoicham', 2,1)->nullable();
            $table->integer('nguoicham_id')->nullable();
            $table->json('loi')->nullable();
            $table->json('data_ca')->nullable();
            $table->json('data_cham')->nullable();
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
        Schema::dropIfExists('chamcongs');
    }
}
