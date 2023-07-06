<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TPerformance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::connection('mysql_seed1')->create('T_Performance', function (Blueprint $table) {
        //     $table->integer('tabId');
        //     $table->date('performanceDay');
        //     $table->integer('notDoneCount');
        //     $table->integer('doneCount');
        //     $table->tinyInteger('percentage');
        //     $table->timestamp('createDay');
        //     $table->timestamp('updateDay');
        //     $table->primary(['tabId','performanceDay']);
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::connection('mysql_seed1')->dropIfExists('T_Performance');
    }
}
