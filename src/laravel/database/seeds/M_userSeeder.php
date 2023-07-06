<?php

use Illuminate\Database\Seeder;

class M_UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = \Carbon\Carbon::now();
        $data = [
            'userName' => 'test',
            'authority' => 0,
            'activeFlg' => 0,
            'archiveDay' => null,
            'userRemarks' => "",
            'color' => "#7886d3",
            'textColor' => "#FFFFFF",
            'borderColor' => "#7886d3",
            'defaultDeadlineFlg' => 0,
            'deleteMessageFlg' => 0,
            'doneAutoActiveFlg' => 0,
            'createUser' => 0,
            'updateUser' => 0,
            'createDay' => $now,
            'updateDay' => $now
        ];
        DB::connection('mysql_seed1')->table('M_User')->insert($data);
    }
}
