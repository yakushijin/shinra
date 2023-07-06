<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::connection('mysql_seed1')->create('M_Group', function (Blueprint $table) {
        //     $table->increments('groupId')->index();
        //     $table->string('groupName',20);
        //     $table->string('color',7);
        //     $table->string('textColor',7);
        //     $table->string('borderColor',7);
        //     $table->string('groupRemarks',100);
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
        // Schema::connection('mysql_seed1')->dropIfExists('M_Group');
    }
}
