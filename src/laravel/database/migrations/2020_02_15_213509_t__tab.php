<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TTab extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::connection('mysql_seed1')->create('T_Tab', function (Blueprint $table) {
        //     $table->increments('tabId')->index();
        //     $table->string('tabName',10)->nullable();
        //     $table->tinyInteger('type');
        //     $table->string('color',7);
        //     $table->string('textColor',7);
        //     $table->string('borderColor',7);
        //     $table->string('tabRemarks',100);
        //     $table->integer('userOrGroupId');
        //     $table->boolean('groupFlg');
        //     $table->date('tabDeadline')->nullable();
        //     $table->boolean('archiveFlg');
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
        // Schema::connection('mysql_seed1')->dropIfExists('T_Tab');
    }
}
