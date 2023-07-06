<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GCompany extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('G_Company', function (Blueprint $table) {
            $table->increments('companyId')->index();
            $table->string('companyName',20);
            $table->tinyInteger('contractStatus');
            $table->string('dbUser',22);
            $table->string('dbPassword',12);
            $table->string('dbHost',15);
            $table->string('mlHost',15);
            $table->smallInteger('userMaxCount');
            $table->smallInteger('groupMaxCount');
            $table->smallInteger('tabMaxCount');
            $table->integer('categoryMaxCount');
            $table->integer('taskMaxCount');
            $table->decimal('dbDataUse', 8, 4);
            $table->smallInteger('dbDataMaxSize');
            $table->decimal('mlDataUse', 8, 4);
            $table->integer('mlDataAllCount');
            $table->smallInteger('mlDataMaxSize');
            $table->integer('systemUserId');
            $table->smallInteger('categorySaveDay');
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
        Schema::dropIfExists('G_Company');
    }
}
