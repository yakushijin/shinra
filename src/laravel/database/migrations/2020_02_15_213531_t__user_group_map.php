<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TUserGroupMap extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::connection('mysql_seed1')->create('T_UserGroupMap', function (Blueprint $table) {
        //     $table->integer('userId');
        //     $table->integer('groupId');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::connection('mysql_seed1')->dropIfExists('T_UserGroupMap');
    }
}
