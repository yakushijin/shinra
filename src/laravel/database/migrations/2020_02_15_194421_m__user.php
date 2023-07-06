<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::connection('mysql_seed1')->create('M_User', function (Blueprint $table) {
        //     $table->increments('userId')->index();
        //     $table->string('userName',20);
        //     $table->boolean('authority');
        //     $table->string('color',7);
        //     $table->string('textColor',7);
        //     $table->string('borderColor',7);
        //     $table->string('userRemarks',100)->nullable();
        //     $table->boolean('defaultDeadlineFlg');
        //     $table->boolean('deleteMessageFlg');
        //     $table->boolean('doneAutoActiveFlg');
        //     $table->boolean('activeFlg');
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
        // Schema::connection('mysql_seed1')->dropIfExists('M_User');
    }
}
