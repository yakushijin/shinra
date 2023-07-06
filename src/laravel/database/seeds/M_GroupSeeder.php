<?php

use Illuminate\Database\Seeder;

class M_GroupSeeder extends Seeder
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
            'groupName' => 'grouptest',
            'color' => "#7886d3",
            'textColor' => "#FFFFFF",
            'borderColor' => "#7886d3",
            'groupRemarks' => '',
            'activeFlg' => 0,
            'archiveDay' => null,
            'createUser' => 0,
            'updateUser' => 0,
            'createDay' => $now,
            'updateDay' => $now
        ];
        DB::connection('mysql_seed1')->table('M_Group')->insert($data);

    }
}
