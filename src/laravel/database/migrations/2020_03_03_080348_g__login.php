<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GLogin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('G_Login', function (Blueprint $table) {
            $table->increments('generalId')->index();
            $table->string('email')->unique();
            $table->string('password',60);
            $table->integer('userId')->index()->default(0);
            $table->integer('companyId')->index()->default(0);
            $table->rememberToken();
            $table->tinyInteger('accountStatus')->default(0);
            $table->string('email_token',30)->nullable();
            $table->dateTime('lastLogin')->nullable();
            $table->dateTime('createDay');
            $table->dateTime('updateDay');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('G_Login');
    }
}
