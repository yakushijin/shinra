<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TTask extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::connection('mysql_seed1')->create('T_Task', function (Blueprint $table) {
        //     $table->increments('taskId')->index();
        //     $table->string('taskName',20);
        //     $table->integer('categoryId');
        //     $table->integer('tabId');
        //     $table->integer('userId');
        //     $table->integer('groupId');
        //     $table->string('taskRemarks',100)->nullable();
        //     $table->tinyInteger('taskNotstarted');
        //     $table->tinyInteger('taskWorking');
        //     $table->tinyInteger('taskWaiting');
        //     $table->tinyInteger('taskDone');
        //     $table->date('taskDeadline')->nullable();
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
        // Schema::connection('mysql_seed1')->dropIfExists('T_Task');
    }
}
