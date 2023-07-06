<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::connection('mysql_seed1')->create('T_Category', function (Blueprint $table) {
        //     $table->increments('categoryId')->index();
        //     $table->string('categoryName',20);
        //     $table->integer('tabId');
        //     $table->integer('userId');
        //     $table->integer('groupId');
        //     $table->string('categoryRemarks',100)->nullable();
        //     $table->tinyInteger('categoryNotstarted');
        //     $table->tinyInteger('categoryWorking');
        //     $table->tinyInteger('categoryWaiting');
        //     $table->tinyInteger('categoryDone');
        //     $table->date('categoryDeadline')->nullable();
        //     $table->boolean('learnedFlg');
        //     $table->boolean('archiveFlg');
        //     $table->timestamp('notstartedDay')->nullable();
        //     $table->timestamp('workingDay')->nullable();
        //     $table->timestamp('waitingDay')->nullable();
        //     $table->timestamp('doneDay')->nullable();
        //     $table->timestamp('archiveDay')->nullable();
        //     $table->integer('createUser');
        //     $table->integer('updateUser');
        //     $table->timestamp('createDay');
        //     $table->timestamp('updateDay');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::connection('mysql_seed1')->dropIfExists('T_Category');
    }
}
